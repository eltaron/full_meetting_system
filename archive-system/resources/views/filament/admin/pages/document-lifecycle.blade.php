<x-filament-panels::page>
    {{ $this->form }}

    @php
        $record = $this->getRecord();
        $logs = $this->getLogs();
    @endphp

    @if ($this->subject && ! $record)
        <x-filament::section>
            <div class="arc-empty">المستند غير موجود (ربما حُذف نهائياً). الحركات المسجلة عليه:</div>
            @include('filament.shared.subject-timeline', ['logs' => $logs])
        </x-filament::section>
    @elseif ($record)
        <x-filament::section>
            <x-slot name="heading">{{ $record->archive_number }} — {{ $record->title }}</x-slot>
            <x-slot name="description">
                {{ $record->direction?->getLabel() }} · {{ $record->document_type?->getLabel() }} ·
                {{ $record->department?->name }} · تاريخ المستند {{ $record->document_date?->format('Y-m-d') }}
            </x-slot>

            <div class="arc-stats">
                <div class="arc-stat">
                    <span>أنشأه</span>
                    <strong>{{ $record->user?->name ?? '-' }}</strong>
                    <small>{{ $record->created_at->format('Y-m-d h:i A') }}</small>
                </div>
                <div class="arc-stat">
                    <span>عدد التعديلات</span>
                    <strong>{{ $logs->where('action', 'updated')->count() }}</strong>
                    <small>آخر تعديل: {{ optional($logs->firstWhere('action', 'updated'))->created_at?->diffForHumans() ?? '-' }}</small>
                </div>
                <div class="arc-stat">
                    <span>مرات العرض وفتح المرفقات</span>
                    <strong>{{ $logs->whereIn('action', ['viewed', 'viewed_attachment'])->count() }}</strong>
                    <small>بواسطة {{ $logs->whereIn('action', ['viewed', 'viewed_attachment'])->pluck('user_id')->unique()->count() }} مستخدم</small>
                </div>
                <div class="arc-stat">
                    <span>الحالة الحالية</span>
                    <strong>
                        @if ($record->trashed())
                            <x-filament::badge color="danger">محذوف</x-filament::badge>
                        @elseif (! $record->is_active)
                            <x-filament::badge color="warning">ملغي</x-filament::badge>
                        @else
                            <x-filament::badge color="success">ساري</x-filament::badge>
                        @endif
                    </strong>
                    @if (! $record->is_active)
                        <small>{{ $record->deactivation_reason }}</small>
                    @endif
                </div>
            </div>

            <div class="arc-lifecycle-actions">
                @unless ($record->trashed())
                    <x-filament::button tag="a" size="sm" color="gray" icon="heroicon-o-eye"
                        :href="\App\Filament\Admin\Resources\SubjectResource::getUrl('view', ['record' => $record])">
                        فتح المستند
                    </x-filament::button>
                @endunless
            </div>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-clock">
            <x-slot name="heading">التسلسل الزمني للحركات ({{ $logs->count() }})</x-slot>
            @include('filament.shared.subject-timeline', ['logs' => $logs])
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="arc-empty">
                اختر مستنداً لعرض دورة عمله كاملة: من أنشأه، من عدّل عليه وماذا غيّر، من اطلع عليه أو فتح مرفقاته، ومن ألغاه أو حذفه.
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
