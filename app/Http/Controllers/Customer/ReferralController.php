<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CustomerReferralService;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function __construct(private readonly CustomerReferralService $customerReferralService)
    {
    }

    /**
     * Display customer referral program dashboard and dynamic referral history.
     */
    public function index()
    {
        $data = $this->customerReferralService->getCustomerReferralData(Auth::user());

        return view('pages.customer.refferal.index', $data);
    }
}
