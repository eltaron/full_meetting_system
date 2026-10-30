<?php

namespace Modules\Archive\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = ['name'];

    public function subjects() {
        return $this->hasMany(Subject::class);
    }
}
