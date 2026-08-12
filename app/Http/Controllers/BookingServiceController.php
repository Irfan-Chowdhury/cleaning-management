<?php

namespace App\Http\Controllers;

class BookingServiceController extends Controller
{
    public function create()
    {
        return view('pages.booking-service.create');
    }
}
