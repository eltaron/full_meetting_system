<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label class="block text-sm font-black text-slate-600">اسم المسئول <span class="text-red-500">*</span></label>
        <input type="text" name="name" required value="{{ old('name', $officer->name ?? '') }}"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-black text-slate-600">المسمى الوظيفي</label>
        <input type="text" name="title" value="{{ old('title', $officer->title ?? '') }}"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-accent/20 focus:border-accent outline-none transition-all">
    </div>
</div>

<label class="inline-flex items-center gap-3 cursor-pointer">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 rounded accent-indigo-600"
        @checked(old('is_active', $officer->is_active ?? true))>
    <span class="text-sm font-bold text-slate-600">مفعّل (يظهر في قائمة التأشيرات)</span>
</label>