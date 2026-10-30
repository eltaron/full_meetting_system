<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * رقم الإدارة + أرقام كل الأقسام التابعة لها على كل المستويات
     */
    public function withDescendantIds(): array
    {
        $ids = [$this->id];
        $level = [$this->id];

        while (! empty($level)) {
            $level = static::whereIn('parent_id', $level)->whereNotIn('id', $ids)->pluck('id')->all();
            $ids = array_merge($ids, $level);
        }

        return $ids;
    }

    // لا يُسمح بحذف إدارة عليها موظفين أو مستندات أو أقسام تابعة
    public function isDeletable(): bool
    {
        return ! $this->users()->exists()
            && ! $this->children()->exists()
            && ! Subject::withTrashed()
                ->where(fn ($q) => $q->where('department_id', $this->id)->orWhere('source_id', $this->id))
                ->exists();
    }
}
