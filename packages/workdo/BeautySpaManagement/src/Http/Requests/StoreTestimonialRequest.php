<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'testimonials' => 'required|array',
            'testimonials.*.customer_name' => 'required|string|max:255',
            'testimonials.*.rating' => 'required|integer|min:1|max:5',
            'testimonials.*.comment' => 'required|string|max:1000',
        ];
    }
}