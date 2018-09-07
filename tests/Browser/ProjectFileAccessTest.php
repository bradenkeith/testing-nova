<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProjectFileAccessTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test **/
    public function user_can_see_files_on_project_show()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $project_files = factory(\App\ProjectFile::class, 2)->make();
        $project->projectFiles()->saveMany($project_files);
        $email->projects()->attach($project);

        $signed_url = URL::signedRoute('projects', ['email_address' => $email->id, 'project' => $project->id]);

        $this->browse(function (Browser $browser) use ($signed_url, $project_files, $email, $project) {
            $browser->visit($signed_url);

            $browser->assertSee('Download Files');
            $project_files->each(function ($file) use ($browser, $email, $project) {
                $browser->assertSee($file->name);

                $download_signed_url = URL::signedRoute('download', ['email_address' => $email->id, 'project' => $project->id, 'file_path' => base64_encode($file->file_path)]);

                $browser->assertSourceHas('<a href="'.$download_signed_url.'" class="btn btn-primary btn-wd">Download</a>');
            });
        });
    }

    /** @test **/
    public function user_can_return_files_on_project_show()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create();
        $email->projects()->attach($project);

        $signed_url = URL::signedRoute('projects', ['email_address' => $email->id, 'project' => $project->id]);

        $this->browse(function (Browser $browser) use ($signed_url, $project, $email) {
            $browser->visit($signed_url);

            $browser->assertSee('Sign and Return');

            $browser->attach('file', __DIR__.'/photos/me.jpg');

            $browser->click('#returned-file-submit');

            $browser->assertSee('File Successfully Uploaded!');

            $browser->assertSee('Files You Already Returned');

            $returnedFile = \App\ReturnedFile::first();

            $download_signed_url = URL::signedRoute('download', ['email_address' => $email->id, 'project' => $project->id, 'file_path' => base64_encode($returnedFile->file_path)]);
            $browser->assertSourceHas('<a href="'.$download_signed_url.'" class="btn btn-primary btn-wd">Download</a>');

            $browser->assertSee('Date Added: '.$returnedFile->created_at->tz('America/New_York')->toDayDateTimeString());
        });
    }
}
