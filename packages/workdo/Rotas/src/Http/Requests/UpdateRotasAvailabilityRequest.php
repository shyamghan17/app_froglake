<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;
use Workdo\Rotas\Models\RotasAvailability;

class UpdateRotasAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'availability' => 'required|array|min:1',
            'availability.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i',
            'availability.*.type' => 'required|string|in:available,unavailable',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => __('Employee is required.'),
            'employee_id.exists' => __('Selected employee is invalid.'),
            'name.required' => __('Name is required.'),
            'name.string' => __('Name must be a string.'),
            'name.max' => __('Name cannot exceed 255 characters.'),
            'start_date.required' => __('Start date is required.'),
            'start_date.date' => __('Start date must be a valid date.'),
            'end_date.required' => __('End date is required.'),
            'end_date.date' => __('End date must be a valid date.'),
            'end_date.after_or_equal' => __('End date must be after or equal to start date.'),
            'availability.required' => __('Availability is required.'),
            'availability.array' => __('Availability must be an array.'),
            'availability.min' => __('At least one availability slot is required.'),
            'availability.*.day.required' => __('Day is required for each availability slot.'),
            'availability.*.day.in' => __('Day must be a valid weekday.'),
            'availability.*.start_time.required' => __('Start time is required for each availability slot.'),
            'availability.*.start_time.date_format' => __('Start time must be in HH:MM format.'),
            'availability.*.end_time.required' => __('End time is required for each availability slot.'),
            'availability.*.end_time.date_format' => __('End time must be in HH:MM format.'),
            'availability.*.type.required' => __('Availability type is required.'),
            'availability.*.type.in' => __('Availability type must be either available or unavailable.')
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateAvailabilityOverlaps($validator);
            $this->validateSameDateAvailability($validator);
        });
    }

    private function validateAvailabilityOverlaps(Validator $validator): void
    {
        $availability = $this->input('availability', []);
        
        foreach ($availability as $index => $slot) {
            if (!isset($slot['day'], $slot['start_time'], $slot['end_time'])) {
                continue;
            }
            
            foreach ($availability as $compareIndex => $compareSlot) {
                if ($index >= $compareIndex || !isset($compareSlot['day'], $compareSlot['start_time'], $compareSlot['end_time'])) {
                    continue;
                }
                
                if ($slot['day'] === $compareSlot['day']) {
                    // Check if slots are identical or overlap
                    if ($slot['start_time'] === $compareSlot['start_time'] && $slot['end_time'] === $compareSlot['end_time']) {
                        $validator->errors()->add(
                            "availability.{$index}.start_time",
                            __('Duplicate time slot :start-:end on :day.', [
                                'start' => $slot['start_time'],
                                'end' => $slot['end_time'],
                                'day' => ucfirst($slot['day'])
                            ])
                        );
                        break;
                    }
                }
            }
        }
    }

    private function validateSameDateAvailability(Validator $validator): void
    {
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $employeeId = $this->input('employee_id');
        $availability = $this->input('availability', []);
        $currentId = $this->route('availability')->id ?? null;
        
        if ($startDate && $endDate && $startDate === $endDate) {
            if (empty($availability)) {
                $validator->errors()->add(
                    'availability',
                    __('When start and end dates are the same, at least one availability slot is required.')
                );
            }
        }
        
        // Check for overlapping date ranges for same employee (excluding current record)
        if ($startDate && $endDate && $employeeId) {
            $query = RotasAvailability::where('employee_id', $employeeId)
                ->where(function($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $endDate)
                          ->where('end_date', '>=', $startDate);
                });
                
            if ($currentId) {
                $query->where('id', '!=', $currentId);
            }
            
            if ($query->exists()) {
                $validator->errors()->add(
                    'start_date',
                    __('Employee already has availability for overlapping dates.')
                );
            }
        }
    }
}