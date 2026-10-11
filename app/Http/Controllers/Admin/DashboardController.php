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
        $todayBookings    = $this->dashboardService->getTodayBookingsMetrics();
        $activeCustomers  = $this->dashboardService->getActiveCustomersMetrics();
        $totalRevenue     = $this->dashboardService->getTotalRevenueMetrics();
        $referralPipeline = $this->dashboardService->getReferralMetrics();

        return view('pages.admin.dashboard', compact(
            'todayBookings',
            'activeCustomers',
            'totalRevenue',
            'referralPipeline'
        ));
    }
}
