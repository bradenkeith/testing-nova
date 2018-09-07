<?php

namespace Tests\Feature\Nova\Projects;

use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
    }

    /** @test **/
    public function project_can_be_retrieved_with_correct_resource_elements()
    {
        $project = factory(\App\Project::class)->create();

        $response = $this->get('/nova-api/projects/1');

        $response->assertJson([
            'resource' => [
                'id' => [
                    'value' => $project->id,
                ],
                'fields' => [
                    [
                        'component' => 'text-field',
                        'attribute' => 'id',
                        'value'     => $project->id,
                    ],
                    [
                        'component' => 'text-field',
                        'attribute' => 'name',
                        'name'      => 'Name',
                        'value'     => $project->name,
                    ],
                    [
                        'component' => 'has-many-field',
                        'attribute' => 'projectFiles',
                        'name'      => 'Project Files',
                        'value'     => null,
                    ],
                    [
                        'component' => 'belongs-to-many-field',
                        'attribute' => 'emailAddresses',
                        'name'      => 'Email Addresses',
                        'value'     => null,
                    ],
                    [
                        'component' => 'has-many-field',
                        'attribute' => 'returnedFiles',
                        'name'      => 'Returned Files',
                        'value'     => null,
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function project_has_correct_validation_on_create()
    {
        $project = factory(\App\Project::class)->make();

        $response = $this->post('/nova-api/projects/', $project->toArray());
        $response->assertStatus(201);
    }

    /** @test **/
    public function name_is_required_on_create()
    {
        $response = $this->post('/nova-api/projects/', ['name'=>null]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);
    }

    /** @test **/
    public function name_has_to_be_unique_on_create()
    {
        $project = factory(\App\Project::class)->create();
        $response = $this->post('/nova-api/projects/', ['name'=>$project->name]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name has already been taken.',
        ]);
    }

    /** @test **/
    public function name_has_to_be_under_255_chars_on_create()
    {
        $project = factory(\App\Project::class)->make([
            'name' => str_repeat('J', 256),
        ]);
        $response = $this->post('/nova-api/projects/', $project->toArray());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 255 characters.',
        ]);
    }
}
