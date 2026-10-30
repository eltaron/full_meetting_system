<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * المستخدم الموحّد للبوابة.
 *
 * يجمع حقول الأنظمة الثلاثة (الأرشيف / الاجتماعات / التكليفات) في جدول
 * users واحد، وتُدار صلاحية الدخول لكل نظام عبر أدوار Spatie
 * (system.archive.access ... إلخ) بدل أدوار منفصلة لكل نظام.
 */
class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'department_id',
        'category_id',
        'role',
        'job_title',
        'job_grade',
        'employee_full_name',
        'national_id',
        'phone',
        'phone_number',
        'user_type',
        'picture_path',
        'manager_id',
        'entity_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'role' => 'staff',
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Relations — Archive                                                */
    /* ------------------------------------------------------------------ */

    public function department(): BelongsTo
    {
        return $this->belongsTo(\Modules\Archive\Models\Department::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(\Modules\Archive\Models\Subject::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(\Modules\Archive\Models\ActivityLog::class);
    }

    /* ------------------------------------------------------------------ */
    /* Relations — Meetings                                               */
    /* ------------------------------------------------------------------ */

    public function category(): BelongsTo
    {
        return $this->belongsTo(\Modules\Meetings\Models\Category::class);
    }

    public function createdMeetings(): HasMany
    {
        return $this->hasMany(\Modules\Meetings\Models\Meeting::class, 'created_by');
    }

    public function attendingMeetings(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Meetings\Models\Meeting::class);
    }

    /* ------------------------------------------------------------------ */
    /* Relations — Follow-up                                              */
    /* ------------------------------------------------------------------ */

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(\Modules\Followup\Models\TaskAssignment::class, 'assignee_id');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(\Modules\Followup\Models\TaskAssignment::class, 'created_by');
    }

    /* ------------------------------------------------------------------ */
    /* System access (the whole point of the unified portal)              */
    /* ------------------------------------------------------------------ */

    public const SYSTEMS = [
        'archive' => [
            'label' => 'الأرشيف الإلكتروني',
            'permission' => 'system.archive.access',
            'url' => '/archive',
            'icon' => 'heroicon-o-archive-box',
        ],
        'meetings' => [
            'label' => 'الاجتماعات',
            'permission' => 'system.meetings.access',
            'url' => '/meetings',
            'icon' => 'heroicon-o-calendar-days',
        ],
        'followup' => [
            'label' => 'التكليفات والمتابعة',
            'permission' => 'system.followup.access',
            'url' => '/followup/dashboard',
            'icon' => 'heroicon-o-clipboard-document-check',
        ],
    ];

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function hasSystem(string $system): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permission = self::SYSTEMS[$system]['permission'] ?? null;

        return $permission !== null && $this->can($permission);
    }

    /**
     * الأنظمة التي يملك المستخدم تصريحاً بالدخول إليها.
     *
     * @return array<string, array<string, string>>
     */
    public function accessibleSystems(): array
    {
        return array_filter(
            self::SYSTEMS,
            fn (array $system, string $key): bool => $this->hasSystem($key),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /* ------------------------------------------------------------------ */
    /* Legacy role helpers (استخدام الأنظمة القديمة)                       */
    /* ------------------------------------------------------------------ */

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin()
            || in_array($this->role, ['admin'], true)
            || $this->hasAnyRole(['super_admin', 'portal_admin', 'archive_admin']);
    }

    public function isArchiveAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->hasRole('archive_admin');
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['manager', 'director', 'admin', 'assignment_manager'], true)
            || $this->hasAnyRole(['archive_manager', 'followup_manager']);
    }

    public function isAssignmentManager(): bool
    {
        return $this->role === 'assignment_manager' || $this->hasRole('followup_manager');
    }

    /**
     * الإدارات التي يحق للمستخدم رؤية أرشيفها.
     */
    public function accessibleDepartmentIds(): array
    {
        if (! $this->department_id || ! $this->department) {
            return [];
        }

        if ($this->isManager()) {
            return $this->department->withDescendantIds();
        }

        return [$this->department_id];
    }

    /** كل مرؤوسي هذا المستخدم بشكل متسلسل. */
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

    public function visibleTasksQuery()
    {
        $query = \Modules\Followup\Models\TaskAssignment::query();

        return match ($this->role) {
            'admin', 'director', 'assignment_manager' => $query,
            'manager' => $query->whereIn('assignee_id', array_merge([$this->id], $this->allSubordinateIds())),
            'supervisor' => $query->where('entity_id', $this->entity_id),
            default => $query->where('assignee_id', $this->id),
        };
    }

    /* ------------------------------------------------------------------ */
    /* Filament                                                           */
    /* ------------------------------------------------------------------ */

    public function getFilamentName(): string
    {
        return $this->username ? "{$this->name} ({$this->username})" : $this->name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return match ($panel->getId()) {
            'portal' => true,
            'archive', 'archive-staff' => $this->hasSystem('archive'),
            'meetings' => $this->hasSystem('meetings'),
            default => false,
        };
    }
}
