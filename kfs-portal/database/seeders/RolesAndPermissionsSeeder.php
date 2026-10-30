<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |----------------------------------------------------------------------
        | الصلاحيات
        |----------------------------------------------------------------------
        */

        $systemPermissions = [
            'system.archive.access',
            'system.meetings.access',
            'system.followup.access',
        ];

        $corePermissions = [
            'users.manage',
            'roles.manage',
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
        ];

        $meetingPermissions = [];

        foreach (['meeting', 'category', 'room', 'user', 'role'] as $model) {
            foreach ([
                'view_any', 'view', 'create', 'update', 'delete', 'delete_any',
                'force_delete', 'force_delete_any', 'restore', 'restore_any',
                'replicate', 'reorder',
            ] as $action) {
                $meetingPermissions[] = "{$action}_{$model}";
            }
        }

        $allPermissions = array_values(array_unique(array_merge(
            $systemPermissions,
            $corePermissions,
            $meetingPermissions,
        )));

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        /*
        |----------------------------------------------------------------------
        | الأدوار
        |----------------------------------------------------------------------
        */

        $roles = [
            'super_admin' => $allPermissions,

            'portal_admin' => array_merge($systemPermissions, $corePermissions, $meetingPermissions),

            'archive_admin' => ['system.archive.access'],
            'archive_manager' => ['system.archive.access'],
            'archive_staff' => ['system.archive.access'],

            'meetings_admin' => array_merge(['system.meetings.access'], $meetingPermissions),
            'meetings_manager' => array_merge(['system.meetings.access'], [
                'view_any_meeting', 'view_meeting', 'create_meeting', 'update_meeting', 'delete_meeting',
                'view_any_category', 'view_category',
                'view_any_room', 'view_room',
                'view_any_user', 'view_user',
            ]),
            'meetings_staff' => array_merge(['system.meetings.access'], [
                'view_any_meeting', 'view_meeting', 'create_meeting', 'update_meeting',
                'view_any_room', 'view_room',
            ]),

            'followup_manager' => array_merge(['system.followup.access'], [
                'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            ]),
            'followup_supervisor' => array_merge(['system.followup.access'], [
                'tasks.view', 'tasks.create', 'tasks.edit',
            ]),
            'followup_staff' => array_merge(['system.followup.access'], [
                'tasks.view', 'tasks.create', 'tasks.edit',
            ]),

            // حساب جديد لم يُعتمد بعد — لا يملك أي صلاحية.
            'pending_user' => [],
        ];

        foreach ($roles as $name => $permissions) {
            $role = Role::findOrCreate($name, 'web');

            if ($name === 'super_admin') {
                // يتجاوز كل الصلاحيات عبر Gate::before، لكن نمنحه الكل أيضاً.
                $role->syncPermissions(Permission::all());
                continue;
            }

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
