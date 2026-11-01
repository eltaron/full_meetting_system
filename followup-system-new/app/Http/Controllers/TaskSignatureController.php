<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskSignatureRequest;
use App\Models\TaskAssignment;
use App\Models\TaskSignature;

class TaskSignatureController extends Controller
{
    public function store(StoreTaskSignatureRequest $request, TaskAssignment $task)
    {
        $this->authorize('update', $task);

        TaskSignature::create([
            'task_assignment_id' => $task->id,
            'signature_officer_id' => $request->signature_officer_id,
            'signature_text' => $request->signature_text,
            'signature_date' => $request->signature_date,
            'note' => $request->note,
        ]);

        return redirect()->route('tasks.edit', $task)->with('success', 'تم إضافة التأشيرة بنجاح.');
    }
}
