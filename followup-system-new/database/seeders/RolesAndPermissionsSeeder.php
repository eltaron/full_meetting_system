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
        // تصفير كاش الصلاحيات قبل ما نبدأ
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // -------------------------------------------------------------
        // الصلاحيات (Permissions)
        // -------------------------------------------------------------

        // موديول متابعة التكليفات
        $taskPermissions = [
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
        ];

        // موديول إدارة المستخدمين والصلاحيات (للمسؤولين فقط)
        $adminPermissions = [
            'users.manage',
            'roles.manage',
        ];

        foreach (array_merge($taskPermissions, $adminPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // -------------------------------------------------------------
        // الأدوار (Roles) وربطها بالصلاحيات المناسبة
        // -------------------------------------------------------------

        // 1) مشرف عام - كل الصلاحيات (أدوار وإدارة مستخدمين)
        $admin = Role::firstOrCreate(['name' => 'مشرف عام']);
        $admin->syncPermissions(Permission::all());

        // 2) مسؤول التكليفات - كل صلاحيات التكليفات
        $assignmentManager = Role::firstOrCreate(['name' => 'مسؤول تكليفات']);
        $assignmentManager->syncPermissions($taskPermissions);

        // 3) مدير - عرض فقط
        $manager = Role::firstOrCreate(['name' => 'مدير']);
        $manager->syncPermissions(['tasks.view']);

        // 4) موظف - عرض فقط
        $staff = Role::firstOrCreate(['name' => 'موظف']);
        $staff->syncPermissions(['tasks.view']);
    }
}
