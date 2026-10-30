<?php

namespace Modules\Archive\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTIONS = [
        'login' => 'تسجيل دخول',
        'created' => 'أرشفة مستند جديد',
        'updated' => 'تعديل بيانات مستند',
        'viewed' => 'عرض بيانات مستند',
        'deactivated' => 'إلغاء تفعيل مستند',
        'activated' => 'إعادة تفعيل مستند',
        'viewed_attachment' => 'فتح مرفق',
        'deleted' => 'حذف مستند',
        'restored' => 'استرجاع مستند محذوف',
        'force_deleted' => 'حذف نهائي لمستند',
    ];

    public const ACTION_COLORS = [
        'login' => 'gray',
        'created' => 'success',
        'updated' => 'info',
        'viewed' => 'gray',
        'deactivated' => 'warning',
        'activated' => 'success',
        'viewed_attachment' => 'gray',
        'deleted' => 'danger',
        'restored' => 'info',
        'force_deleted' => 'danger',
    ];

    public const FIELD_LABELS = [
        'title' => 'العنوان',
        'description' => 'الملخص',
        'document_date' => 'تاريخ المستند',
        'direction' => 'الاتجاه',
        'document_type' => 'نوع المستند',
        'reference_number' => 'رقم الصادر/الوارد',
        'source_id' => 'الجهة الداخلية',
        'external_party' => 'الجهة الخارجية',
        'confidentiality' => 'درجة السرية',
        'paper_location' => 'مكان الأصل الورقي',
        'department_id' => 'الإدارة المالكة',
        'user_id' => 'الموظف',
    ];

    protected $fillable = [
        'user_id',
        'department_id',
        'action',
        'subject_id',
        'description',
        'properties',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    public static function record(string $action, ?Subject $subject = null, array $properties = [], ?User $user = null): self
    {
        $user ??= auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'department_id' => $user?->department_id,
            'action' => $action,
            'subject_id' => $subject?->id,
            'description' => $subject ? "{$subject->archive_number} - {$subject->title}" : null,
            'properties' => $properties ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }
}
