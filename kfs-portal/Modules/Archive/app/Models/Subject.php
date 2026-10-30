<?php

namespace Modules\Archive\Models;

use App\Models\User;
use Modules\Archive\Enums\Confidentiality;
use Modules\Archive\Enums\DocumentDirection;
use Modules\Archive\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Subject extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    // حقول الحالة (is_active / deactivated_*) لا تتغير إلا عبر deactivate() و activate()
    protected $fillable = [
        'title',
        'description',
        'document_date',
        'direction',
        'document_type',
        'reference_number',
        'source_id',
        'external_party',
        'confidentiality',
        'paper_location',
        'user_id',
        'department_id',
    ];

    protected $attributes = [
        'is_active' => true,
        'direction' => 'incoming',
        'document_type' => 'letter',
        'confidentiality' => 'normal',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'direction' => DocumentDirection::class,
            'document_type' => DocumentType::class,
            'confidentiality' => Confidentiality::class,
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Subject $subject) {
            $subject->archive_number ??= static::nextArchiveNumber();
        });
    }

    // رقم قيد مسلسل لكل سنة، مثال: KFS-2026-000001
    public static function nextArchiveNumber(): string
    {
        $prefix = config('archive.number_prefix') . '-' . now()->year . '-';

        $last = static::withTrashed()
            ->where('archive_number', 'like', $prefix . '%')
            ->orderByDesc('archive_number')
            ->value('archive_number');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function registerMediaCollections(): void
    {
        // المرفقات تحفظ على قرص خاص ولا تفتح إلا عبر رابط محمي بتسجيل الدخول والصلاحيات
        $this->addMediaCollection('archive_attachments')->useDisk('local');
    }

    public function source()
    {
        // المصدر هو إحدى إدارات المحافظة
        return $this->belongsTo(Department::class, 'source_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * المستندات التي يحق للمستخدم رؤيتها:
     * - مدير المنظومة: الكل
     * - مدير الإدارة: إدارته وكل الأقسام التابعة لها
     * - الموظف: إدارته فقط، عدا "سري للغاية" إلا لو هو من سجّله
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query
            ->whereIn('department_id', $user->accessibleDepartmentIds())
            ->when(! $user->isManager(), fn (Builder $q) => $q->where(
                fn (Builder $q) => $q
                    ->where('confidentiality', '!=', Confidentiality::TopSecret->value)
                    ->orWhere('user_id', $user->id)
            ));
    }

    public function isVisibleTo(User $user): bool
    {
        return static::withTrashed()->visibleTo($user)->whereKey($this->getKey())->exists();
    }

    public function deactivate(User $by, string $reason): void
    {
        $this->forceFill([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivated_by' => $by->id,
            'deactivation_reason' => $reason,
        ])->save();
    }

    public function activate(): void
    {
        $this->forceFill([
            'is_active' => true,
            'deactivated_at' => null,
            'deactivated_by' => null,
            'deactivation_reason' => null,
        ])->save();
    }
}
