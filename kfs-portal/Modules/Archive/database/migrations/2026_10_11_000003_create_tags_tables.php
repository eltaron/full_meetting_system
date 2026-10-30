<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // التصنيف / الكلمة الدالة (مثال: تراخيص، تعديات، موازنة)
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('subject_tag', function (Blueprint $table) {
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['subject_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_tag');
        Schema::dropIfExists('tags');
    }
};
