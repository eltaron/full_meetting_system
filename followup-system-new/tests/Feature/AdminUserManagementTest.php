<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create(['role' => 'admin']);

        $user->assignRole('مشرف عام');

        return $user;
    }

    private function ordinaryUser(): User
    {
        $user = User::factory()->create(['role' => 'staff']);

        $user->assignRole('موظف');

        return $user;
    }

    public function test_guest_is_redirected_away_from_admin_pages(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect('/login');
        $this->get(route('admin.roles.index'))->assertRedirect('/login');
        $this->get(route('admin.permissions.index'))->assertRedirect('/login');
        $this->get(route('admin.user-permissions.index'))->assertRedirect('/login');
    }

    public function test_user_without_manage_permissions_is_forbidden(): void
    {
        $user = $this->ordinaryUser();

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_admin_index_pages_render(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()->assertSee('الموظفون');
        $this->actingAs($admin)->get(route('admin.roles.index'))->assertOk()->assertSee('مشرف عام');
        $this->actingAs($admin)->get(route('admin.permissions.index'))->assertOk()->assertSee('tasks.view');
        $this->actingAs($admin)->get(route('admin.user-permissions.index'))->assertOk();
    }

    public function test_admin_can_create_a_user_with_role_and_roles(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'password' => 'secret-pass-123',
            'password_confirmation' => 'secret-pass-123',
            'employee_full_name' => 'أحمد محمد إبراهيم',
            'phone_number' => '01234567890',
            'job_title' => 'منسق',
            'role' => 'assignment_manager',
            'roles' => ['مسؤول تكليفات'],
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'ahmed@example.com',
            'role' => 'assignment_manager',
            'phone_number' => '01234567890',
        ]);

        $user = User::where('email', 'ahmed@example.com')->first();
        $this->assertTrue($user->hasRole('مسؤول تكليفات'));
        $this->assertTrue($user->hasPermissionTo('tasks.edit'));
        $this->assertTrue(Hash::check('secret-pass-123', $user->password));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_role_with_assigned_users_cannot_be_deleted(): void
    {
        $admin = $this->adminUser();
        $this->ordinaryUser();
        $role = Role::where('name', 'موظف')->first();

        $this->assertDatabaseCount('model_has_roles', 2);

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'مدير متابعة',
            'permissions' => ['tasks.view', 'tasks.edit'],
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'مدير متابعة')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('tasks.view'));
        $this->assertTrue($role->hasPermissionTo('tasks.edit'));
    }

    public function test_new_permission_is_auto_granted_to_admin_role(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('admin.permissions.store'), [
            'name' => 'tasks.approve',
        ])->assertRedirect();

        $permission = Permission::where('name', 'tasks.approve')->first();
        $this->assertNotNull($permission);

        $adminRole = Role::where('name', 'مشرف عام')->first();
        $this->assertTrue($adminRole->hasPermissionTo($permission));
    }

    public function test_permission_name_must_be_module_action(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('admin.permissions.store'), [
            'name' => 'not-a-permission',
        ])->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('permissions', ['name' => 'not-a-permission']);
    }

    public function test_admin_can_sync_direct_user_permissions(): void
    {
        $admin = $this->adminUser();
        $target = $this->ordinaryUser();

        $this->actingAs($admin)->put(route('admin.user-permissions.update', $target), [
            'permissions' => ['tasks.edit'],
        ])->assertRedirect(route('admin.user-permissions.edit', $target));

        $this->assertTrue($target->hasDirectPermission('tasks.edit'));
        $this->assertTrue($target->hasPermissionTo('tasks.edit'));
    }
}
