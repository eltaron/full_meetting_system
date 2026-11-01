<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_assignment_id',
        'signature_officer',
        'signature_officer_id',
        'signature_text',
        'signature_date',
        'note',
    ];

    protected $casts = [
        'signature_date' => 'date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }
    public function officer()
{
    return $this->belongsTo(SignatureOfficer::class, 'signature_officer_id');
}
}
