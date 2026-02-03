<?php

namespace App\Providers;

use App\Events\PurchaseOrderApproved;
use App\Events\PurchaseOrderCreated;
use App\Events\StockMovementRecorded;
use App\Listeners\CheckReorderLevel;
use App\Listeners\NotifySupplierOfPurchaseOrder;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        StockMovementRecorded::class => [
            CheckReorderLevel::class,
        ],
        PurchaseOrderApproved::class => [
            NotifySupplierOfPurchaseOrder::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
