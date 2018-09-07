<?php

namespace Tests\Unit\Jobs;

use App\Events\ProjectHasNewEmailAddressWithAccessEvent;
use App\Mail\HasAccessToProjectEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendHasAccessToProjectNotificationTest extends TestCase
{
    /** @test **/
    public function it_sends_the_message_to_email_address()
    {
        Mail::fake();

        $project = factory(\App\Project::class)->create();
        $email_address = factory(\App\EmailAddress::class)->create();

        ProjectHasNewEmailAddressWithAccessEvent::dispatch($project, $email_address);

        Mail::assertQueued(HasAccessToProjectEmail::class, function ($mail) use ($project,$email_address) {
            return $mail->hasTo($email_address->email)
            && $mail->project->id == $project->id
            && $mail->emailAddress->id == $email_address->id;
        });
    }
}
