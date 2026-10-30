<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // حذف إدارة لا يجب أن يمسح المستندات الواردة منها
            $table->dropForeign(['source_id']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('source_id')->nullable()->change();
            $table->foreign('source_id')->references('id')->on('departments')->restrictOnDelete();

            $table->string('archive_number')->nullable()->unique()->after('id'); // رقم القيد الأرشيفي
            $table->string('direction')->default('incoming')->after('archive_number'); // وارد | صادر
            $table->string('document_type')->default('letter')->after('direction'); // نوع المستند
            $table->string('reference_number')->nullable()->after('document_type'); // رقم الصادر/الوارد على المكاتبة
            $table->string('external_party')->nullable()->after('source_id'); // جهة خارجية (وزارة، هيئة، مواطن...)
            $table->string('confidentiality')->default('normal'); // درجة السرية
            $table->string('paper_location')->nullable(); // مكان حفظ الأصل الورقي

            // إلغاء التفعيل (بديل الحذف للموظفين)
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();
            $table->foreignId('deactivated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('deactivation_reason')->nullable();

            $table->softDeletes(); // الحذف من مدير المنظومة فقط وقابل للاسترجاع

            $table->index(['department_id', 'is_active']);
            $table->index('document_date');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['deactivated_by']);
            $table->dropIndex(['department_id', 'is_active']);
            $table->dropIndex(['document_date']);
            $table->dropColumn([
                'archive_number', 'direction', 'document_type', 'reference_number', 'external_party',
                'confidentiality', 'paper_location', 'is_active', 'deactivated_at', 'deactivated_by',
                'deactivation_reason', 'deleted_at',
            ]);
        });
    }
};
