<?php

namespace Tests\Feature\Nova\Users;

use App\User;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->user->assignRole('super_administrator');
    }

    /** @test **/
    public function user_can_be_retrieved_with_correct_resource_elements()
    {
        $response = $this->get('/nova-api/users/1');

        $response->assertJson([
            'resource' => [
                'id' => [
                    'value' => $this->user->id,
                ],
                'fields' => [
                    [
                        'component' => 'text-field',
                        'attribute' => 'id',
                        'value'     => $this->user->id,
                    ],
                    [
                        'component'    => 'file-field',
                        'name'         => 'Avatar',
                        'attribute'    => 'email',
                        'value'        => null,
                        'thumbnailUrl' => 'https://www.gravatar.com/avatar/'.md5($this->user->email).'?s=300',
                    ],
                    [
                        'component' => 'text-field',
                        'attribute' => 'name',
                        'value'     => $this->user->name,
                    ],
                    [
                        'component' => 'text-field',
                        'attribute' => 'email',
                        'value'     => $this->user->email,
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function users_can_be_retrieved_with_latest_first()
    {
        $this->user->assignRole('super_administrator');

        $user2 = factory(User::class)->create();

        $response = $this->get('/nova-api/users');

        $response->assertJson([
            'label'     => 'Users',
            'resources' => [
                [
                    'id' => [
                        'value' => $user2->id,
                    ],
                ],
                [
                    'id' => [
                        'value' => $this->user->id,
                    ],
                ],
            ],
        ]);
    }

    /** @test **/
    public function user_has_correct_validation_on_create()
    {
        $user = factory(\App\User::class)->make();

        $good_data_response = $this->post('/nova-api/users/', $user->getAttributes());
        $good_data_response->assertStatus(201);
    }

    /** @test **/
    public function name_is_required_on_create()
    {
        $user = $this->post('/nova-api/users/', ['name'=>null]);
        $user->assertStatus(302);
        $user->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);
    }

    /** @test **/
    public function name_must_be_under_255_chars_on_create()
    {
        $user = factory(\App\User::class)->make([
            'name' => str_repeat('J', 256),
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'The name may not be greater than 255 characters.',
        ]);
    }

    /** @test **/
    public function email_is_required_on_create()
    {
        $user = factory(\App\User::class)->make([
            'email' => null,
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
        ]);
    }

    /** @test **/
    public function email_must_be_under_254_chars_on_create()
    {
        $user = factory(\App\User::class)->make([
            'email' => str_repeat('J', 255),
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email may not be greater than 254 characters.',
        ]);
    }

    /** @test **/
    public function email_must_be_valid_on_create()
    {
        $user = factory(\App\User::class)->make([
            'email' => 'johndoe',
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email must be a valid email address.',
        ]);
    }

    /** @test **/
    public function email_must_be_unique_on_create()
    {
        $user = factory(\App\User::class)->create();
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);
    }

    /** @test **/
    public function password_is_required_on_create()
    {
        $user = factory(\App\User::class)->make([
            'password' => null,
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'The password field is required.',
        ]);
    }

    /** @test **/
    public function password_must_be_string_on_create()
    {
        $user = factory(\App\User::class)->make([
            'password' => 123,
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'The password must be a string.',
        ]);
    }

    /** @test **/
    public function password_must_be_6_chars_on_create()
    {
        $user = factory(\App\User::class)->make([
            'password' => 12345,
        ]);
        $response = $this->post('/nova-api/users/', $user->getAttributes());
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'The password must be at least 6 characters.',
        ]);
    }

    /** @test **/
    public function email_must_be_unique_except_for_self_on_update()
    {
        $this->user->assignRole('super_administrator');

        $user = factory(\App\User::class)->make([
            'email' => $this->user->email,
        ]);
        $response = $this->put(
            '/nova-api/users/'.$this->user->id,
            $user->toArray()
        );
        $response->assertStatus(200);
    }

    /** @test **/
    public function email_must_be_unique_on_update()
    {
        $user2 = factory(\App\User::class)->create();
        $user = factory(\App\User::class)->make([
            'email' => $user2->email,
        ]);
        $response = $this->put(
            '/nova-api/users/'.$this->user->id,
            $user->toArray()
        );
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);
    }

    /** @test **/
    public function password_can_be_nullable_on_update()
    {
        $user = factory(\App\User::class)->make([
            'password' => null,
        ]);
        $response = $this->put(
            '/nova-api/users/'.$this->user->id,
            $user->getAttributes()
        );
        $response->assertStatus(200);
    }

    /** @test **/
    public function password_must_be_string_on_update()
    {
        $user = factory(\App\User::class)->make([
            'password' => 123,
        ]);
        $response = $this->put(
            '/nova-api/users/'.$this->user->id,
            $user->getAttributes()
        );
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'The password must be a string.',
        ]);
    }

    /** @test **/
    public function password_must_be_6_chars_on_update()
    {
        $user = factory(\App\User::class)->make([
            'password' => 12345,
        ]);
        $response = $this->put(
            '/nova-api/users/'.$this->user->id,
            $user->getAttributes()
        );
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'The password must be at least 6 characters.',
        ]);
    }
}
