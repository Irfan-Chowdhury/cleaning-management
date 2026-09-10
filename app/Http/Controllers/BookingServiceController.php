<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStep1Request;
use App\Models\Service;
use App\Services\BookingSessionService;
use Illuminate\Http\RedirectResponse;

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

    public function questionnaire(Service $service)
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
        return view('pages.booking-service.date-time');
    }

    public function yourDetails()
    {
        return view('pages.booking-service.your-details');
    }

    public function reviewConfirm()
    {
        return view('pages.booking-service.review-confirm');
    }
}
