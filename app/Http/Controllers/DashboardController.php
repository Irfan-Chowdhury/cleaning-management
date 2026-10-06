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

        return view('pages.dashboard', compact('nextCleaning'));
    }
}
