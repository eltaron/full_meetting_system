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
        Schema::table('subjects', function (Blueprint $table) {
            // 1. حذف الربط القديم بجدول sources
            $table->dropForeign(['source_id']);

            // 2. تغيير الربط ليشير إلى جدول departments
            $table->foreign('source_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
            $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
        });
    }
};
