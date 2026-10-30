<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الموضوع
            $table->text('description')->nullable(); // وصف الموضوع
            $table->date('document_date')->nullable(); // تاريخ الوثيقة

            // العلاقات
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete(); // الجهة
            $table->foreignId('user_id')->constrained('users'); // الموظف الذي أدخل البيانات
            $table->foreignId('department_id')->constrained('departments'); // الإدارة التي ينتمي لها الموضوع

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
