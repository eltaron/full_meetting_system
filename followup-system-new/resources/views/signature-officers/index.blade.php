@extends('layouts.app')

@section('title', 'مسئولو التأشيرات')
@section('page-title', 'مسئولو التأشيرات')

@section('content')
<div class="max-w-5xl mx-auto w-full">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">

        <div class="bg-primary p-6 text-white flex items-center justify-between">
            <h3 class="text-2xl font-black flex items-center gap-3">
                <i class="fas fa-signature text-accent"></i> مسئولو التأشيرات
            </h3>
            <a href="{{ route('signature-officers.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-accent text-white font-bold hover:bg-accent-hover transition-all shadow-sm">
                <i class="fas fa-plus"></i> إضافة مسئول
            </a>
        </div>

        @if (session('success'))
            <div class="mx-8 mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-8 mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="p-8 overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="text-sm text-slate-500 border-b border-slate-100">
                        <th class="py-3 px-4 font-bold">#</th>
                        <th class="py-3 px-4 font-bold">الاسم</th>
                        <th class="py-3 px-4 font-bold">المسمى الوظيفي</th>
                        <th class="py-3 px-4 font-bold">عدد التأشيرات</th>
                        <th class="py-3 px-4 font-bold">الحالة</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($officers as $officer)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-all">
                            <td class="py-3 px-4 text-slate-400">{{ $loop->iteration }}</td>
                            <td class="py-3 px-4 font-bold text-slate-700">{{ $officer->name }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $officer->title ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $officer->signatures_count }}</td>
                            <td class="py-3 px-4">
                                @if ($officer->is_active)
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">مفعّل</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-slate-200 text-slate-600 text-xs font-bold">معطّل</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('signature-officers.edit', $officer) }}"
                                       class="px-3 py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all" title="تعديل">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form method="POST" action="{{ route('signature-officers.toggle', $officer) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-2 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-all"
                                            title="{{ $officer->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="fas {{ $officer->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('signature-officers.destroy', $officer) }}"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المسئول؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">لا يوجد مسئولو تأشيرات مسجلون بعد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection