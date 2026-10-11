<?php

use App\Services\AdminDashboardService;

it('instantiates AdminDashboardService and returns today bookings structure', function () {
    $service = new AdminDashboardService();

    expect(method_exists($service, 'getTodayBookingsMetrics'))->toBeTrue();
});
