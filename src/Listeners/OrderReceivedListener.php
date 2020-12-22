<?php


namespace Svodya\PayZone\Listeners;

use Svodya\PayZone\Events\OrderReceivedEvent;
use Svodya\PayZone\Mail\OrderReceivedMail;
use Illuminate\Support\Facades\Mail;

class OrderReceivedListener
{
    public function handle(OrderReceivedEvent $event)
    {
        Mail::to('nitishk879@gmail.com')->send(new OrderReceivedMail($event->transaction));
    }
}
