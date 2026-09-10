<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class BookingSessionService
{
    protected string $sessionKey = 'booking_wizard';

    /**
     * Retrieve the entire booking session data.
     */
    public function getBookingSession(): array
    {
        return Session::get($this->sessionKey, []);
    }

    /**
     * Save Step 1 data into the session.
     */
    public function saveStep1(array $data): void
    {
        $sessionData = $this->getBookingSession();
        $sessionData['step1'] = [
            'service_id' => $data['service_id'] ?? null,
            'questions' => $data['questions'] ?? [],
            'service_notes' => $data['service_notes'] ?? null,
        ];

        Session::put($this->sessionKey, $sessionData);
    }

    /**
     * Get Step 1 data from session.
     */
    public function getStep1Data(): array
    {
        $sessionData = $this->getBookingSession();
        return $sessionData['step1'] ?? [];
    }

    /**
     * Clear all booking wizard session data.
     */
    public function clearSession(): void
    {
        Session::forget($this->sessionKey);
    }
}
