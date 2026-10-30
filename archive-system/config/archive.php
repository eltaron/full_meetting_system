<?php

return [
    // اسم الجهة الحكومية كما يظهر في الواجهة والمطبوعات
    'organization' => env('ARCHIVE_ORGANIZATION', 'محافظة كفر الشيخ'),

    // اسم المنظومة
    'system_name' => env('ARCHIVE_SYSTEM_NAME', 'منظومة الأرشيف الإلكتروني'),

    // بادئة رقم القيد الأرشيفي، مثال: KFS-2026-000001
    'number_prefix' => env('ARCHIVE_NUMBER_PREFIX', 'KFS'),
];
