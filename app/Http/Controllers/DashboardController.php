<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendReferralInviteRequest;
use App\Services\CustomerDashboardService;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected CustomerDashboardService $dashboardService,
        protected ReferralService $referralService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $userId = (int) $user->id;

        $nextCleaning = $this->dashboardService->getNextCleaning($userId);
        $stats = $this->dashboardService->getDashboardStats($userId);
        $recentBookings = $this->dashboardService->getRecentBookings($userId, 3);
        $quickBookServices = $this->dashboardService->getQuickBookServices($userId);
        $referralData = $this->dashboardService->getReferralProgramData($userId);

        // Fetch "Your Referrals" feature data using injected ReferralService instance
        $yourReferrals = $this->referralService->getYourReferralsDashboardData($user);

        return view('pages.dashboard', compact(
            'nextCleaning',
            'stats',
            'recentBookings',
            'quickBookServices',
            'referralData',
            'yourReferrals'
        ));
    }

    /**
     * Handle referral email invitation submission from customer dashboard.
     */
    public function sendInvite(SendReferralInviteRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $recipientEmail = $request->validated('email');
        $customMessage = $request->validated('message');

        $result = $this->referralService->sendEmailInvitation($user, $recipientEmail, $customMessage);

        if ($result['success']) {
            return redirect()->route('dashboard')
                ->with('success', $result['message']);
        }

        return redirect()->route('dashboard')
            ->with('error', $result['message']);
    }
}
