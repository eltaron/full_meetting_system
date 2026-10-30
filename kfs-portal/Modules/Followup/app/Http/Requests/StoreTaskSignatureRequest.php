<?php

namespace Modules\Followup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'signature_officer_id' => ['required', Rule::exists('signature_officers', 'id')->where('is_active', true)],
            'signature_text' => ['required', 'string', 'max:2000'],
            'signature_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'signature_officer_id.required' => 'يجب اختيار مسئول التأشيرة',
            'signature_officer_id.exists' => 'مسئول التأشيرة المختار غير صحيح أو غير مفعل',
            'signature_text.required' => 'نص التأشيرة مطلوب',
            'signature_date.required' => 'تاريخ التأشيرة مطلوب',
        ];
    }
}
