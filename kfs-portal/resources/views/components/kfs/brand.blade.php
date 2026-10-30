@props(['light' => false, 'compact' => false])

@php
    $title = config('app.name', 'بوابة محافظة كفر الشيخ');
    $sub = 'المنظومة الإدارية الموحدة';
@endphp

<a href="{{ url('/') }}" {{ $attributes->merge(['class' => 'kfs-brand' . ($light ? ' kfs-brand--light' : '')]) }}>
    <span class="kfs-brand__mark" aria-hidden="true">
        <img src="{{ asset('logo.png') }}" alt="" style="width:38px;height:38px;object-fit:contain;border-radius:9px;background:#FBFAF6;padding:2px;">
    </span>
    @unless ($compact)
        <span class="kfs-brand__text">
            <span class="kfs-brand__title">{{ $title }}</span>
            <span class="kfs-brand__sub">{{ $sub }}</span>
        </span>
    @endunless
</a>
