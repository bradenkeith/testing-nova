<?php

namespace Tests\Feature\Projects;

use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProjectAccessTest extends TestCase
{
    /** @test **/
    public function route_is_protected_by_signed_URLs()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->save($project);

        $signed_url = URL::signedRoute('projects', ['email_address' => $email->id, 'project' => $project->id]);

        $response = $this->get($signed_url);
        $response->assertOk();

        $unauthenticated_response = $this->get('projects/'.$project->id.'/'.$email->id);
        $unauthenticated_response->assertStatus(403);
    }

    /** @test **/
    public function route_is_protected_by_email_id_having_access_to_project()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->attach($project);

        $signed_url = URL::signedRoute('projects', ['email_address' => $email->id, 'project' => $project->id]);

        $response = $this->get($signed_url);
        $response->assertOk();

        $email->projects()->detach($project);

        $unauthenticated_response = $this->get($signed_url);
        $unauthenticated_response->assertStatus(403);
    }
}
