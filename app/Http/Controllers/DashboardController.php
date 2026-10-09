<?php

namespace App\Http\Controllers;

use App\Services\CustomerDashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected CustomerDashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $userId = (int) Auth::id();
        $nextCleaning = $this->dashboardService->getNextCleaning($userId);
        $stats = $this->dashboardService->getDashboardStats($userId);
        $recentBookings = $this->dashboardService->getRecentBookings($userId, 3);
        $quickBookServices = $this->dashboardService->getQuickBookServices($userId);
        $referralData = $this->dashboardService->getReferralProgramData($userId);

        return view('pages.dashboard', compact('nextCleaning', 'stats', 'recentBookings', 'quickBookServices', 'referralData'));
    }
}
