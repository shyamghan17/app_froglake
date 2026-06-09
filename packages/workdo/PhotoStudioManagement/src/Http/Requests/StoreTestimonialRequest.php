<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_feedback_label'        => 'required|string|max:255',
            'client_feedback_title'         => 'required|string|max:255',
            'testimonial_title'            => 'required|string|max:255',
            'testimonial_image'             => 'required|string',
            'testimonials'                  => 'array|min:1',
            'testimonials.*.customer_name'  => 'required|string|max:255',
            'testimonials.*.designation'    => 'required|string|max:255',
            'testimonials.*.rating'         => 'required|integer|min:1|max:5',
            'testimonials.*.comment'        => 'required|string',
            'testimonials.*.profile_image'  => 'nullable|string',
        ];
    }
}
