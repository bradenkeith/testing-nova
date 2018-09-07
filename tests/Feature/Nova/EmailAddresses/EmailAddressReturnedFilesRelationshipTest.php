<?php

namespace Tests\Feature\Nova\EmailAddresses;

use Tests\TestCase;

class EmailAddressReturnedFilesRelationshipTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
    }

    /** @test **/
    public function emailAddresses_hasMany_returnedFiles()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $project = factory(\App\Project::class)->create(['id'=>2]);
        $email->projects()->save($project);

        $returnedFile = factory(\App\ReturnedFile::class)->create([
            'id'               => 3,
            'email_address_id' => $email->id,
            'project_id'       => $project->id,
        ]);

        $response = $this->get('nova-api/returned-files?viaResource=email-addresses&viaResourceId='.$email->id.'&viaRelationship=returnedFiles&relationshipType=hasMany');

        $response->assertJson([
            'label'     => 'Returned Files',
            'resources' => [
                [
                    'id' => [
                        'value'=> $returnedFile->id,
                    ],
                ],
            ],
        ]);
    }
}
