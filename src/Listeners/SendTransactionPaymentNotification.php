<?php

namespace Svodya\PayZone\Listeners;

use Svodya\PayZone\Events\TransactionPaymentReceivedEvent;
use Svodya\PayZone\Mail\TransactionPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTransactionPaymentNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransactionPaymentReceivedEvent  $event
     * @return void
     */
    public function handle(TransactionPaymentReceivedEvent $event)
    {
        Mail::to('nitishk879@gmail.com')->send(new TransactionPayment($event->transaction));
    }
}
