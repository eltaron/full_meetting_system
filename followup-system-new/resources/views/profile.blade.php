@extends('layouts.app')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')

@php
    $user       = auth()->user();
    $roleLabels = [
        'admin'              => 'مشرف عام',
        'director'           => 'محافظ / نائب',
        'manager'            => 'مدير',
        'assignment_manager' => 'مسؤول التكليفات',
        'supervisor'         => 'مشرف مركز',
        'staff'              => 'موظف',
    ];
    $roleLabel = $roleLabels[$user->role] ?? 'موظف';

    $statusCounts = $user->assignedTasks()
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status');
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- بطاقة الهوية --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="h-40 bg-gradient-to-r from-primary via-[#003366] to-primary relative">
            <div class="absolute inset-0 opacity-20 cubes-pattern"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        </div>

        <div class="px-6 sm:px-10 pb-8">
            <div class="flex flex-col md:flex-row items-center md:items-end justify-between -mt-16 mb-8 gap-6">
                <div class="flex flex-col md:flex-row items-center md:items-end gap-6 text-center md:text-right">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=001f3f&color=fff"
                        alt="{{ $user->name }}"
                        class="w-28 h-28 md:w-36 md:h-36 rounded-full border-4 md:border-8 border-white shadow-2xl bg-white">
                    <div class="pb-2 md:pb-4">
                        <h1 class="text-2xl md:text-3xl font-black text-slate-800 mb-2">{{ $user->name }}</h1>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                            <span
                                class="inline-flex items-center gap-2 px-4 py-1.5 bg-accent/10 text-accent font-bold rounded-xl text-sm border border-accent/20">
                                <i class="fas fa-user-shield"></i> {{ $roleLabel }}
                            </span>
                            <span
                                class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 text-primary font-bold rounded-xl text-sm border border-primary/10">
                                <i class="fas fa-check-circle"></i> نشط الآن
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile') }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-primary font-black rounded-2xl hover:border-accent hover:text-accent transition-all shadow-sm hover:shadow-md">
                    <i class="fas fa-user-edit text-accent"></i> تعديل البيانات
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ([
                    ['icon' => 'fa-envelope', 'label' => 'البريد الإلكتروني', 'value' => $user->email],
                    ['icon' => 'fa-phone-alt', 'label' => 'رقم الهاتف', 'value' => $user->phone_number ?: '—', 'ltr' => true],
                    ['icon' => 'fa-id-card', 'label' => 'الرقم القومي', 'value' => $user->national_id ?: '—', 'ltr' => true],
                    ['icon' => 'fa-briefcase', 'label' => 'الوظيفة', 'value' => $user->job_title ?: '—'],
                    ['icon' => 'fa-layer-group', 'label' => 'الدرجة الوظيفية', 'value' => $user->job_grade ?: '—'],
                    ['icon' => 'fa-calendar-alt', 'label' => 'تاريخ الانضمام', 'value' => optional($user->created_at)->format('Y/m/d') ?: '—'],
                ] as $field)
                    <div class="bg-slate-50/50 rounded-2xl p-5 border border-slate-100 flex items-center gap-4">
                        <div
                            class="w-11 h-11 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm">
                            <i class="fas {{ $field['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-slate-400 font-bold mb-1">{{ $field['label'] }}</p>
                            <p class="font-bold text-slate-700 truncate {{ ($field['ltr'] ?? false) ? 'font-sans' : '' }}"
                                @if ($field['ltr'] ?? false) dir="ltr" @endif>{{ $field['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ملخص تكليفاتي --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
        <h2 class="text-xl font-black text-primary mb-5">
            <i class="fas fa-tasks ml-2 text-accent"></i>ملخص تكليفاتي
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['label' => 'إجمالي تكليفاتي', 'value' => $statusCounts->sum(), 'cls' => 'bg-blue-50 text-blue-600'],
                ['label' => 'تم التنفيذ', 'value' => $statusCounts['تم التنفيذ'] ?? 0, 'cls' => 'bg-green-50 text-green-600'],
                ['label' => 'قيد التنفيذ', 'value' => ($statusCounts['جاري التنفيذ'] ?? 0) + ($statusCounts['متأخر'] ?? 0), 'cls' => 'bg-yellow-50 text-yellow-600'],
                ['label' => 'لم تبدأ', 'value' => $statusCounts['لم يبدأ'] ?? 0, 'cls' => 'bg-red-50 text-red-600'],
            ] as $card)
                <div class="{{ $card['cls'] }} rounded-2xl p-5 text-center">
                    <div class="text-3xl font-black">{{ $card['value'] }}</div>
                    <p class="text-sm font-bold mt-1">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        <a href="{{ route('tasks.index') }}"
            class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-accent text-white font-bold rounded-2xl hover:bg-accent-hover transition-all">
            <i class="fas fa-list-check"></i> عرض كل التكليفات
        </a>
    </div>
</div>
@endsection