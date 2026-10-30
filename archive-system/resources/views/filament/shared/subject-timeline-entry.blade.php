@include('filament.shared.subject-timeline', [
    'logs' => \App\Models\ActivityLog::with(['user', 'department'])
        ->where('subject_id', $getRecord()->getKey())
        ->latest('id')
        ->get(),
])
