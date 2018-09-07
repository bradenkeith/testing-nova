<?php

namespace Tests\Unit\Jobs;

use App\Events\FileDeleted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileDeletedEventTest extends TestCase
{
    /** @test **/
    public function it_triggers_when_project_file_deleted()
    {
        Event::fake();
        $projectFile = factory(\App\ProjectFile::class)->create();
        $projectFile->delete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($projectFile) {
            return $e->file_path === $projectFile->file_path;
        });
    }

    /** @test **/
    public function it_triggers_when_returned_file_deleted()
    {
        Event::fake();
        $returnedFile = factory(\App\ReturnedFile::class)->create();
        $returnedFile->delete();

        Event::assertDispatched(FileDeleted::class, function ($e) use ($returnedFile) {
            return $e->file_path === $returnedFile->file_path;
        });
    }

    /** @test **/
    public function it_deletes_file()
    {
        Event::fake();
        Storage::fake('local');

        $uploadedFile = UploadedFile::fake()->image('avatar.jpg');
        $saveFile = Storage::put($uploadedFile->name, $uploadedFile);

        $file = factory(\App\ReturnedFile::class)->create([
            'file_path' => $saveFile,
        ]);

        Storage::disk('local')->assertExists($saveFile);

        FileDeleted::dispatch($file);

        Event::assertDispatched(FileDeleted::class);

        Storage::disk('local')->assertMissing($saveFile);
    }
}
