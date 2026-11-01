<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignment_id')->constrained('task_assignments')->cascadeOnDelete();
            $table->string('signature_officer'); // مسئول التأشيرة (نص حر)
            $table->text('signature_text')->nullable(); // نص التأشيرة
            $table->date('signature_date')->nullable(); // تاريخ التأشيرة
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['task_assignment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_signatures');
    }
};
