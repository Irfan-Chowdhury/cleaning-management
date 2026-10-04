<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\QuestionOption;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingCompletedNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingProcessingNotification;
use App\Notifications\NewBookingPendingNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function __construct(
        protected BookingSessionService $bookingSessionService,
        protected ImageService $imageService
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
        if (!empty($step1['answers']) && is_array($step1['answers'])) {
            foreach ($step1['answers'] as $questionId => $val) {
                if ($val === null || $val === '') {
                    continue;
                }
                $questionTitle = $step1['question_titles'][$questionId] ?? "Question #{$questionId}";
                $answerStr = '';

                if (is_array($val)) {
                    $optionLabels = [];
                    foreach ($val as $subVal) {
                        if (is_numeric($subVal)) {
                            $opt = QuestionOption::find($subVal);
                            if ($opt) {
                                $optionLabels[] = $opt->label;
                            }
                        } else {
                            $optionLabels[] = $subVal;
                        }
                    }
                    $answerStr = implode(', ', $optionLabels);
                } elseif (is_numeric($val)) {
                    $opt = QuestionOption::find($val);
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

        // Process & store booking images if provided in step 1
        if (!empty($step1['images']) && is_array($step1['images'])) {
            $this->imageService->persistBookingImages($booking->id, $step1['images']);
        }

        // Create initial Payment record with status 'pending'
        try {
            Payment::create([
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
            $payment = Payment::firstOrNew(['booking_id' => $booking->id]);
            $payment->user_id = $booking->user_id;
            $payment->amount = isset($data['amount']) ? (float) $data['amount'] : (float) $booking->total_amount;
            $payment->payment_status = $data['payment_status'] ?? ($booking->payment_status ?? 'pending');
            $payment->payment_method = $data['payment_method'] ?? ($booking->payment_method ?? 'pending');
            $payment->save();
        } catch (\Throwable $e) {
            Log::error('Failed to sync payment record: ' . $e->getMessage());
        }

        // Notify customer on status changes (Approved, Processing, Completed)
        $customer = $booking->user ?? User::where('email', $booking->customer_email)->first();

        if ($customer) {
            try {
                if ($oldStatus !== BookingStatus::APPROVED->value && $newStatusEnum === BookingStatus::APPROVED) {
                    $customer->notify(new BookingApprovedNotification($booking));
                } elseif ($oldStatus !== BookingStatus::PROCESSING->value && $newStatusEnum === BookingStatus::PROCESSING) {
                    $customer->notify(new BookingProcessingNotification($booking));
                } elseif ($oldStatus !== BookingStatus::COMPLETED->value && $newStatusEnum === BookingStatus::COMPLETED) {
                    $customer->notify(new BookingCompletedNotification($booking));
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send customer booking status update notification: ' . $e->getMessage());
            }
        }

        return $booking;
    }

    /**
     * Confirm an approved booking by Customer, update status to confirmed, and notify admins.
     */
    public function confirmBookingByCustomer(Booking $booking, User $user, float $walletAmount = 0.0): Booking
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

        $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);

        // Process wallet deduction if requested and customer has sufficient balance
        if ($walletAmount > 0) {
            $dbTx = WalletTransaction::where('user_id', $user->id)->get();
            $availableBalance = max(0, (float) $dbTx->where('type', 'credit')->sum('amount') - (float) $dbTx->where('type', 'debit')->sum('amount'));

            $settings = Cache::rememberForever('app_settings', function () {
                return Setting::latest()->first();
            });

            $minBookingAmount = (float) ($settings?->minimum_booking_amount ?? 0);
            $maxWalletUsage = (float) ($settings?->max_wallet_usage ?? 0);

            if ($subtotal >= $minBookingAmount && $availableBalance >= $walletAmount) {
                if ($maxWalletUsage > 0 && $walletAmount > $maxWalletUsage) {
                    $walletAmount = $maxWalletUsage;
                }

                $walletAmount = min($walletAmount, $subtotal);
                $newTotal = max(0, $subtotal - $walletAmount);

                $booking->subtotal = $subtotal;
                $booking->credit_used = $walletAmount;
                $booking->discount_amount = $walletAmount;
                $booking->total_amount = $newTotal;

                // Create WalletTransaction debit entry
                try {
                    WalletTransaction::create([
                        'user_id'     => $user->id,
                        'booking_id'  => $booking->id,
                        'type'        => 'debit',
                        'amount'      => $walletAmount,
                        'source'      => 'booking_payment',
                        'description' => 'Wallet credit used for Booking #BK-' . sprintf('%03d', $booking->id),
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Failed to record wallet transaction debit: ' . $e->getMessage());
                }
            }
        }

        $booking->status = BookingStatus::CONFIRMED;
        $booking->save();

        // Sync or update payment record status if present
        try {
            $payment = Payment::firstOrNew(['booking_id' => $booking->id]);
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

    /* ========================================================================= */
    /* CUSTOMER SECTION PART                                                     */
    /* ========================================================================= */

    /**
     * Get list of formatted bookings for customer dashboard / my-bookings.
     */
    public function getCustomerBookings(int $userId): \Illuminate\Support\Collection
    {
        $dbBookings = Booking::with(['service', 'payment', 'images'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        if ($dbBookings->isEmpty()) {
            return collect();
        }

        return $dbBookings->map(function ($b) {
            $statusVal = $b->status instanceof BookingStatus ? $b->status->value : (string) $b->status;
            $paymentStatusRaw = strtolower($b->payment?->payment_status ?? $b->payment_status ?? 'pending');
            $paymentStatus = ucfirst($paymentStatusRaw);
            $paymentMethod = $b->payment?->payment_method ?? $b->payment_method ?? 'Pending Payment';

            $questionnaires = [];
            if (!empty($b->answers) && is_array($b->answers)) {
                $questionnaires = $b->answers;
            }
            if (!empty($b->service_notes)) {
                $questionnaires[] = [
                    'question' => 'Service Notes',
                    'answer'   => $b->service_notes,
                ];
            }
            if (!empty($b->special_instructions)) {
                $questionnaires[] = [
                    'question' => 'Special Instructions',
                    'answer'   => $b->special_instructions,
                ];
            }

            return (object)[
                'id'                   => $b->id,
                'booking_id'           => 'BK-' . sprintf('%03d', $b->id),
                'service_name'         => $b->service?->name ?? 'Cleaning Service',
                'date'                 => $b->booking_date ?? $b->created_at->format('Y-m-d'),
                'time'                 => $b->start_time ? \Carbon\Carbon::parse($b->start_time)->format('g:i A') : '09:00 AM',
                'amount'               => (float) $b->total_amount,
                'status'               => ucfirst($statusVal),
                'status_raw'           => strtolower($statusVal),
                'payment_status'       => $paymentStatus,
                'payment_method'       => $paymentMethod === 'pending' ? 'Pending Payment' : $paymentMethod,
                'paid_amount'          => $paymentStatusRaw === 'paid' ? (float) $b->total_amount : 0.00,
                'wallet_used'          => (float) $b->credit_used,
                'credit_used'          => (float) $b->credit_used,
                'discount_amount'      => (float) $b->discount_amount,
                'referal_code'         => $b->referal_code,
                'promo_code'           => $b->promo_code,
                'images'               => $b->images,
                'cancellation_eligible'=> (strtolower($statusVal) === 'approved'),
                'questionnaires'       => $questionnaires,
            ];
        });
    }

    /**
     * Get dedicated booking details model for customer /my-bookings/{id}.
     */
    public function getCustomerBookingDetails(int $bookingId, int $userId): Booking
    {
        return Booking::with(['service', 'payment', 'images'])
            ->where('id', $bookingId)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Cancel an approved booking by customer and notify admin.
     */
    public function cancelCustomerBooking(Booking $booking, int $userId): array
    {
        if ((int) $booking->user_id !== (int) $userId) {
            return [
                'success' => false,
                'code'    => 403,
                'message' => 'Unauthorized action.',
            ];
        }

        $statusVal = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;

        if (strtolower($statusVal) !== 'approved') {
            return [
                'success' => false,
                'code'    => 422,
                'message' => 'Only approved bookings can be cancelled schedule.',
            ];
        }

        $booking->status = BookingStatus::CANCELLED;
        $booking->save();

        // Send app notification to all Admins
        try {
            $admins = User::where('role', 1)->get();
            foreach ($admins as $admin) {
                $admin->notify(new BookingCancelledNotification($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send admin booking cancellation notification: ' . $e->getMessage());
        }

        return [
            'success'    => true,
            'code'       => 200,
            'message'    => 'Booking schedule has been cancelled successfully.',
            'status'     => 'Cancelled',
            'status_raw' => 'cancelled',
        ];
    }
}
