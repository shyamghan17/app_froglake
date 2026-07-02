<?php

namespace Workdo\PettyCashManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Workdo\PettyCashManagement\Models\PettyCashReconciliation;

class StorePettyCashReconciliationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'counted_cash' => 'required|numeric|min:0',
            'locked' => 'sometimes|boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $start = (string) $this->input('period_start');
            $end = (string) $this->input('period_end');

            $overlapExists = PettyCashReconciliation::query()
                ->where('created_by', creatorId())
                ->where('period_start', '<=', $end)
                ->where('period_end', '>=', $start)
                ->exists();

            if ($overlapExists) {
                $validator->errors()->add('period_start', __('A reconciliation already exists for an overlapping period.'));
            }
        });
    }
}

