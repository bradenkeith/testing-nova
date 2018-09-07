<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ReturnedFileAccessTest extends TestCase
{
    /** @test **/
    public function route_is_protected_by_signed_URLs()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->save($project);

        $signed_url = URL::signedRoute('returned-file', ['email_address' => $email->id, 'project' => $project->id]);

        $response = $this->post($signed_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $response->assertRedirect();

        $unauthenticated_response = $this->post('returned-file/'.$project->id.'/'.$email->id, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $unauthenticated_response->assertStatus(403);
    }

    /** @test **/
    public function route_is_protected_by_email_id_having_access_to_project()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->attach($project);

        $signed_url = URL::signedRoute('returned-file', ['email_address' => $email->id, 'project' => $project->id]);

        $response = $this->post($signed_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $response->assertRedirect();

        $email->projects()->detach($project);

        $unauthenticated_response = $this->post($signed_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $unauthenticated_response->assertStatus(403);
    }
}
