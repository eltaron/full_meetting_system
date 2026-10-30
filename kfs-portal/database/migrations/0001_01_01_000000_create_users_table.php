<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول المستخدمين الموحّد للبوابة.
 *
 * يدمج حقول الأنظمة الثلاثة (الأرشيف / الاجتماعات / التكليفات) في جدول واحد،
 * مع الحفاظ على الحقول القديمة حتى تعمل الشاشات المنقولة كما هي.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // الهوية
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('employee_full_name')->nullable();
            $table->string('national_id')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // الترتيب الإداري القديم
            $table->string('role')->default('staff')->index(); // admin|director|manager|assignment_manager|supervisor|staff
            $table->string('job_title')->nullable();
            $table->string('job_grade')->nullable();
            $table->string('user_type')->nullable();

            // الاتصال والصورة
            $table->string('phone')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('picture_path')->nullable();

            // الروابط الإدارية
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('entity_id')->nullable()->index();

            // الحالة
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_login_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
