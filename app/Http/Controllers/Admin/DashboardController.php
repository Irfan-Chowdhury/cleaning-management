<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected AdminDashboardService $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): View
    {
        $todayBookings         = $this->dashboardService->getTodayBookingsMetrics();
        $activeCustomers       = $this->dashboardService->getActiveCustomersMetrics();
        $totalRevenue          = $this->dashboardService->getTotalRevenueMetrics();
        $referralPipeline      = $this->dashboardService->getReferralMetrics();
        $bookingQueue          = $this->dashboardService->getBookingQueue(10);
        $serviceMix            = $this->dashboardService->getServiceMix();
        $openSlotsThisWeek         = $this->dashboardService->getOpenSlotsThisWeek();
        $totalHolidaysCurrentMonth = $this->dashboardService->getTotalHolidaysCurrentMonthCount();
        $creditsIssuedThisMonth    = $this->dashboardService->getTotalCreditsIssuedCurrentMonth();
        $welcomeBonusThisMonth     = $this->dashboardService->getWelcomeBonusThisMonth();
        $referralBonusThisMonth    = $this->dashboardService->getReferralBonusThisMonth();
        $googleRewardsThisMonth    = $this->dashboardService->getGoogleRewardsThisMonth();
        $recentActivities          = []; //$this->dashboardService->getRecentActivities(4);

        return view('pages.admin.dashboard', compact(
            'todayBookings',
            'activeCustomers',
            'totalRevenue',
            'referralPipeline',
            'bookingQueue',
            'serviceMix',
            'openSlotsThisWeek',
            'totalHolidaysCurrentMonth',
            'creditsIssuedThisMonth',
            'welcomeBonusThisMonth',
            'referralBonusThisMonth',
            'googleRewardsThisMonth',
            'recentActivities'
        ));
    }
}
