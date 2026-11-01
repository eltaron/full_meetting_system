<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignatureOfficer extends Model
{
    protected $fillable = ['name', 'title', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function signatures()
    {
        return $this->hasMany(TaskSignature::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
