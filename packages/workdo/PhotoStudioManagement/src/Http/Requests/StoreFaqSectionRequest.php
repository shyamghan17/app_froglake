<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'faq_page_title'     => 'required|string|max:255',
            'faq_label'          => 'required|string|max:255',
            'faq_title'          => 'required|string|max:255',
            'faqs'               => 'required|array|min:1',
            'faqs.*.question'    => 'required|string',
            'faqs.*.answer'      => 'required|string',
        ];
    }
}
