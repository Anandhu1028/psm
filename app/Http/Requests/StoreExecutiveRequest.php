<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExecutiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage_executives');
    }

    public function rules(): array
    {
        return [
            'employee_id'         => ['required', 'string', 'max:50', 'unique:executives,employee_id'],
            'name'                => ['required', 'string', 'max:150'],
            'mobile'              => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'unique:executives,email'],
            'company_id'          => ['required', 'exists:companies,id'],
            'zone_id'             => ['required', 'exists:zones,id'],
            'date_joined'         => ['nullable', 'date'],
            'probation_end_date'  => ['nullable', 'date', 'after_or_equal:date_joined'],
            'status'              => ['required', 'in:probation,active,inactive'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'photo'               => ['nullable', 'image', 'max:2048'],
            'monthly_admission_target' => ['required', 'integer', 'min:0'],
        ];
    }
}
