<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserRoleTest extends TestCase
{
    /** @test **/
    public function user_can_be_assigned_roles()
    {
        $this->user->assignRole('super_administrator');

        $this->assertTrue($this->user->hasRole('super_administrator'));
    }
}
