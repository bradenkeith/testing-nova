<?php

namespace Tests\Unit\Mail;

use App\Mail\HasAccessToProjectEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NotifyHasAccessToProjectEmail extends TestCase
{
    /** @test **/
    public function email_has_correct_subject_and_message()
    {
        $user = factory(\App\EmailAddress::class)->create()->fresh();
        $project = factory(\App\Project::class)->create()->fresh();

        $email = new HasAccessToProjectEmail($project, $user);

        $signed_url = URL::signedRoute('projects', ['email_address' => $user->id, 'project' => $project->id]);

        $this->assertEquals('Project Access', $email->build()->subject);
        $this->assertContains('Hi '.$user->name.',', trim($email->render($email)));
        $this->assertContains('You have been given access to project "'.$project->name.'".', trim($email->render($email)));
        $this->assertContains('To access the files for this project, please use the link below.', trim($email->render($email)));
        $this->assertContains('<a href="'.$signed_url.'" class="button button-primary" target="_blank" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3097D1; border-top: 10px solid #3097D1; border-right: 18px solid #3097D1; border-bottom: 10px solid #3097D1; border-left: 18px solid #3097D1;">View Project</a>', trim($email->render($email)));
        $this->assertContains('This is an unmonitored email box, please do not respond to this email.', trim($email->render($email)));
    }
}
