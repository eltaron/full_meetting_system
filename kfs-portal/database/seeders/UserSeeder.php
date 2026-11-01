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
        | حسابات تجريبية: حساب واحد لكل دور/صلاحية (تُحذف في الإنتاج)
        | كل الحسابات التالية لكلمة المرور Demo@12345
        |----------------------------------------------------------------------
        */

        $demos = [
            [
                'username' => 'super.admin',
                'name' => 'مدير المنظومة (نسخة اختبار)',
                'email' => 'super.admin@kfs.gov.eg',
                'role' => 'admin',
                'job_title' => 'مدير المنظومة',
                'department_id' => $archiveDept?->id,
                'category_id' => $leadersCategory?->id,
                'roles' => ['super_admin'],
            ],
            [
                'username' => 'portal.admin',
                'name' => 'مسؤول البوابة',
                'email' => 'portal.admin@kfs.gov.eg',
                'role' => 'admin',
                'job_title' => 'مسؤول البوابة الإلكترونية',
                'department_id' => $archiveDept?->id,
                'category_id' => $leadersCategory?->id,
                'roles' => ['portal_admin'],
            ],

            ['username' => 'archive.admin', 'name' => 'مدير الأرشيف', 'email' => 'archive.admin@kfs.gov.eg', 'role' => 'admin', 'job_title' => 'مدير الأرشيف', 'department_id' => $archiveDept?->id, 'category_id' => $leadersCategory?->id, 'roles' => ['archive_admin']],
            ['username' => 'archive.manager', 'name' => 'مسؤول أرشيف', 'email' => 'archive.manager@kfs.gov.eg', 'role' => 'manager', 'job_title' => 'مسؤول الأرشيف', 'department_id' => $archiveDept?->id, 'category_id' => $staffCategory?->id, 'roles' => ['archive_manager']],
            ['username' => 'archive.staff', 'name' => 'موظف أرشيف', 'email' => 'archive.staff@kfs.gov.eg', 'role' => 'staff', 'job_title' => 'موظف أرشيف', 'department_id' => $archiveDept?->id, 'category_id' => $staffCategory?->id, 'roles' => ['archive_staff']],

            ['username' => 'meetings.admin', 'name' => 'مدير الاجتماعات', 'email' => 'meetings.admin@kfs.gov.eg', 'role' => 'admin', 'job_title' => 'مدير اجتماعات', 'department_id' => $meetingsDept?->id, 'category_id' => $leadersCategory?->id, 'roles' => ['meetings_admin']],
            ['username' => 'meetings.manager', 'name' => 'مسؤول اجتماعات', 'email' => 'meetings.manager@kfs.gov.eg', 'role' => 'manager', 'job_title' => 'مسؤول اجتماعات', 'department_id' => $meetingsDept?->id, 'category_id' => $staffCategory?->id, 'roles' => ['meetings_manager']],
            ['username' => 'meetings.staff', 'name' => 'موظف اجتماعات', 'email' => 'meetings.staff@kfs.gov.eg', 'role' => 'staff', 'job_title' => 'موظف اجتماعات', 'department_id' => $meetingsDept?->id, 'category_id' => $staffCategory?->id, 'roles' => ['meetings_staff']],

            ['username' => 'followup.manager', 'name' => 'مدير المتابعة', 'email' => 'followup.manager@kfs.gov.eg', 'role' => 'assignment_manager', 'job_title' => 'مدير المتابعة والتكليفات', 'department_id' => $followupDept?->id, 'entity_id' => $followupEntity?->id, 'category_id' => $leadersCategory?->id, 'roles' => ['followup_manager']],
            ['username' => 'followup.supervisor', 'name' => 'مشرف متابعة', 'email' => 'followup.supervisor@kfs.gov.eg', 'role' => 'supervisor', 'job_title' => 'مشرف المتابعة', 'department_id' => $followupDept?->id, 'entity_id' => $followupEntity?->id, 'category_id' => $staffCategory?->id, 'roles' => ['followup_supervisor']],
            ['username' => 'followup.staff', 'name' => 'موظف متابعة', 'email' => 'followup.staff@kfs.gov.eg', 'role' => 'staff', 'job_title' => 'موظف متابعة وتكليفات', 'department_id' => $followupDept?->id, 'entity_id' => $followupEntity?->id, 'category_id' => $staffCategory?->id, 'roles' => ['followup_staff']],

            // حساب لم يُعتمد بعد: لا يستطيع الدخول حتى يُفعّله مدير البوابة.
            ['username' => 'pending.user', 'name' => 'حساب قيد المراجعة', 'email' => 'pending.user@kfs.gov.eg', 'role' => 'staff', 'job_title' => 'موظف جديد', 'category_id' => $staffCategory?->id, 'roles' => ['pending_user'], 'active' => false],
        ];

        foreach ($demos as $demo) {
            $roles = $demo['roles'];
            $active = $demo['active'] ?? true;
            unset($demo['roles'], $demo['active']);

            $user = User::updateOrCreate(
                ['username' => $demo['username']],
                array_merge($demo, [
                    'password' => Hash::make('Demo@12345'),
                    'is_active' => $active,
                    'email_verified_at' => $active ? now() : null,
                ]),
            );

            $user->syncRoles($roles);
        }
    }
}
