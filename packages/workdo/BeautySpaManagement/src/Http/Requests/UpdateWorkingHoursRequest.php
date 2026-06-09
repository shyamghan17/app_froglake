<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkingHoursRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'opening_time' => 'required',
            'closing_time' => 'required',
            'business_hours' => 'required|array',
            'business_hours.*.day' => 'required|string',
            'business_hours.*.is_open' => 'required|boolean',
            'holiday_setting' => 'nullable|string|in:on,off',
        ];
    }
}