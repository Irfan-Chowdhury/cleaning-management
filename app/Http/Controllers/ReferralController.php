<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;

class ReferralController extends Controller
{
    public function __construct(private readonly ReferralService $referralService)
    {
    }

    public function index()
    {
        $referrals = $this->referralService->getAdminReferralData();

        return view('pages.admin.referrals.index', compact('referrals'));
    }
}
