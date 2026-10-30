<?php

namespace Modules\Followup\Http\Controllers;

use Modules\Followup\Models\SignatureOfficer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SignatureOfficerController extends Controller
{
    public function index()
    {
        $officers = SignatureOfficer::withCount('signatures')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('signature-officers.index', compact('officers'));
    }

    public function create()
    {
        return view('signature-officers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        SignatureOfficer::create($data);

        return redirect()->route('signature-officers.index')
            ->with('success', 'تم إضافة مسئول التأشيرة بنجاح');
    }

    public function edit(SignatureOfficer $signatureOfficer)
    {
        return view('signature-officers.edit', ['officer' => $signatureOfficer]);
    }

    public function update(Request $request, SignatureOfficer $signatureOfficer)
    {
        $data = $this->validated($request, $signatureOfficer);
        $data['is_active'] = $request->boolean('is_active');

        $signatureOfficer->update($data);

        return redirect()->route('signature-officers.index')
            ->with('success', 'تم تعديل بيانات مسئول التأشيرة بنجاح');
    }

    public function toggle(SignatureOfficer $signatureOfficer)
    {
        $signatureOfficer->update(['is_active' => ! $signatureOfficer->is_active]);

        return back()->with('success', $signatureOfficer->is_active ? 'تم تفعيل المسئول' : 'تم تعطيل المسئول');
    }

    public function destroy(SignatureOfficer $signatureOfficer)
    {
        if ($signatureOfficer->signatures()->exists()) {
            return back()->withErrors([
                'delete' => 'لا يمكن حذف مسئول مرتبط بتأشيرات سابقة، يمكنك تعطيله بدلاً من ذلك.',
            ]);
        }

        $signatureOfficer->delete();

        return redirect()->route('signature-officers.index')
            ->with('success', 'تم حذف مسئول التأشيرة');
    }

    private function validated(Request $request, ?SignatureOfficer $officer = null): array
    {
        return $request->validate([
            'name'  => ['required', 'string', 'max:255', Rule::unique('signature_officers', 'name')->ignore($officer?->id)],
            'title' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'اسم مسئول التأشيرة مطلوب',
            'name.unique'   => 'هذا الاسم مسجل من قبل',
        ]);
    }
}