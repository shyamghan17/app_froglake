<?php

namespace Workdo\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sources = $this->input('sources');

        $normalizedSources = is_array($sources)
            ? array_values(array_filter($sources, fn($value) => $value !== null && $value !== ''))
            : ($sources !== null && $sources !== '' ? [$sources] : null);

        $whatsappSameAsPhone = filter_var(
            $this->input('whatsapp_same_as_phone', true),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        $this->merge([
            'sources' => $normalizedSources,
            'whatsapp_same_as_phone' => $whatsappSameAsPhone ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'subject' => 'required|string|max:200',
            'phone' => 'nullable|string|regex:/^\+\d{1,3}\d{9,13}$/',
            'date' => 'nullable|date',
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn($query) => $query->where('created_by', creatorId())),
            ],
            'designation' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'pan_vat_number' => 'nullable|string|max:100',
            'organization_type' => 'nullable|string|max:100',
            'whatsapp_same_as_phone' => 'nullable|boolean',
            'whatsapp_viber_number' => [
                'nullable',
                'string',
                'regex:/^\+\d{1,3}\d{9,13}$/',
                Rule::requiredIf(fn() => $this->boolean('whatsapp_same_as_phone') === false),
            ],
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:30',
            'sources' => 'required|array|min:1',
            'sources.*' => [
                'integer',
                Rule::exists('sources', 'id')->where(fn($query) => $query->where('created_by', creatorId())),
            ],
        ];
    }
}
