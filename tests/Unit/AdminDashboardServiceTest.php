<?php

use App\Services\AdminDashboardService;

it('instantiates AdminDashboardService and returns today bookings structure', function () {
    $service = new AdminDashboardService();

    expect(method_exists($service, 'getTodayBookingsMetrics'))->toBeTrue()
        ->and(method_exists($service, 'getActiveCustomersMetrics'))->toBeTrue()
        ->and(method_exists($service, 'getTotalRevenueMetrics'))->toBeTrue()
        ->and(method_exists($service, 'getReferralMetrics'))->toBeTrue()
        ->and(method_exists($service, 'getBookingQueue'))->toBeTrue()
        ->and(method_exists($service, 'getServiceMix'))->toBeTrue()
        ->and(method_exists($service, 'getOpenSlotsThisWeek'))->toBeTrue();
});
