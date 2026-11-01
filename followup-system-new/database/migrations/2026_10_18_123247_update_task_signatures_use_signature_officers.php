<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_signatures', function (Blueprint $table) {
            // نحذف أي ربط قديم بجدول users أو عمود النص الحر
            if (Schema::hasColumn('task_signatures', 'signature_officer')) {
                $table->dropColumn('signature_officer');
            }
        });

        Schema::table('task_signatures', function (Blueprint $table) {
            if (Schema::hasColumn('task_signatures', 'signature_officer_id')) {
                // لو كان العمود موجود وبيشير على users نشيل الـ FK القديم
                try { $table->dropForeign(['signature_officer_id']); } catch (\Throwable $e) {}
                $table->dropColumn('signature_officer_id');
            }
        });

        Schema::table('task_signatures', function (Blueprint $table) {
            $table->foreignId('signature_officer_id')
                ->nullable()
                ->after('task_assignment_id')
                ->constrained('signature_officers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('task_signatures', function (Blueprint $table) {
            $table->dropForeign(['signature_officer_id']);
            $table->dropColumn('signature_officer_id');
            $table->string('signature_officer')->nullable();
        });
    }
};