<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{

    use LazilyRefreshDatabase;

    /** @test */
    public function can_assign_permissions_to_user()
    {
        $user = User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionsTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }

    /** @test */
    public function cannot_assign_the_same_permission_twice()
    {
        $user = User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionsTo($permission);
        $user->givePermissionsTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }
}
