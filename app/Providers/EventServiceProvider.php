<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\PaymentStatusUpdated;
use App\Events\ProductPublished;
use App\Listeners\RecordProductPublicationAudit;
use App\Listeners\UpdateCustomerMetrics;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            UpdateCustomerMetrics::class,
        ],
        PaymentStatusUpdated::class => [
            UpdateCustomerMetrics::class,
        ],
        ProductPublished::class => [
            RecordProductPublicationAudit::class,
        ],
    ];
}
