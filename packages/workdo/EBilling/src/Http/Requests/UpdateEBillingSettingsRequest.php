<?php

namespace Workdo\EBilling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEBillingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings.ebilling_enabled' => 'required|string|in:on,off',
            'settings.ebilling_invoice_prefix' => 'required|string|max:20',
        ];
    }
}

