<?php

namespace Tests\Feature\Nova\Users;

use Tests\TestCase;

class RoleResourceTest extends TestCase
{
    /** @test **/
    public function it_displays_in_navigation()
    {
        $this->assertTrue(\App\Nova\Role::$displayInNavigation);
    }
}
