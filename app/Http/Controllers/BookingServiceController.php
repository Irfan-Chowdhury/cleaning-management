<?php

namespace App\Http\Controllers;

use App\Models\Service;

class BookingServiceController extends Controller
{
    public function create()
    {
        $services = Service::where('status', 'active')->orderBy('name')->get();

        return view('pages.booking-service.create', compact('services'));
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
