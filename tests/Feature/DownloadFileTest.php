<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DownloadFileTest extends TestCase
{
    /** @test **/
    public function route_is_protected_by_signed_URLs()
    {
        Storage::fake('local');

        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->save($project);

        $signed_returned_file_url = URL::signedRoute('returned-file',
            [
                'email_address' => $email->id,
                'project'       => $project->id,
            ]
        );

        $this->post($signed_returned_file_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $file = \App\ReturnedFile::first();
        $file_path = base64_encode($file->file_path);

        $signed_url = URL::signedRoute('download',
            [
                'email_address' => $email->id,
                'project'       => $project->id,
                'file_path'     => $file_path,
            ]
        );
        $response = $this->get($signed_url);
        $response->assertOk();

        $unauthenticated_response = $this->get('download/'.$project->id.'/'.$email->id.'/'.$file_path);
        $unauthenticated_response->assertStatus(403);
    }

    /** @test **/
    public function route_is_protected_by_email_id_having_access_to_project()
    {
        Storage::fake('local');

        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->save($project);

        $signed_returned_file_url = URL::signedRoute('returned-file',
            [
                'email_address' => $email->id,
                'project'       => $project->id,
            ]
        );

        $this->post($signed_returned_file_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $file = \App\ReturnedFile::first();
        $file_path = base64_encode($file->file_path);

        $signed_url = URL::signedRoute('download',
            [
                'email_address' => $email->id,
                'project'       => $project->id,
                'file_path'     => $file_path,
            ]
        );
        $response = $this->get($signed_url);
        $response->assertOk();

        $email->projects()->detach($project);

        $unauthenticated_response = $this->get($signed_url);
        $unauthenticated_response->assertStatus(403);
    }

    /** @test **/
    public function download_url_initiates_file_download()
    {
        Storage::fake('local');

        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->save($project);

        $signed_returned_file_url = URL::signedRoute('returned-file',
            [
                'email_address' => $email->id,
                'project'       => $project->id,
            ]
        );

        $this->post($signed_returned_file_url, [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $file = \App\ReturnedFile::first();
        $file_path = base64_encode($file->file_path);
        $file_name = array_slice(explode('/', $file->file_path), -1)[0];

        $signed_url = URL::signedRoute('download',
            [
                'email_address' => $email->id,
                'project'       => $project->id,
                'file_path'     => $file_path,
            ]
        );
        $response = $this->get($signed_url);
        $response->assertHeader('content-type', 'image/jpeg');
        $response->assertHeader('content-disposition', 'attachment; filename='.$file_name);
    }
}
