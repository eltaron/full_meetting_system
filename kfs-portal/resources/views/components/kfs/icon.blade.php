@props(['name' => 'dot', 'size' => 24, 'stroke' => 1.7])

@php
    $paths = [
        'archive' => '<path d="M3.75 7.5h16.5v11.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V7.5Z"/><path d="M2.25 5.25h19.5v2.25H2.25z"/><path d="M9 12h6"/>',
        'calendar' => '<rect x="3.75" y="5.25" width="16.5" height="15" rx="1.5"/><path d="M3.75 9.75h16.5M8.25 3v4.5M15.75 3v4.5"/><path d="M7.5 13.5h3v3h-3z"/>',
        'tasks' => '<path d="M9 12.75 11.25 15 15 9.75"/><path d="M5.25 4.5h9l4.5 4.5v10.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-13.5a1.5 1.5 0 0 1 1.5-1.5Z"/><path d="M14.25 4.5V9h4.5"/>',
        'shield' => '<path d="M12 3 4.5 6v6c0 4.5 3.15 7.65 7.5 9 4.35-1.35 7.5-4.5 7.5-9V6L12 3Z"/><path d="m9 12 2 2 4-4"/>',
        'users' => '<circle cx="9" cy="8.25" r="3"/><path d="M3.75 19.5a5.25 5.25 0 0 1 10.5 0"/><path d="M16.5 5.25a3 3 0 0 1 0 6M17.25 14.25a5.25 5.25 0 0 1 3 5.25"/>',
        'document' => '<path d="M6.75 3.75h7.5l3.75 3.75v12.75H6.75z"/><path d="M14.25 3.75v3.75h3.75M9.75 12h4.5M9.75 15.75h4.5"/>',
        'arrow-left' => '<path d="M19.5 12H5.25M11.25 6 5.25 12l6 6"/>',
        'arrow-right' => '<path d="M4.5 12h14.25M12.75 6l6 6-6 6"/>',
        'arrow-up-left' => '<path d="M17.25 17.25 6.75 6.75M6.75 14.25v-7.5h7.5"/>',
        'check' => '<path d="m4.5 12.75 4.5 4.5 10.5-10.5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="8.25"/><path d="m8.25 12 2.5 2.5 5-5"/>',
        'lock' => '<rect x="4.5" y="10.5" width="15" height="9" rx="1.5"/><path d="M8.25 10.5V7.5a3.75 3.75 0 0 1 7.5 0v3"/>',
        'user' => '<circle cx="12" cy="8.25" r="3.5"/><path d="M4.5 20.25a7.5 7.5 0 0 1 15 0"/>',
        'mail' => '<rect x="3.75" y="5.25" width="16.5" height="13.5" rx="1.5"/><path d="m3.75 6.75 8.25 6 8.25-6"/>',
        'menu' => '<path d="M4.5 7.5h15M4.5 12h15M4.5 16.5h15"/>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'chart' => '<path d="M4.5 19.5h15"/><rect x="6.75" y="11.25" width="2.5" height="5.25"/><rect x="11.25" y="6.75" width="2.5" height="9.75"/><rect x="15.75" y="9" width="2.5" height="7.5"/>',
        'clock' => '<circle cx="12" cy="12" r="8.25"/><path d="M12 7.5v4.5l3 1.75"/>',
        'building' => '<path d="M4.5 20.25V5.25l7.5-2.25v17.25M12 20.25h7.5V9L12 6.75"/><path d="M7.5 8.25h1.5M7.5 11.25h1.5M7.5 14.25h1.5M15 12h1.5M15 15h1.5"/>',
        'key' => '<circle cx="8.25" cy="15.75" r="3.75"/><path d="m10.8 13.2 7.95-7.95M15.75 8.25l1.5 1.5M18 6l1.5 1.5"/>',
        'bell' => '<path d="M6.75 9.75a5.25 5.25 0 0 1 10.5 0c0 5.25 2.25 6.75 2.25 6.75H4.5s2.25-1.5 2.25-6.75Z"/><path d="M10.5 19.5a1.5 1.5 0 0 0 3 0"/>',
        'spark' => '<path d="M12 3.75 13.5 9l5.25 1.5-5.25 1.5L12 17.25 10.5 12 5.25 10.5 10.5 9 12 3.75Z"/><path d="M18.75 15.75 19.5 18l2.25.75-2.25.75-.75 2.25-.75-2.25L15.75 18l2.25-.75.75-1.5Z"/>',
        'layers' => '<path d="m12 3.75 8.25 4.5L12 12.75 3.75 8.25 12 3.75Z"/><path d="m3.75 12 8.25 4.5 8.25-4.5M3.75 15.75 12 20.25l8.25-4.5"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6"/><path d="m15 15 4.5 4.5"/>',
        'logout' => '<path d="M14.25 8.25V5.25a1.5 1.5 0 0 0-1.5-1.5h-7.5a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5h7.5a1.5 1.5 0 0 0 1.5-1.5v-3"/><path d="M18 9 21 12l-3 3M10.5 12H21"/>',
        'dot' => '<circle cx="12" cy="12" r="3"/>',
        'warning' => '<path d="M12 4.5 21 19.5H3L12 4.5Z"/><path d="M12 9.75v4.5M12 17.25h.01"/>',
        'info' => '<circle cx="12" cy="12" r="8.25"/><path d="M12 11.25v5.25M12 8.25h.01"/>',
    ];
    $path = $paths[$name] ?? $paths['dot'];
@endphp

<svg {{ $attributes->merge(['class' => 'kfs-icon']) }}
     width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     fill="none" stroke="currentColor" stroke-width="{{ $stroke }}"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    {!! $path !!}
</svg>
