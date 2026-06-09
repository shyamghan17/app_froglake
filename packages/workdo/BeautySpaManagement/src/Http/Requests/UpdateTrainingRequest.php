<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_name' => 'required|max:100',
            'trainer' => 'required|max:100',
            'date' => 'required|date',
            'duration' => 'required|max:50',
            'location' => 'required|max:200',
            'description' => 'nullable|max:1000'
        ];
    }
}