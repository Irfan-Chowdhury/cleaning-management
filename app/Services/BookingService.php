<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\NewBookingPendingNotification;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function __construct(
        protected BookingSessionService $bookingSessionService
    ) {
    }

    /**
     * Create a new booking from wizard step submission, notify admins, and return the booking.
     */
    public function createBookingFromWizard(User $user, array $step1, array $step2, array $step3, array $offer = []): Booking
    {
        $subtotal = 0.00;
        $discountAmount = (float) ($offer['discount_amount'] ?? 0.00);
        $totalAmount = 0.00;

        // Process questionnaire questions & answers into JSON format
        $answers = [];
        $rawQuestions = $step1['questions'] ?? [];

        if (!empty($rawQuestions) && is_array($rawQuestions)) {
            foreach ($rawQuestions as $key => $val) {
                if (is_array($val) && isset($val['question']) && isset($val['answer'])) {
                    $answers[] = [
                        'question' => (string) $val['question'],
                        'answer'   => (string) $val['answer'],
                    ];
                } else {
                    $questionModel = \App\Models\ServiceQuestion::find($key);
                    $questionTitle = $questionModel ? $questionModel->title : ("Question #" . $key);

                    if (is_array($val)) {
                        $optionLabels = [];
                        foreach ($val as $subVal) {
                            if (is_numeric($subVal)) {
                                $opt = \App\Models\QuestionOption::find($subVal);
                                $optionLabels[] = $opt ? $opt->label : $subVal;
                            } else {
                                $optionLabels[] = $subVal;
                            }
                        }
                        $answerStr = implode(', ', $optionLabels);
                    } elseif (is_numeric($val)) {
                        $opt = \App\Models\QuestionOption::find($val);
                        $answerStr = $opt ? $opt->label : (string) $val;
                    } else {
                        $answerStr = (string) $val;
                    }

                    if (!empty($answerStr)) {
                        $answers[] = [
                            'question' => $questionTitle,
                            'answer'   => $answerStr,
                        ];
                    }
                }
            }
        }

        $booking = Booking::create([
            'user_id'              => $user->id,
            'service_id'           => $step1['service_id'] ?? 1,
            'answers'              => $answers,
            'frequency'            => $step2['frequency'] ?? 'one_time',
            'booking_date'         => $step2['booking_date'] ?? null,
            'start_time'           => $step2['start_time'] ?? null,
            'end_time'             => $step2['end_time'] ?? null,
            'customer_name'        => $step3['customer_name'] ?? trim($user->first_name . ' ' . ($user->last_name ?? '')),
            'customer_email'       => $step3['customer_email'] ?? $user->email,
            'customer_phone'       => $step3['customer_phone'] ?? $user->phone,
            'customer_address'     => $step3['customer_address'] ?? $user->address,
            'unit_suite_floor'     => $step3['unit_suite_floor'] ?? null,
            'suburb'               => $step3['suburb'] ?? null,
            'postcode'             => $step3['postcode'] ?? null,
            'special_instructions' => $step3['special_instructions'] ?? null,
            'service_notes'        => $step1['service_notes'] ?? null,
            'status'               => BookingStatus::PENDING,
            'payment_status'       => 'pending',
            'payment_method'       => 'pending',
            'subtotal'             => $subtotal,
            'discount_amount'      => $discountAmount,
            'credit_used'          => ($offer['type'] ?? '') === 'wallet' ? $discountAmount : 0.00,
            'total_amount'         => $totalAmount,
            'referal_code'         => ($offer['type'] ?? '') === 'referral' ? ($offer['code'] ?? null) : null,
            'promo_code'           => ($offer['type'] ?? '') === 'promo' ? ($offer['code'] ?? null) : null,
        ]);

        // Create initial Payment record with status 'pending'
        try {
            \App\Models\Payment::create([
                'booking_id'     => $booking->id,
                'user_id'        => $user->id,
                'amount'         => $totalAmount,
                'payment_method' => 'pending',
                'payment_status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create payment record: ' . $e->getMessage());
        }

        // Send Notification to all Admins
        try {
            $admins = User::where('role', 1)->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewBookingPendingNotification($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send admin booking notification: ' . $e->getMessage());
        }

        return $booking;
    }

    /**
     * Update booking status & details by Admin, record audit log, and send customer approval notification.
     */
    public function updateBookingByAdmin(Booking $booking, array $data, User $adminUser): Booking
    {
        $oldStatus = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;

        $newStatusValue = $data['status'] instanceof BookingStatus ? $data['status']->value : (string) $data['status'];
        $newStatusEnum = BookingStatus::from($newStatusValue);

        $updateData = [
            'status' => $newStatusEnum,
        ];

        if (!empty($data['service_id'])) {
            $updateData['service_id'] = $data['service_id'];
        }

        if (isset($data['amount'])) {
            $updateData['total_amount'] = (float) $data['amount'];
        }

        if (!empty($data['date'])) {
            $updateData['booking_date'] = $data['date'];
        }

        if (!empty($data['slot'])) {
            $updateData['start_time'] = $data['slot'];
        }

        if (!empty($data['payment_status'])) {
            $updateData['payment_status'] = strtolower($data['payment_status']);
        }

        if (!empty($data['payment_method'])) {
            $updateData['payment_method'] = $data['payment_method'];
        }

        // Auditable trait automatically logs this update in audit_logs
        $booking->update($updateData);

        // Sync or create Payment model record
        try {
            $payment = \App\Models\Payment::firstOrNew(['booking_id' => $booking->id]);
            $payment->user_id = $booking->user_id;
            $payment->amount = isset($data['amount']) ? (float) $data['amount'] : (float) $booking->total_amount;
            $payment->payment_status = $data['payment_status'] ?? ($booking->payment_status ?? 'pending');
            $payment->payment_method = $data['payment_method'] ?? ($booking->payment_method ?? 'pending');
            $payment->save();
        } catch (\Throwable $e) {
            Log::error('Failed to sync payment record: ' . $e->getMessage());
        }

        // If status changed to Approved, notify customer
        if ($oldStatus !== BookingStatus::APPROVED->value && $newStatusEnum === BookingStatus::APPROVED) {
            $customer = $booking->user ?? User::where('email', $booking->customer_email)->first();

            if ($customer) {
                try {
                    $customer->notify(new BookingApprovedNotification($booking));
                } catch (\Throwable $e) {
                    Log::error('Failed to send customer approval notification: ' . $e->getMessage());
                }
            }
        }

        return $booking;
    }

    /**
     * Confirm an approved booking by Customer, update status to confirmed, and notify admins.
     */
    public function confirmBookingByCustomer(Booking $booking, User $user): Booking
    {
        $statusValue = $booking->status instanceof BookingStatus
            ? $booking->status->value
            : strtolower((string) $booking->status);

        if ($statusValue !== BookingStatus::APPROVED->value) {
            throw new \InvalidArgumentException('Only approved bookings can be confirmed.');
        }

        // Check ownership unless admin
        if ((int) $user->role !== 1 && $booking->user_id != $user->id) {
            throw new \UnauthorizedException('You are not authorized to confirm this booking.');
        }

        $booking->update([
            'status' => BookingStatus::CONFIRMED,
        ]);

        // Sync or update payment record status if present
        try {
            $payment = \App\Models\Payment::firstOrNew(['booking_id' => $booking->id]);
            $payment->user_id = $booking->user_id;
            $payment->amount = (float) $booking->total_amount;
            if (empty($payment->payment_status)) {
                $payment->payment_status = 'pending';
            }
            if (empty($payment->payment_method)) {
                $payment->payment_method = 'pending';
            }
            $payment->save();
        } catch (\Throwable $e) {
            Log::error('Failed to sync payment record on confirmation: ' . $e->getMessage());
        }

        // Notify all admins of booking confirmation
        try {
            $admins = User::where('role', 1)->get();
            foreach ($admins as $admin) {
                $admin->notify(new BookingConfirmedNotification($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send admin confirmation notification: ' . $e->getMessage());
        }

        return $booking;
    }
}
