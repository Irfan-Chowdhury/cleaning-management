<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = (object)[
            'company_name'               => 'Clean Manage Pro',
            'company_logo'               => 'https://placehold.co/340x96/ffffff/0866e8?text=Clean+Manage',
            'welcome_credit'             => 20.00,
            'referral_reward'            => 25.00,
            'google_review_reward'       => 15.00,
            'max_advance_booking_days'   => 30,
            'cancellation_notice_hours' => 24,
        ];

        return view('pages.admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        return redirect()->route('settings.index')->with('success', 'Company settings updated successfully!');
    }
}
