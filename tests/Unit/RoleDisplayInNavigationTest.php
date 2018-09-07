<?php

namespace Tests\Unit;

use Tests\TestCase;

class RoleDisplayInNavigationTest extends TestCase
{
    /** @test **/
    public function role_links_display_in_navigation()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $novaRole = new \App\Nova\Role($role);
        $this->assertTrue($novaRole::$displayInNavigation);
    }
}
