@php
    use Modules\Archive\Models\ActivityLog;

    $icons = [
        'created' => 'heroicon-m-plus-circle',
        'updated' => 'heroicon-m-pencil-square',
        'viewed' => 'heroicon-m-eye',
        'viewed_attachment' => 'heroicon-m-paper-clip',
        'deactivated' => 'heroicon-m-no-symbol',
        'activated' => 'heroicon-m-arrow-path',
        'deleted' => 'heroicon-m-trash',
        'restored' => 'heroicon-m-arrow-uturn-left',
        'force_deleted' => 'heroicon-m-x-circle',
    ];
@endphp

@if ($logs->isEmpty())
    <div class="arc-empty">لا توجد حركات مسجلة على هذا المستند</div>
@else
    <ol class="arc-timeline">
        @foreach ($logs as $log)
            @php
                $color = ActivityLog::ACTION_COLORS[$log->action] ?? 'gray';
                $changes = $log->properties['changes'] ?? [];
            @endphp
            <li class="arc-timeline-item">
                <span class="arc-timeline-dot arc-dot-{{ $color }}">
                    <x-filament::icon :icon="$icons[$log->action] ?? 'heroicon-m-bolt'" class="arc-timeline-icon" />
                </span>

                <div class="arc-timeline-head">
                    <x-filament::badge :color="$color">
                        {{ ActivityLog::ACTIONS[$log->action] ?? $log->action }}
                    </x-filament::badge>
                    <strong>{{ $log->user?->name ?? 'مستخدم محذوف' }}</strong>
                    @if ($log->department)
                        <span class="arc-muted">({{ $log->department->name }})</span>
                    @endif
                    <time class="arc-muted arc-time" title="{{ $log->created_at }}">
                        {{ $log->created_at->format('Y-m-d h:i A') }} · {{ $log->created_at->diffForHumans() }}
                    </time>
                </div>

                @if ($log->action === 'deactivated' && ! empty($log->properties['reason']))
                    <p class="arc-reason">السبب: {{ $log->properties['reason'] }}</p>
                @endif

                @if ($log->action === 'viewed_attachment' && ! empty($log->properties['file']))
                    <p class="arc-muted">
                        الملف: {{ $log->properties['file'] }} {{ ! empty($log->properties['download']) ? '(تحميل)' : '(عرض)' }}
                    </p>
                @endif

                @if ($changes)
                    <table class="arc-changes">
                        <thead>
                            <tr><th>الحقل</th><th>قبل</th><th>بعد</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($changes as $field => $change)
                                <tr>
                                    <td><strong>{{ ActivityLog::FIELD_LABELS[$field] ?? $field }}</strong></td>
                                    <td class="arc-old">{{ $change['old'] ?? '-' }}</td>
                                    <td class="arc-new">{{ $change['new'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if ($log->ip_address)
                    <p class="arc-ip">IP: {{ $log->ip_address }}</p>
                @endif
            </li>
        @endforeach
    </ol>
@endif
