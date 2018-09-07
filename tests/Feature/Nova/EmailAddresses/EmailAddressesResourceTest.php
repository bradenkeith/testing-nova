<?php

namespace Tests\Feature\Nova\EmailAddresses;

use Tests\TestCase;

class EmailAddressesResourceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
    }

    /** @test **/
    public function label_is_correct()
    {
        $email = factory(\App\EmailAddress::class)->create();
        $response = $this->get('/nova-api/email-addresses/1');
        $response->assertJson([
            'panels' => [
                [
                    'name' => 'Email Address Details',
                ],
            ],
        ]);
    }

    /** @test **/
    public function email_address_can_be_retrieved_with_correct_resource_elements()
    {
        $email = factory(\App\EmailAddress::class)->create();

        $response = $this->get('/nova-api/email-addresses/1');

        $response->assertJson([
            'resource' => [
                'id' => [
                    'value' => $email->id,
                ],
                'fields' => [
                    [
                        'component' => 'text-field',
                        'attribute' => 'id',
                        'value'     => $email->id,
                    ],
                    [
                        'component'    => 'file-field',
                        'name'         => 'Avatar',
                        'attribute'    => 'email',
                        'value'        => null,
                        'thumbnailUrl' => 'https://www.gravatar.com/avatar/'.md5($email->email).'?s=300',
                    ],
                    [
                        'component' => 'text-field',
                        'attribute' => 'name',
                        'value'     => $email->name,
                    ],
                    [
                        'component' => 'text-field',
                        'attribute' => 'email',
                        'value'     => $email->email,
                    ],
                    [
                        'component' => 'belongs-to-many-field',
                        'name'      => 'Projects',
                        'attribute' => 'projects',
                    ],
                    [
                        'component' => 'has-many-field',
                        'name'      => 'Returned Files',
                        'attribute' => 'returnedFiles',
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function user_has_correct_validation_on_create()
    {
        $model = factory(\App\EmailAddress::class)->make();

        $good_data_response = $this->post('/nova-api/email-addresses/', $model->getAttributes());
        $good_data_response->assertStatus(201);
    }

    /** @test **/
    public function name_is_required_on_create()
    {
        $model = $this->post('/nova-api/email-addresses/', ['name'=>null]);
        $model->assertStatus(302);
        $model->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);
    }

    /** @test **/
    public function name_must_be_under_255_chars_on_create()
    {
        $model = factory(\App\EmailAddress::class)->make([
            'name' => str_repeat('J', 256),
        ]);
        $response = $this->post('/nova-api/email-addresses/', $model->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 255 characters.',
        ]);
    }

    /** @test **/
    public function email_is_required_on_create()
    {
        $model = factory(\App\EmailAddress::class)->make([
            'email' => null,
        ]);
        $response = $this->post('/nova-api/email-addresses/', $model->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
        ]);
    }

    /** @test **/
    public function email_must_be_under_255_chars_on_create()
    {
        $model = factory(\App\EmailAddress::class)->make([
            'email' => str_repeat('J', 256),
        ]);
        $response = $this->post('/nova-api/email-addresses/', $model->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email may not be greater than 255 characters.',
        ]);
    }

    /** @test **/
    public function email_must_be_valid_on_create()
    {
        $model = factory(\App\EmailAddress::class)->make([
            'email' => 'johndoe',
        ]);
        $response = $this->post('/nova-api/email-addresses/', $model->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email must be a valid email address.',
        ]);
    }

    /** @test **/
    public function email_must_be_unique_on_create()
    {
        $model = factory(\App\EmailAddress::class)->create();
        $response = $this->post('/nova-api/email-addresses/', $model->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);
    }

    /** @test **/
    public function email_must_be_unique_except_for_self_on_update()
    {
        $model = factory(\App\EmailAddress::class)->create([
            'email' => $this->user->email,
        ]);
        $response = $this->put(
            '/nova-api/email-addresses/'.$model->id,
            $model->toArray()
        );
        $response->assertStatus(200);
    }

    /** @test **/
    public function email_must_be_unique_on_update()
    {
        $model = factory(\App\EmailAddress::class)->create();
        $model2 = factory(\App\EmailAddress::class)->create();

        $response = $this->put(
            '/nova-api/email-addresses/'.$model2->id,
            $model->toArray()
        );

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);
    }
}
