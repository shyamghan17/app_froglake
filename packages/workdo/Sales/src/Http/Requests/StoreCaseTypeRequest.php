<?php

namespace Workdo\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaseTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required'
        ];
    }
}