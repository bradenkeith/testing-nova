<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class HasAccessToProjectEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $emailAddress;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Project $project, \App\EmailAddress $emailAddress)
    {
        $this->project = $project;
        $this->emailAddress = $emailAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::signedRoute('projects', ['email_address' => $this->emailAddress->id, 'project' => $this->project->id]);

        return $this
            ->subject('Project Access')
            ->markdown('emails.has-access-to-project', ['url' => $url]);
    }
}
