<?php

namespace App\Http\Controllers;

class BookingServiceController extends Controller
{
    public function create()
    {
        return view('pages.booking-service.create');
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
