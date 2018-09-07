<?php

namespace Tests\Feature\Nova\Projects;

use App\Mail\HasAccessToProjectEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendHasAccessToProjectEmailActionTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
        Mail::fake();
    }

    /** @test **/
    public function project_actions_can_be_retrieved()
    {
        $project = factory(\App\Project::class)->create();
        $email_address = factory(\App\EmailAddress::class)->create();
        $project->emailAddresses()->attach($email_address);

        $response = $this->get('/nova-api/projects/actions?viaResource=email-addresses&viaResourceId='.$project->id.'&viaRelationship=projects');

        $response->assertJson([
            'pivotActions' => [
                'actions' => [
                    [
                        'class'  => "App\Nova\Actions\SendHasAccessToProjectEmail",
                        'name'   => 'Send Has Access To Project Email',
                        'uriKey' => 'send-has-access-to-project-email',
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function email_address_actions_can_be_retrieved()
    {
        $project = factory(\App\Project::class)->create();
        $email_address = factory(\App\EmailAddress::class)->create();
        $project->emailAddresses()->attach($email_address);

        $response = $this->get('/nova-api/email-addresses/actions?viaResource=projects&viaResourceId='.$email_address->id.'&viaRelationship=email-addresses');

        $response->assertJson([
            'pivotActions' => [
                'actions' => [
                    [
                        'class'  => "App\Nova\Actions\SendHasAccessToProjectEmail",
                        'name'   => 'Send Has Access To Project Email',
                        'uriKey' => 'send-has-access-to-project-email',
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function send_has_access_to_project_email_action_fires_from_project()
    {
        $project = factory(\App\Project::class)->create();
        $email_address = factory(\App\EmailAddress::class)->create();
        $project->emailAddresses()->attach($email_address);

        $response = $this->post('/nova-api/projects/action?action=send-has-access-to-project-email&pivotAction=true&search=&filters=&trashed=&viaResource=email-addresses&viaResourceId='.$email_address->id.'&viaRelationship=projects', ['resources'=>$project->id]);

        $response->assertOk();
    }

    /** @test **/
    public function send_has_access_to_project_email_action_fires_from_email_address()
    {
        $project = factory(\App\Project::class)->create();
        $email_address = factory(\App\EmailAddress::class)->create();
        $project->emailAddresses()->attach($email_address);

        $response = $this->post('/nova-api/email-addresses/action?action=send-has-access-to-project-email&pivotAction=true&search=&filters=&trashed=&viaResource=projects&viaResourceId='.$project->id.'&viaRelationship=emailAddresses', ['resources'=>$email_address->id]);

        $response->assertOk();
    }

    /** @test **/
    public function send_has_access_to_project_email_action_handle()
    {
        $project = factory(\App\Project::class)->create();
        $email_address = factory(\App\EmailAddress::class)->create();
        $project->emailAddresses()->attach($email_address);

        $response = $this->post('/nova-api/email-addresses/action?action=send-has-access-to-project-email&pivotAction=true&search=&filters=&trashed=&viaResource=projects&viaResourceId='.$project->id.'&viaRelationship=emailAddresses', ['resources'=>$email_address->id]);

        $response->assertOk();

        Mail::assertQueued(HasAccessToProjectEmail::class, function ($e) use ($project, $email_address) {
            return $e->project->id === $project->id
                && $e->emailAddress->id === $email_address->id;
        });
    }
}
