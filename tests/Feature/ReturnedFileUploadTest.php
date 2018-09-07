<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ReturnedFileUploadTest extends TestCase
{
    /** @test **/
    public function user_can_upload_file()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->save($project);

        $signed_url = URL::signedRoute('returned-file', ['email_address' => $email->id, 'project' => $project->id]);

        Storage::fake('local');

        $response = $this->post($signed_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response
            ->assertRedirect(
                URL::signedRoute('projects',
                    ['email_address' => $email->id, 'project' => $project->id])
            )
            ->assertSessionHas('status', 'File Successfully Uploaded!');

        $returnedFile = \App\ReturnedFile::first();
        $this->assertFalse(is_null($returnedFile));

        Storage::disk('local')->assertExists($returnedFile->file_path);
    }
}
