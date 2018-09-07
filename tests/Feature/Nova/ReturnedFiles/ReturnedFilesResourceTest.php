<?php

namespace Tests\Feature\Nova\ReturnedFiles;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnedFilesResourceTest extends TestCase
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
        $file = factory(\App\ReturnedFile::class)->create();
        $response = $this->get('/nova-api/returned-files/1');
        $response->assertJson([
            'panels' => [
                [
                    'name' => 'Returned File Details',
                ],
            ],
        ]);
    }

    /** @test **/
    public function files_can_be_retrieved_with_correct_resource_elements()
    {
        $file = factory(\App\ReturnedFile::class)->create();

        $response = $this->get('/nova-api/returned-files/1');

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
                    [
                        'component' => 'belongs-to-field',
                        'name'      => 'Email Address',
                        'attribute' => 'emailAddress',
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function project_has_correct_validation_on_create()
    {
        $returnedFile = factory(\App\ReturnedFile::class)->make();

        $good_data_response = $this->post('/nova-api/returned-files/',
            array_merge(
                $returnedFile->getAttributes(),
                [
                    'project'      => $returnedFile->project_id,
                    'emailAddress' => $returnedFile->email_address_id,
                    'file_path'    => UploadedFile::fake()->image('avatar.jpg'),
                ]
            ));

        $good_data_response->assertStatus(201);
    }

    /** @test **/
    public function project_is_required_on_create()
    {
        $response = $this->post('/nova-api/returned-files/', ['project_id'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'project' => 'The project field is required.',
        ]);
    }

    /** @test **/
    public function file_field_is_required_on_create()
    {
        $response = $this->post('/nova-api/returned-files/', ['file_path'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'file_path' => 'The file path field is required.',
        ]);
    }

    /** @test **/
    public function email_address_is_required_on_create()
    {
        $response = $this->post('/nova-api/returned-files/', ['email_address'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'emailAddress' => 'The email address field is required.',
        ]);
    }
}
