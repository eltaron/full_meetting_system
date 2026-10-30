<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('username'); // admin | manager | employee
            $table->string('job_title')->nullable()->after('role'); // المسمى الوظيفي
            $table->boolean('is_active')->default(true)->after('job_title'); // إيقاف حساب الموظف بدلاً من حذفه
            $table->timestamp('last_login_at')->nullable();
        });

        // الحسابات بدون إدارة كانت هي حسابات الإدارة العامة
        DB::table('users')->whereNull('department_id')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'job_title', 'is_active', 'last_login_at']);
        });
    }
};
