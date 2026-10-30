<?php

namespace Modules\Followup\Http\Controllers;

use Modules\Followup\Http\Requests\StoreTaskSignatureRequest;
use Modules\Followup\Models\TaskAssignment;
use Modules\Followup\Models\TaskSignature;

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
