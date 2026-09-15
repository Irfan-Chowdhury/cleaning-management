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
     * Save Step 2 data into the session.
     */
    public function saveStep2(array $data): void
    {
        $sessionData = $this->getBookingSession();
        $sessionData['step2'] = [
            'booking_date' => $data['booking_date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'frequency' => $data['frequency'] ?? 'one_time',
        ];

        Session::put($this->sessionKey, $sessionData);
    }

    /**
     * Get Step 2 data from session.
     */
    public function getStep2Data(): array
    {
        $sessionData = $this->getBookingSession();
        return $sessionData['step2'] ?? [];
    }

    /**
     * Save Step 3 data into the session.
     */
    public function saveStep3(array $data): void
    {
        $sessionData = $this->getBookingSession();
        $sessionData['step3'] = [
            'detail_mode'          => $data['detail_mode'] ?? 'account',
            'customer_name'        => $data['customer_name'] ?? null,
            'customer_email'       => $data['customer_email'] ?? null,
            'customer_phone'       => $data['customer_phone'] ?? null,
            'customer_address'     => $data['customer_address'] ?? null,
            'unit_suite_floor'     => $data['unit_suite_floor'] ?? null,
            'suburb'               => $data['suburb'] ?? null,
            'postcode'             => $data['postcode'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
        ];

        Session::put($this->sessionKey, $sessionData);
    }

    /**
     * Get Step 3 data from session.
     */
    public function getStep3Data(): array
    {
        $sessionData = $this->getBookingSession();
        return $sessionData['step3'] ?? [];
    }

    /**
     * Save offer (promo/referral/wallet) data into session.
     */
    public function saveOffer(array $data): void
    {
        $sessionData = $this->getBookingSession();
        $sessionData['offer'] = $data;
        Session::put($this->sessionKey, $sessionData);
    }

    /**
     * Get offer data from session.
     */
    public function getOfferData(): array
    {
        $sessionData = $this->getBookingSession();
        return $sessionData['offer'] ?? [];
    }

    /**
     * Remove offer data from session.
     */
    public function removeOffer(): void
    {
        $sessionData = $this->getBookingSession();
        unset($sessionData['offer']);
        Session::put($this->sessionKey, $sessionData);
    }

    /**
     * Clear all booking wizard session data.
     */
    public function clearSession(): void
    {
        Session::forget($this->sessionKey);
    }
}
