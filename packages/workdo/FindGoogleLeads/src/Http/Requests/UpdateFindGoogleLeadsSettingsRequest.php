<?php

namespace Workdo\FindGoogleLeads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFindGoogleLeadsSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings.findgoogleleads_api_key' => 'required|string',
            'settings.finsgoogleleads_radius' => 'required|numeric|min:1',
            'settings.finsgoogleleads_pipelines' => 'required|exists:pipelines,id',
            'settings.finsgoogleleads_lead_stages' => 'required|exists:lead_stages,id',
        ];
    }

    public function messages(): array
    {
        return [
            'settings.findgoogleleads_api_key.required' => __('API Key is required.'),
            'settings.finsgoogleleads_radius.required' => __('Radius is required.'),
            'settings.finsgoogleleads_pipelines.required' => __('Pipeline selection is required.'),
            'settings.finsgoogleleads_lead_stages.required' => __('Lead stage selection is required.'),
        ];
    }
}