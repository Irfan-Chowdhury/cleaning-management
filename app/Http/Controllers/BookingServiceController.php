<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStep1Request;
use App\Http\Requests\BookingStep2Request;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\ScheduleSlot;
use App\Models\Service;
use App\Models\WeeklySchedule;
use App\Services\BookingSessionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingServiceController extends Controller
{
    protected BookingSessionService $bookingSessionService;

    public function __construct(BookingSessionService $bookingSessionService)
    {
        $this->bookingSessionService = $bookingSessionService;
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
        $dateStr = $request->query('date', Carbon::today()->format('Y-m-d'));

        try {
            $date = Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 422);
        }

        $formattedDate = $date->format('Y-m-d');
        $dayName = $date->format('l'); // e.g. 'Monday'

        // Check if date is a holiday
        $holiday = Holiday::where('is_active', true)
            ->where('start_date', '<=', $formattedDate)
            ->where('end_date', '>=', $formattedDate)
            ->first();

        if ($holiday) {
            return response()->json([
                'date' => $formattedDate,
                'day_of_week' => $dayName,
                'is_holiday' => true,
                'holiday_title' => $holiday->title,
                'is_day_active' => false,
                'slots' => [],
            ]);
        }

        // Check if day of week is active in weekly_schedule
        $weeklySchedule = WeeklySchedule::where('day_of_week', $dayName)->first();

        if (!$weeklySchedule || !$weeklySchedule->is_active) {
            return response()->json([
                'date' => $formattedDate,
                'day_of_week' => $dayName,
                'is_holiday' => false,
                'holiday_title' => null,
                'is_day_active' => false,
                'slots' => [],
            ]);
        }

        // Fetch slots for this day
        $slots = ScheduleSlot::where('weekly_schedule_id', $weeklySchedule->id)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        // Get existing booked start_times for this date
        $bookedTimes = Booking::where('booking_date', $formattedDate)
            ->where('status', '!=', 'cancelled')
            ->pluck('start_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        $formattedSlots = $slots->map(function ($slot) use ($bookedTimes) {
            $startTime = Carbon::parse($slot->start_time)->format('H:i');
            $endTime = $slot->end_time ? Carbon::parse($slot->end_time)->format('H:i') : null;
            $displayTime = Carbon::parse($slot->start_time)->format('g:i A');
            $isBooked = in_array($startTime, $bookedTimes);

            return [
                'id' => $slot->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'display_time' => $displayTime,
                'is_booked' => $isBooked,
            ];
        });

        return response()->json([
            'date' => $formattedDate,
            'day_of_week' => $dayName,
            'is_holiday' => false,
            'holiday_title' => null,
            'is_day_active' => true,
            'slots' => $formattedSlots,
        ]);
    }

    public function storeStep2(BookingStep2Request $request): RedirectResponse
    {
        $this->bookingSessionService->saveStep2($request->validated());

        return redirect()->route('booking-service.your-details');
    }

    public function yourDetails()
    {
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

    public function reviewConfirm()
    {
        return view('pages.booking-service.review-confirm');
    }
}
