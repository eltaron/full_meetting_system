<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Archive\Models\Department;
use Modules\Followup\Models\TaskEntity;
use Modules\Meetings\Models\Category;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $archiveDept = Department::where('name', 'الإدارة العامة للأرشيف')->first();
        $meetingsDept = Department::where('name', 'إدارة الاجتماعات')->first();
        $followupDept = Department::where('name', 'إدارة المتابعة والتكليفات')->first();
        $followupEntity = TaskEntity::where('name', 'إدارة المتابعة والتكليفات')->first();

        $staffCategory = Category::where('slug', 'staff')->first();
        $leadersCategory = Category::where('slug', 'leaders')->first();

        /*
        |----------------------------------------------------------------------
        | مدير البوابة
        |----------------------------------------------------------------------
        */

        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'مدير البوابة',
                'employee_full_name' => 'مدير البوابة الإلكترونية',
                'email' => 'admin@kfs.gov.eg',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'job_title' => 'مدير البوابة الإلكترونية',
                'department_id' => $archiveDept?->id,
                'category_id' => $leadersCategory?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles(['super_admin']);

        /*
        |----------------------------------------------------------------------
        | حسابات تجريبية للأنظمة الثلاثة (تُحذف في الإنتاج)
        |----------------------------------------------------------------------
        */

        $demos = [
            [
                'username' => 'archive.demo',
                'name' => 'مسؤول الأرشيف',
                'email' => 'archive.demo@kfs.gov.eg',
                'role' => 'manager',
                'job_title' => 'مدير الإدارة العامة للأرشيف',
                'department_id' => $archiveDept?->id,
                'category_id' => $staffCategory?->id,
                'roles' => ['archive_manager'],
            ],
            [
                'username' => 'meetings.demo',
                'name' => 'مسؤول الاجتماعات',
                'email' => 'meetings.demo@kfs.gov.eg',
                'role' => 'staff',
                'job_title' => 'أخصائي اجتماعات',
                'department_id' => $meetingsDept?->id,
                'category_id' => $staffCategory?->id,
                'roles' => ['meetings_staff'],
            ],
            [
                'username' => 'followup.demo',
                'name' => 'مسؤول المتابعة',
                'email' => 'followup.demo@kfs.gov.eg',
                'role' => 'staff',
                'job_title' => 'أخصائي متابعة وتكليفات',
                'department_id' => $followupDept?->id,
                'entity_id' => $followupEntity?->id,
                'category_id' => $staffCategory?->id,
                'roles' => ['followup_staff'],
            ],
        ];

        foreach ($demos as $demo) {
            $roles = $demo['roles'];
            unset($demo['roles']);

            $user = User::updateOrCreate(
                ['username' => $demo['username']],
                array_merge($demo, [
                    'password' => Hash::make('Demo@12345'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]),
            );

            $user->syncRoles($roles);
        }
    }
}
