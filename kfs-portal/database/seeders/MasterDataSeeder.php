<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Archive\Models\Department;
use Modules\Followup\Models\TaskEntity;
use Modules\Followup\Models\TaskSource;
use Modules\Meetings\Models\Category;
use Modules\Meetings\Models\Room;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |----------------------------------------------------------------------
        | الإدارات (شجرة هرمية)
        |----------------------------------------------------------------------
        */

        $root = Department::firstOrCreate(['name' => 'ديوان عام المحافظة']);

        $departments = [
            'الإدارة العامة للأرشيف',
            'الأمانة العامة',
            'إدارة الاجتماعات',
            'إدارة المتابعة والتكليفات',
            'الإدارة العامة للموارد البشرية',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(
                ['name' => $name],
                ['parent_id' => $root->id],
            );
        }

        /*
        |----------------------------------------------------------------------
        | تصنيفات المستخدمين (تُستخدم في منظومة الاجتماعات)
        |----------------------------------------------------------------------
        */

        foreach ([
            ['name' => 'القيادات', 'slug' => 'leaders'],
            ['name' => 'المديرون', 'slug' => 'managers'],
            ['name' => 'الموظفون', 'slug' => 'staff'],
        ] as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }

        /*
        |----------------------------------------------------------------------
        | القاعات
        |----------------------------------------------------------------------
        */

        foreach ([
            ['name' => 'قاعة الاجتماعات الكبرى', 'location' => 'الدور الأول — ديوان عام المحافظة', 'capacity' => 40],
            ['name' => 'قاعة المجلس التنفيذي', 'location' => 'الدور الثاني — ديوان عام المحافظة', 'capacity' => 20],
            ['name' => 'قاعة الاجتماعات الصغرى', 'location' => 'الدور الأرضي — ديوان عام المحافظة', 'capacity' => 10],
        ] as $room) {
            Room::firstOrCreate(
                ['name' => $room['name']],
                ['location' => $room['location'], 'capacity' => $room['capacity'], 'is_active' => true],
            );
        }

        /*
        |----------------------------------------------------------------------
        | مصادر وجهات التكليفات
        |----------------------------------------------------------------------
        */

        foreach ([
            'مذكرة رسمية',
            'بريد إلكتروني',
            'اجتماع',
            'اتصال هاتفي',
            'خطاب وارد',
        ] as $source) {
            TaskSource::firstOrCreate(['name' => $source], ['is_active' => true]);
        }

        foreach ([
            ['name' => 'ديوان عام المحافظة', 'type' => 'إدارة'],
            ['name' => 'الإدارة العامة للأرشيف', 'type' => 'إدارة'],
            ['name' => 'الأمانة العامة', 'type' => 'إدارة'],
            ['name' => 'إدارة الاجتماعات', 'type' => 'إدارة'],
            ['name' => 'إدارة المتابعة والتكليفات', 'type' => 'إدارة'],
            ['name' => 'مديرية التربية والتعليم', 'type' => 'مديرية'],
            ['name' => 'مديرية الصحة', 'type' => 'مديرية'],
        ] as $entity) {
            TaskEntity::firstOrCreate(
                ['name' => $entity['name']],
                ['type' => $entity['type'], 'is_active' => true],
            );
        }
    }
}
