<?php

namespace App\Providers;

use App\Events\BookingCompletedAndPaid;
use App\Listeners\AwardReferralBonus;
use App\Listeners\SendReferralBonusNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        BookingCompletedAndPaid::class => [
            AwardReferralBonus::class,
            SendReferralBonusNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
