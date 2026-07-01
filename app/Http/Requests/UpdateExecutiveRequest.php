<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExecutiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage_executives');
    }

    public function rules(): array
    {
        $id = $this->route('executive')->id ?? $this->route('executive');
        return [
            'employee_id'        => ['required', 'string', 'max:50', Rule::unique('executives', 'employee_id')->ignore($id)],
            'name'               => ['required', 'string', 'max:150'],
            'mobile'             => ['nullable', 'string', 'max:20'],
            'email'              => ['nullable', 'email', Rule::unique('executives', 'email')->ignore($id)],
            'company_id'         => ['required', 'exists:companies,id'],
            'zone_id'            => ['required', 'exists:zones,id'],
            'date_joined'        => ['nullable', 'date'],
            'probation_end_date' => ['nullable', 'date'],
            'status'             => ['required', 'in:probation,active,inactive'],
            'notes'              => ['nullable', 'string', 'max:1000'],
            'photo'              => ['nullable', 'image', 'max:2048'],
            'monthly_admission_target' => ['required', 'integer', 'min:0'],
        ];
    }
}
