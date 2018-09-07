<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectHasNewEmailAddressWithAccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project;
    public $emailAddress;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Project $project, \App\EmailAddress $emailAddress)
    {
        $this->project = $project;
        $this->emailAddress = $emailAddress;
    }
}
