<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** القيم القديمة -> القيم الجديدة للأولوية */
    private array $map = [
        'عالية' => 'عاجل',
        'متوسطة' => 'عادي',
        'منخفضة' => 'عادي',
    ];

    public function up(): void
    {
        $this->remapOldValues();

        // إعادة بناء الجدول (على SQLite تحديداً) بتحيّد قيود الـ check عن باقي الأعمدة،
        // فبنعيد تعريفها هنا عشان نضمن بقاءها سليمة.
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->enum('priority', ['سري', 'عاجل', 'هام وعاجل', 'عادي'])
                ->default('عادي')
                ->change();
            $table->enum('status', ['لم يبدأ', 'جاري التنفيذ', 'تم التنفيذ', 'متأخر', 'متوقف'])
                ->default('لم يبدأ')
                ->change();
            $table->enum('document_type', ['وارد', 'صادر'])
                ->default('وارد')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->string('priority')->default('متوسطة')->change();
        });

        DB::table('task_assignments')->whereIn('priority', ['سري', 'عاجل', 'هام وعاجل'])
            ->update(['priority' => 'عالية']);
        DB::table('task_assignments')->where('priority', 'عادي')
            ->update(['priority' => 'متوسطة']);

        Schema::table('task_assignments', function (Blueprint $table) {
            $table->enum('priority', ['عالية', 'متوسطة', 'منخفضة'])
                ->default('متوسطة')
                ->change();
        });
    }

    /**
     * نحوّل العمود لنوع نصي بسيط أولاً (بدون قيد check)، ثم نعيد تعيين القيم
     * القديمة للجديدة حتى لا يفشل القيد الجديد عند إعادة بناء الجدول.
     */
    private function remapOldValues(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->string('priority')->default('عادي')->change();
        });

        foreach ($this->map as $old => $new) {
            DB::table('task_assignments')->where('priority', $old)->update(['priority' => $new]);
        }
    }
};
