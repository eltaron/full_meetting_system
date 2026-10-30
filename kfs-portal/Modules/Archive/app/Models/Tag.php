<?php

namespace Modules\Archive\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'color'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
}
