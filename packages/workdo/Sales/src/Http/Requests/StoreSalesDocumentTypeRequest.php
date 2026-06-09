<?php

namespace Workdo\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required'
        ];
    }
}