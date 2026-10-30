@extends('layouts.app')

@section('title', 'تعديل مسئول تأشيرة')
@section('page-title', 'تعديل مسئول تأشيرة')

@section('content')
<div class="max-w-3xl mx-auto w-full">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="bg-primary p-6 text-white">
            <h3 class="text-2xl font-black flex items-center gap-3">
                <i class="fas fa-pen text-accent"></i> تعديل: {{ $officer->name }}
            </h3>
        </div>

        @if ($errors->any())
            <div class="mx-8 mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('signature-officers.update', $officer) }}" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            @include('signature-officers._form')

            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('signature-officers.index') }}"
                   class="px-6 py-4 rounded-2xl text-slate-500 font-bold hover:bg-slate-50 transition-all">إلغاء</a>
                <button type="submit"
                    class="flex-1 bg-primary text-white font-black py-4 rounded-2xl border-2 border-transparent hover:border-accent shadow-lg text-lg transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-check-double text-accent"></i> حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection