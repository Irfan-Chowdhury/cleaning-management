<?php

namespace App\Http\Controllers;

class BookingServiceController extends Controller
{
    public function create()
    {
        return view('booking-service.create');
    }

    public function dateTime()
    {
        return view('booking-service.date-time');
    }

    public function yourDetails()
    {
        return view('booking-service.your-details');
    }

    public function reviewConfirm()
    {
        return view('booking-service.review-confirm');
    }
}
