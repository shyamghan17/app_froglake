<?php

namespace Workdo\PettyCashManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePettyCashRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->user()?->can('manage-any-petty-cash-requests')) {
                        return;
                    }

                    if ((int) $value !== (int) $this->user()?->id) {
                        $fail(__('You can only create petty cash requests for yourself.'));
                    }
                },
            ],
            'categorie_id' => [
                'required',
                'integer',
                Rule::exists('petty_cash_categories', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
            'requested_amount' => 'required|numeric|min:0',
            'remarks'          => 'nullable',
            'receipt_path'     => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ];
    }
}
