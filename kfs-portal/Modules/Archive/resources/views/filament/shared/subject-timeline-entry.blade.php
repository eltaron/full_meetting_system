@include('filament.shared.subject-timeline', [
    'logs' => \Modules\Archive\Models\ActivityLog::with(['user', 'department'])
        ->where('subject_id', $getRecord()->getKey())
        ->latest('id')
        ->get(),
])
