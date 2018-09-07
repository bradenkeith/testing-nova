<?php

namespace Tests\Feature\Nova\ProjectFiles;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectFileResourceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
        Storage::fake();
    }

    /** @test **/
    public function label_is_correct()
    {
        $file = factory(\App\ProjectFile::class)->create();
        $response = $this->get('/nova-api/project-files/1');
        $response->assertJson([
            'panels' => [
                [
                    'name' => 'Project File Details',
                ],
            ],
        ]);
    }

    /** @test **/
    public function files_can_be_retrieved_with_correct_resource_elements()
    {
        $file = factory(\App\ProjectFile::class)->create();

        $response = $this->get('/nova-api/project-files/1');

        $response->assertJson([
            'resource' => [
                'id' => [
                    'value' => $file->id,
                ],
                'fields' => [
                    [
                        'component' => 'text-field',
                        'attribute' => 'id',
                        'value'     => $file->id,
                    ],
                    [
                        'component' => 'text-field',
                        'attribute' => 'name',
                        'name'      => 'Name',
                        'value'     => $file->name,
                    ],
                    [
                        'component' => 'file-field',
                        'name'      => 'File',
                        'attribute' => 'file_path',
                        'value'     => $file->file_path,
                    ],
                    [
                        'component' => 'belongs-to-field',
                        'name'      => 'Project',
                        'attribute' => 'project',
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function project_has_correct_validation_on_create()
    {
        $projectFile = factory(\App\ProjectFile::class)->make();

        $good_data_response = $this->post('/nova-api/project-files/',
            array_merge(
                $projectFile->getAttributes(),
                [
                    'project'   => $projectFile->project_id,
                    'file_path' => UploadedFile::fake()->image('avatar.jpg'),
                ]
            ));

        $good_data_response->assertStatus(201);
    }

    /** @test **/
    public function name_is_required_on_create()
    {
        $response = $this->post('/nova-api/project-files/', ['name'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);
    }

    /** @test **/
    public function project_is_required_on_create()
    {
        $response = $this->post('/nova-api/project-files/', ['project_id'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'project' => 'The project field is required.',
        ]);
    }

    /** @test **/
    public function file_field_is_required_on_create()
    {
        $response = $this->post('/nova-api/project-files/', ['file_path'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'file_path' => 'The file path field is required.',
        ]);
    }
}
