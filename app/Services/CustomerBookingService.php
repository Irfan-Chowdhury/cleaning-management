<?php

namespace App\Services;

use App\Models\ServiceQuestion;

class CustomerBookingService
{
    protected BookingSessionService $bookingSessionService;
    protected ImageService $imageService;

    public function __construct(
        BookingSessionService $bookingSessionService,
        ImageService $imageService
    ) {
        $this->bookingSessionService = $bookingSessionService;
        $this->imageService = $imageService;
    }
    
    /**
     * Step-1 | Service Details
     *
     * Coordinates business logic and workflows for Customer Booking Step 1.
     */

    /**
     * Validate service questions: required status and allowed options from database.
     *
     * @param int|null $serviceId
     * @param array $submittedQuestions
     * @return array Array of validation errors keyed by "questions.{id}"
     */
    public function validateServiceQuestions(?int $serviceId, array $submittedQuestions): array
    {
        if (!$serviceId) {
            return [];
        }

        try {
            $serviceQuestions = ServiceQuestion::where('service_id', $serviceId)
                ->with('questionOptions')
                ->orderBy('sort_order')
                ->get();
        } catch (\Throwable $e) {
            return [];
        }

        $errors = [];
        $validQuestionIds = [];

        foreach ($serviceQuestions as $question) {
            $validQuestionIds[] = (string) $question->id;
            $answer = $submittedQuestions[$question->id] ?? null;

            // 1. Validate required answer
            if ($question->required && $this->isAnswerEmpty($answer)) {
                $errors["questions.{$question->id}"] = "The '{$question->title}' field is required.";
                continue;
            }

            // 2. Validate answer against predefined question options
            if (!$this->isOptionAnswerValid($question, $answer)) {
                $errors["questions.{$question->id}"] = "The selected option for '{$question->title}' is invalid.";
            }
        }

        // 3. Check if any submitted question ID does not belong to the selected service
        foreach ($submittedQuestions as $questionId => $answer) {
            if (!in_array((string) $questionId, $validQuestionIds, true)) {
                $errors["questions.{$questionId}"] = 'Invalid question submitted for the selected service.';
            }
        }

        return $errors;
    }

    /**
     * Check if a submitted answer is empty.
     *
     * @param mixed $answer
     * @return bool
     */
    public function isAnswerEmpty(mixed $answer): bool
    {
        return $answer === null || $answer === '' || (is_array($answer) && count($answer) === 0);
    }

    /**
     * Check if a submitted answer matches allowed options for an option-based question.
     *
     * @param ServiceQuestion $question
     * @param mixed $answer
     * @return bool
     */
    public function isOptionAnswerValid(ServiceQuestion $question, mixed $answer): bool
    {
        if ($this->isAnswerEmpty($answer) || $question->questionOptions->isEmpty()) {
            return true;
        }

        $allowedLabels = $question->questionOptions->pluck('label')->all();
        $allowedIds = $question->questionOptions->pluck('id')->map(fn($id) => (string) $id)->all();
        $validOptions = array_merge($allowedLabels, $allowedIds);

        if (is_array($answer)) {
            foreach ($answer as $selectedOption) {
                if ($selectedOption !== null && $selectedOption !== '' && !in_array((string) $selectedOption, $validOptions, true)) {
                    return false;
                }
            }
            return true;
        }

        return in_array((string) $answer, $validOptions, true);
    }

    /**
     * Process and store Step 1 data (coordinate images and session storage).
     *
     * @param array $validatedData
     * @param array|null $uploadedImages
     * @return void
     */
    public function processStep1(array $validatedData, ?array $uploadedImages = null): void
    {
        $existingStep1 = $this->bookingSessionService->getStep1Data();
        $existingImages = $existingStep1['images'] ?? [];

        $validatedData['images'] = $this->imageService->uploadTemporaryBookingImages(
            $uploadedImages,
            $existingImages
        );

        $this->bookingSessionService->saveStep1($validatedData);
    }
}
