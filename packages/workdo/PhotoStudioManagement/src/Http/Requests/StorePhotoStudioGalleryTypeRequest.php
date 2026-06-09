<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoStudioGalleryTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->status)) {
            $this->merge([
                'status' => in_array($this->status, ['true', '1', 'active'], true),
            ]);
        }
    }
}
