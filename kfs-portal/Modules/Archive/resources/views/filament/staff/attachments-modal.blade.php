<div class="arc-attachments">
    @if ($media->isEmpty())
        <div class="arc-empty">لا توجد مرفقات لهذه الوثيقة</div>
    @else
        <ul class="arc-attachment-list">
            @foreach ($media as $file)
                <li class="arc-attachment">
                    <div class="arc-attachment-name">
                        <x-filament::icon
                            :icon="str_starts_with($file->mime_type, 'image/') ? 'heroicon-o-photo' : 'heroicon-o-document-text'"
                            class="arc-attachment-icon" />
                        <span title="{{ $file->name }}">{{ $file->name }}</span>
                        <small class="arc-muted">{{ $file->human_readable_size }}</small>
                    </div>

                    <div class="arc-attachment-actions">
                        <x-filament::button tag="a" size="xs" icon="heroicon-o-eye" target="_blank"
                            :href="route('attachments.show', $file)">
                            فتح / طباعة
                        </x-filament::button>
                        <x-filament::button tag="a" size="xs" color="gray" icon="heroicon-o-arrow-down-tray"
                            :href="route('attachments.show', ['media' => $file, 'download' => 1])">
                            تحميل
                        </x-filament::button>
                    </div>
                </li>
            @endforeach
        </ul>
        <p class="arc-muted arc-center">
            * لطباعة الملف اضغط "فتح / طباعة" ثم Ctrl+P. يتم تسجيل كل فتح أو تحميل في سجل الحركات.
        </p>
    @endif
</div>
