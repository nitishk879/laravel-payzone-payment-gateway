<?php


namespace Svodya\PayZone\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Svodya\PayZone\Events\OrderReceivedEvent;
use Svodya\PayZone\Events\TransactionPaymentReceivedEvent;
use Svodya\PayZone\Listeners\OrderReceivedListener;
use Svodya\PayZone\Listeners\SendTransactionPaymentNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransactionPaymentReceivedEvent::class =>[
            SendTransactionPaymentNotification::class
        ],
//        'Svodya\PayZone\Events\TransactionPaymentReceivedEvent' => [
//            'Svodya\PayZone\Listeners\SendTransactionPaymentNotification',
//        ],
        'Svodya\PayZone\Events\OrderReceivedEvent' => [
            'Svodya\PayZone\Listeners\OrderReceivedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
