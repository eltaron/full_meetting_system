<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * الأعمدة المسموح تعبئتها جماعياً (Mass Assignment).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_full_name',
        'national_id',
        'phone_number',
        'job_title',
        'job_grade',
        'user_type',
        'picture_path',
        'role',
        'manager_id',
        'entity_id',
    ];

    /**
     * الأعمدة اللي متتخفيش وقت التحويل لـ Array/JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع الأعمدة تلقائياً.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ------------------------------------------------------------
    // العلاقات
    // ------------------------------------------------------------

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    /** كل مرؤوسي هذا المستخدم بشكل متسلسل (مرؤوسين المرؤوسين... إلخ) */
    public function allSubordinateIds(): array
    {
        $ids = [];
        $queue = $this->subordinates()->pluck('id')->all();

        while (! empty($queue)) {
            $ids = array_merge($ids, $queue);
            $queue = self::whereIn('manager_id', $queue)->pluck('id')->all();
        }

        return $ids;
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assignee_id');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'created_by');
    }

    // ------------------------------------------------------------
    // Helpers للأدوار
    // ------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAssignmentManager(): bool
    {
        return $this->role === 'assignment_manager';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['manager', 'director', 'admin'], true);
    }

    /**
     * الاستعلام الأساسي: أي التكليفات يقدر هذا المستخدم يشوفها،
     * حسب دوره في الهيكل الإداري.
     */
    public function visibleTasksQuery()
    {
        $query = TaskAssignment::query();

        return match ($this->role) {
            'admin', 'director', 'assignment_manager' => $query, // كل التكليفات
            'manager' => $query->whereIn('assignee_id', array_merge([$this->id], $this->allSubordinateIds())),
            'supervisor' => $query->where('entity_id', $this->entity_id),
            default => $query->where('assignee_id', $this->id), // staff: تكليفاته هو بس
        };
    }
}
