<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function __construct(private readonly ReferralService $referralService)
    {
    }

    /**
     * Display customer referral program dashboard and dynamic referral history.
     */
    public function index()
    {
        $data = $this->referralService->getCustomerReferralData(Auth::user());

        return view('pages.customer.refferal.index', $data);
    }
}
