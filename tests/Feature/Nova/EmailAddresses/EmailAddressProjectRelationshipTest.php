<?php

namespace Tests\Feature\Nova\EmailAddresses;

use Tests\TestCase;

class EmailAddressProjectRelationshipTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
    }

    /** @test **/
    public function emailAddresses_belongToMany_projects()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create(['id'=>2]);
        $email->projects()->save($project);

        $response = $this->get('nova-api/projects?viaResource=email-addresses&viaResourceId='.$email->id.'&viaRelationship=projects&relationshipType=belongsToMany');

        $response->assertJson([
            'label'     => 'Projects',
            'resources' => [
                [
                    'id' => [
                        'value'=> 2,
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function projects_hasMany_email_addresses()
    {
        $project = factory(\App\Project::class)->create();
        $email = factory(\App\EmailAddress::class)->create(['id'=>2]);
        $project->emailAddresses()->save($email);

        $response = $this->get('nova-api/email-addresses?viaResource=projects&viaResourceId='.$project->id.'&viaRelationship=emailAddresses&relationshipType=belongsToMany');

        $response->assertJson([
            'label'     => 'Email Addresses',
            'resources' => [
                [
                    'id' => [
                        'value' => 2,
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function email_address_is_attachable_to_project()
    {
        $project = factory(\App\Project::class)->create();
        $email = factory(\App\EmailAddress::class)->create();
        $response = $this->get('nova-api/projects/1/attachable/email-addresses');
        $response->assertJson([
            'resources' => [
                [
                    'value'   => $email->id,
                    'display' => $email->name,
                ],
            ],
        ]);
    }

    /** @test **/
    public function project_is_attachable_to_email_address()
    {
        $project = factory(\App\Project::class)->create();
        $email = factory(\App\EmailAddress::class)->create();
        $response = $this->get('nova-api/email-addresses/1/attachable/projects');
        $response->assertJson([
            'resources' => [
                [
                    'value'   => $project->id,
                    'display' => $project->name,
                ],
            ],
        ]);
    }
}
