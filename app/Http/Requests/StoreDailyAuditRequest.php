<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('enter_daily_audit');
    }

    public function rules(): array
    {
        return [
            'executive_id'               => ['required', 'exists:executives,id'],
            'audit_date'                 => ['required', 'date', 'before_or_equal:today'],
            'connected_calls'            => ['required', 'integer', 'min:0', 'max:999'],
            'confirmed_meetings'         => ['required', 'integer', 'min:0', 'max:99'],
            'meetings_attended'          => ['required', 'integer', 'min:0', 'max:99'],
            'admissions_today'           => ['required', 'integer', 'min:0'],
            'crm_followup'               => ['boolean'],
            'crm_disposition_correct'    => ['boolean'],
            'first_contact_within_45min' => ['boolean'],
            'all_leads_followed_up'      => ['boolean'],
            'warm_lead_converted'        => ['boolean'],
            'cold_lead_reactivated'      => ['boolean'],
            // FOCUZ-specific (nullable)
            'rolling_day'                => ['nullable', 'integer', 'min:1', 'max:365'],
            'rolling_window_days'        => ['nullable', 'integer', 'min:1'],
            'rolling_meeting_count'      => ['nullable', 'integer', 'min:0'],
            'checkpoint_result'          => ['nullable', 'in:passed,failed,na'],
            // Common
            'evidence'                   => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,mp4', 'max:20480'],
            'remarks'                    => ['nullable', 'string', 'max:1000'],
            'violations'                 => ['nullable', 'array'],
            'violations.*'               => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'executive_id.required'      => 'Please select an executive.',
            'audit_date.before_or_equal' => 'Audit date cannot be in the future.',
            'connected_calls.required'   => 'Connected calls count is required.',
        ];
    }

    /**
     * Prepare checkbox booleans before validation.
     */
    protected function prepareForValidation(): void
    {
        $boolFields = [
            'crm_followup', 'crm_disposition_correct',
            'first_contact_within_45min', 'all_leads_followed_up',
            'warm_lead_converted', 'cold_lead_reactivated',
        ];

        $merged = [];
        foreach ($boolFields as $field) {
            $merged[$field] = $this->has($field) ? 1 : 0;
        }

        $this->merge($merged);
    }
}
