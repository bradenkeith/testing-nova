<?php

namespace Tests\Feature;

use Tests\TestCase;

class NovaAccessTest extends TestCase
{
    /** @test **/
    public function super_admin_has_ui_access()
    {
        $this->user->assignRole('super_administrator');

        $response = $this->get('/nova-api/users/1');

        $response->assertStatus(200);
    }

    /** @test **/
    public function basic_user_does_not_have_ui_access()
    {
        $response = $this->get('/nova-api/users/1');

        $response->assertStatus(403);
    }

    /** @test **/
    public function app_is_named_correctly()
    {
        $this->assertEquals(config('nova.name'), 'Example Custom Name');
    }

    /** @test **/
    public function app_is_at_the_correct_url()
    {
        $this->assertEquals(config('nova.path'), '/admin');
    }
}
