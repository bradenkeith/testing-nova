<?php

namespace App\Listeners;

use App\Events\ProjectHasNewEmailAddressWithAccessEvent;
use App\Mail\HasAccessToProjectEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendHasAccessToProjectNotification implements ShouldQueue
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
     * @param object $event
     *
     * @return void
     */
    public function handle(ProjectHasNewEmailAddressWithAccessEvent $event)
    {
        Log::info('SendHasAccessToProjectNotification running... '.$event->emailAddress->email);
        Mail::to($event->emailAddress->email)
            ->queue(
                new HasAccessToProjectEmail($event->project, $event->emailAddress)
            );
    }
}
