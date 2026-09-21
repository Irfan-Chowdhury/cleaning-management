<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyReferralRequest;
use App\Http\Requests\BookingConfirmRequest;
use App\Http\Requests\BookingStep1Request;
use App\Http\Requests\BookingStep2Request;
use App\Http\Requests\BookingStep3Request;
use App\Http\Requests\RemoveReferralRequest;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Promotion;
use App\Models\ScheduleSlot;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WeeklySchedule;
use App\Services\BookingService;
use App\Services\BookingSessionService;
use App\Services\PromotionDiscountService;
use App\Services\ReferralService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingServiceController extends Controller
{
    protected BookingSessionService $bookingSessionService;
    protected BookingService $bookingService;

    public function __construct(
        BookingSessionService $bookingSessionService,
        BookingService $bookingService
    ) {
        $this->bookingSessionService = $bookingSessionService;
        $this->bookingService = $bookingService;
    }

    public function create()
    {
        $services = Service::where('status', 'active')->orderBy('name')->get();
        $step1Data = $this->bookingSessionService->getStep1Data();

        return view('pages.booking-service.create', compact('services', 'step1Data'));
    }

    public function storeStep1(BookingStep1Request $request): RedirectResponse
    {
        $this->bookingSessionService->saveStep1($request->validated());

        return redirect()->route('booking-service.date-time');
    }

    public function questionnaire(Service $service): JsonResponse
    {
        $service->load('serviceQuestions.questionOptions');

        return response()->json([
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
            ],
            'questions' => $service->serviceQuestions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'title' => $question->title,
                    'field_type' => $question->field_type,
                    'required' => (bool) $question->required,
                    'sort_order' => $question->sort_order,
                    'options' => $question->questionOptions->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'label' => $option->label,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function dateTime()
    {
        $step1Data = $this->bookingSessionService->getStep1Data();
        if (empty($step1Data) || empty($step1Data['service_id'])) {
            return redirect()->route('booking-service.create')
                ->with('warning', 'Please select a service and complete Step 1 first.');
        }

        $holidays = Holiday::where('is_active', true)
            ->get(['title', 'start_date', 'end_date'])
            ->map(function ($h) {
                return [
                    'title' => $h->title,
                    'start_date' => Carbon::parse($h->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($h->end_date)->format('Y-m-d'),
                ];
            });

        $step2Data = $this->bookingSessionService->getStep2Data();

        return view('pages.booking-service.date-time', compact('holidays', 'step2Data'));
    }

    public function slotsForDate(Request $request): JsonResponse
    {
        try {
            $dateStr = $request->query('date', Carbon::today()->format('Y-m-d'));

            try {
                $date = Carbon::parse($dateStr);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format.'], 422);
            }

            $formattedDate = $date->format('Y-m-d');
            $dayName = $date->format('l'); // e.g. 'Monday'

            // Check if date is a holiday
            try {
                $holiday = Holiday::where('is_active', true)
                    ->where('start_date', '<=', $formattedDate)
                    ->where('end_date', '>=', $formattedDate)
                    ->first();

                if ($holiday) {
                    return response()->json([
                        'date'          => $formattedDate,
                        'day_of_week'   => $dayName,
                        'is_holiday'    => true,
                        'holiday_title' => $holiday->title,
                        'is_day_active' => false,
                        'slots'         => [],
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore if holidays table missing
            }

            // Check if day of week is active in weekly_schedule
            $weeklySchedule = null;
            try {
                $weeklySchedule = WeeklySchedule::where('day_of_week', $dayName)->first();
            } catch (\Throwable $e) {
            }

            // Fetch slots for this day from DB
            $slots = collect();
            if ($weeklySchedule && $weeklySchedule->is_active) {
                try {
                    $slots = ScheduleSlot::where('weekly_schedule_id', $weeklySchedule->id)
                        ->orderBy('sort_order')
                        ->orderBy('start_time')
                        ->get();
                } catch (\Throwable $e) {
                }
            }

            // Fallback: If no slots exist in DB, provide default 7 AM - 6 PM standard slots
            if ($slots->isEmpty() && (!$weeklySchedule || $weeklySchedule->is_active)) {
                $defaultTimes = [
                    '07:00:00' => '08:00:00',
                    '08:00:00' => '09:00:00',
                    '09:00:00' => '10:00:00',
                    '10:00:00' => '11:00:00',
                    '11:00:00' => '12:00:00',
                    '12:00:00' => '13:00:00',
                    '13:00:00' => '14:00:00',
                    '14:00:00' => '15:00:00',
                    '15:00:00' => '16:00:00',
                    '16:00:00' => '17:00:00',
                    '17:00:00' => '18:00:00',
                    '18:00:00' => '19:00:00',
                ];

                $slots = collect();
                $sort = 1;
                foreach ($defaultTimes as $start => $end) {
                    $slots->push((object)[
                        'id'         => $sort,
                        'start_time' => $start,
                        'end_time'   => $end,
                    ]);
                    $sort++;
                }
            }

            // Get existing booked start_times for this date
            $bookedTimes = [];
            try {
                $bookedTimes = Booking::where('booking_date', $formattedDate)
                    ->get()
                    ->filter(function ($b) {
                        $st = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : strtolower((string)$b->status);
                        return $st !== 'cancelled';
                    })
                    ->pluck('start_time')
                    ->map(function ($time) {
                        return Carbon::parse($time)->format('H:i');
                    })
                    ->toArray();
            } catch (\Throwable $e) {
            }

            $formattedSlots = $slots->map(function ($slot) use ($bookedTimes) {
                $startTime = Carbon::parse($slot->start_time)->format('H:i');
                $endTime = !empty($slot->end_time) ? Carbon::parse($slot->end_time)->format('H:i') : null;
                $displayTime = Carbon::parse($slot->start_time)->format('g:i A');
                $isBooked = in_array($startTime, $bookedTimes);

                return [
                    'id'           => $slot->id,
                    'start_time'   => Carbon::parse($slot->start_time)->format('H:i:s'),
                    'end_time'     => $endTime ? Carbon::parse($slot->end_time)->format('H:i:s') : null,
                    'display_time' => $displayTime,
                    'is_booked'    => $isBooked,
                ];
            });

            return response()->json([
                'date'          => $formattedDate,
                'day_of_week'   => $dayName,
                'is_holiday'    => false,
                'holiday_title' => null,
                'is_day_active' => true,
                'slots'         => $formattedSlots,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Failed to load time slots.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function storeStep2(BookingStep2Request $request): RedirectResponse
    {
        $step1Data = $this->bookingSessionService->getStep1Data();
        if (empty($step1Data) || empty($step1Data['service_id'])) {
            return redirect()->route('booking-service.create')
                ->with('warning', 'Please complete Step 1 first.');
        }

        $this->bookingSessionService->saveStep2($request->validated());

        return redirect()->route('booking-service.your-details');
    }

    public function yourDetails()
    {
        $step1Data = $this->bookingSessionService->getStep1Data();
        if (empty($step1Data) || empty($step1Data['service_id'])) {
            return redirect()->route('booking-service.create')
                ->with('warning', 'Please complete Step 1 first.');
        }

        $step2Data = $this->bookingSessionService->getStep2Data();
        if (empty($step2Data) || empty($step2Data['booking_date']) || empty($step2Data['start_time'])) {
            return redirect()->route('booking-service.date-time')
                ->with('warning', 'Please select date and time slot in Step 2 first.');
        }

        $user = auth()->user();

        $accountData = [
            'name'     => $user ? trim($user->first_name . ' ' . ($user->last_name ?? '')) : '',
            'email'    => $user->email ?? '',
            'phone'    => $user->phone ?? '',
            'address'  => $user->address ?? '',
            'unit'     => '',
            'suburb'   => '',
            'postcode' => '',
        ];

        $step3Data = $this->bookingSessionService->getStep3Data();

        return view('pages.booking-service.your-details', compact('accountData', 'step3Data'));
    }

    public function storeStep3(BookingStep3Request $request): RedirectResponse
    {
        $step1 = $this->bookingSessionService->getStep1Data();
        if (empty($step1) || empty($step1['service_id'])) {
            return redirect()->route('booking-service.create')
                ->with('warning', 'Please complete Step 1 first.');
        }

        $step2 = $this->bookingSessionService->getStep2Data();
        if (empty($step2) || empty($step2['booking_date']) || empty($step2['start_time'])) {
            return redirect()->route('booking-service.date-time')
                ->with('warning', 'Please select date and time slot in Step 2 first.');
        }

        $validated = $request->validated();
        $this->bookingSessionService->saveStep3($validated);

        $offer = $this->bookingSessionService->getOfferData();
        $user = auth()->user();

        $booking = $this->bookingService->createBookingFromWizard($user, $step1, $step2, $validated, $offer);

        // Clear wizard session after creation
        $this->bookingSessionService->clearSession();

        $targetRoute = (int) $user->role === 1 ? 'bookings.index' : 'customer.bookings.index';

        return redirect()->route($targetRoute)
            ->with('success', "Booking #{$booking->id} submitted! Status is Pending while Admin reviews.");
    }

    public function reviewConfirm(Request $request)
    {
        $bookingId = $request->query('booking') ?? $request->query('booking_id');

        if (!$bookingId) {
            $targetRoute = (int) auth()->user()->role === 1 ? 'bookings.index' : 'customer.bookings.index';
            return redirect()->route($targetRoute)
                ->with('warning', 'Step 4 is only accessible when reviewing an approved booking.');
        }

        $booking = Booking::with('service')->find($bookingId);

        if (!$booking) {
            $targetRoute = (int) auth()->user()->role === 1 ? 'bookings.index' : 'customer.bookings.index';
            return redirect()->route($targetRoute)
                ->with('error', 'Booking not found.');
        }

        // Check ownership unless admin
        if ((int) auth()->user()->role !== 1 && $booking->user_id != auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'You are not authorized to view this booking.');
        }

        $statusValue = $booking->status instanceof \App\Enums\BookingStatus
            ? $booking->status->value
            : strtolower((string) $booking->status);

        if ($statusValue !== 'approved') {
            $targetRoute = (int) auth()->user()->role === 1 ? 'bookings.index' : 'customer.bookings.index';
            return redirect()->route($targetRoute)
                ->with('warning', 'Booking status must be Approved by an Admin before accessing Step 4.');
        }

        $step1Data = [
            'service_id'    => $booking->service_id,
            'service_name'  => $booking->service->name ?? 'Cleaning Service',
            'service_notes' => $booking->service_notes,
        ];

        $step2Data = [
            'booking_date' => $booking->booking_date,
            'start_time'   => $booking->start_time,
            'end_time'     => $booking->end_time,
            'frequency'    => $booking->frequency,
        ];

        $step3Data = [
            'customer_name'        => $booking->customer_name,
            'customer_email'       => $booking->customer_email,
            'customer_phone'       => $booking->customer_phone,
            'customer_address'     => $booking->customer_address,
            'unit_suite_floor'     => $booking->unit_suite_floor,
            'suburb'               => $booking->suburb,
            'postcode'             => $booking->postcode,
            'special_instructions' => $booking->special_instructions,
        ];

        $offerData = [
            'discount_amount' => $booking->discount_amount,
            'credit_used'     => $booking->credit_used,
            'referal_code'    => $booking->referal_code,
            'promo_code'      => $booking->promo_code,
        ];

        $settings = \Illuminate\Support\Facades\Cache::rememberForever('app_settings', function () {
            return \App\Models\Setting::latest()->first();
        });

        $user = auth()->user();
        $dbTransactions = \App\Models\WalletTransaction::where('user_id', $user->id ?? 0)->get();
        $totalCredit = (float) $dbTransactions->where('type', 'credit')->sum('amount');
        $totalDebit = (float) $dbTransactions->where('type', 'debit')->sum('amount');
        $userWalletBalance = max(0, $totalCredit - $totalDebit);

        $latestBooking = $booking;

        return view('pages.booking-service.review-confirm', compact('step1Data', 'step2Data', 'step3Data', 'offerData', 'latestBooking', 'settings', 'userWalletBalance'));
    }

    public function confirmBooking(BookingConfirmRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $bookingId = (int) $validated['booking_id'];
        $walletAmount = (float) ($request->input('wallet_amount') ?? 0);

        $booking = Booking::findOrFail($bookingId);

        $this->bookingService->confirmBookingByCustomer($booking, auth()->user(), $walletAmount);

        // Destroy referral related session data for this user
        session()->forget('booking_wizard.offer');

        $targetRoute = (int) auth()->user()->role === 1 ? 'bookings.index' : 'customer.bookings.index';

        return redirect()->route($targetRoute)
            ->with('success', "Booking #{$booking->id} has been confirmed!");
    }

    public function applyPromo(ApplyReferralRequest $request, ReferralService $referralService, PromotionDiscountService $promotionDiscountService): JsonResponse
    {
        $validated = $request->validated();
        $booking = Booking::find((int) $validated['booking_id']);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 422);
        }

        $code = trim($validated['code']);
        $type = $request->input('type');

        if ($type === 'referral') {
            $result = $referralService->applyCodeToBooking($booking, $code, auth()->user());
        } elseif ($type === 'promo') {
            $result = $promotionDiscountService->applyPromotionToBooking($booking, $code, auth()->user());
        } else {
            $promoExists = \App\Models\Promotion::where('code', \Illuminate\Support\Str::upper($code))->exists();
            if ($promoExists) {
                $result = $promotionDiscountService->applyPromotionToBooking($booking, $code, auth()->user());
            } else {
                $result = $referralService->applyCodeToBooking($booking, $code, auth()->user());
            }
        }

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    public function removePromo(RemoveReferralRequest $request, ReferralService $referralService, PromotionDiscountService $promotionDiscountService): JsonResponse
    {
        $validated = $request->validated();
        $booking = Booking::find((int) $validated['booking_id']);

        if ($booking && !empty($booking->promo_code)) {
            $result = $promotionDiscountService->removePromotionFromBooking($booking);
        } else {
            $result = $referralService->removeCodeFromBooking($booking);
        }

        return response()->json($result);
    }
}
