<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_officers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // اسم مسئول التأشيرة
            $table->string('title')->nullable();          // المسمى الوظيفي (اختياري)
            $table->boolean('is_active')->default(true);  // لإخفائه من القائمة بدون حذفه
            $table->timestamps();

            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_officers');
    }
};