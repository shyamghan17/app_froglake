<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeautyBookingPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_person'     => 'required|integer|min:1',
            'payment_amount'   => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'booking_id'       => 'required|integer',
            'service'          => 'required|integer',
            'payment_date'     => 'required|date',
            'customer_name'    => 'required|string',
            'reference_number' => 'required|string',
        ];
    }
}
