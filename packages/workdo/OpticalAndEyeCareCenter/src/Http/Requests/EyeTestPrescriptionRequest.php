<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;

abstract class EyeTestPrescriptionRequest extends FormRequest
{
    private const EYE_OPTIONS = ['right', 'left', 'both', 'na'];

    private const MEDICINE_EYE_OPTIONS = ['right', 'left', 'both', 'systemic'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', $this->tenantPatientRule()],
            'doctor_name' => ['required', 'integer', $this->tenantDoctorRule()],
            'test_date' => ['required', 'date'],
            'follow_up_date' => ['nullable', 'date', 'after_or_equal:test_date'],
            'prescription_expiry_date' => ['nullable', 'date', 'after_or_equal:test_date'],
            'test_results' => ['nullable', 'string'],
            'prescription_details' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'complaints' => ['nullable', 'array'],
            'complaints.items' => ['nullable', 'array'],
            'complaints.items.*' => ['nullable', 'string', 'max:255'],
            'complaints.custom' => ['nullable', 'string', 'max:1000'],
            'complaints.affected_eye' => ['nullable', Rule::in(self::EYE_OPTIONS)],

            'visual_acuity' => ['nullable', 'array'],
            'visual_acuity.right' => ['nullable', 'array'],
            'visual_acuity.left' => ['nullable', 'array'],
            'visual_acuity.right.distance' => ['nullable', 'string', 'max:100'],
            'visual_acuity.right.near' => ['nullable', 'string', 'max:100'],
            'visual_acuity.right.pinhole' => ['nullable', 'string', 'max:100'],
            'visual_acuity.right.with_glasses' => ['nullable', 'string', 'max:100'],
            'visual_acuity.right.without_glasses' => ['nullable', 'string', 'max:100'],
            'visual_acuity.left.distance' => ['nullable', 'string', 'max:100'],
            'visual_acuity.left.near' => ['nullable', 'string', 'max:100'],
            'visual_acuity.left.pinhole' => ['nullable', 'string', 'max:100'],
            'visual_acuity.left.with_glasses' => ['nullable', 'string', 'max:100'],
            'visual_acuity.left.without_glasses' => ['nullable', 'string', 'max:100'],

            'refraction' => ['nullable', 'array'],
            'refraction.right' => ['nullable', 'array'],
            'refraction.left' => ['nullable', 'array'],
            'refraction.right.sphere' => ['nullable', 'string', 'max:50'],
            'refraction.right.cylinder' => ['nullable', 'string', 'max:50'],
            'refraction.right.axis' => ['nullable', 'string', 'max:50'],
            'refraction.right.vision' => ['nullable', 'string', 'max:50'],
            'refraction.right.near_vision' => ['nullable', 'string', 'max:50'],
            'refraction.right.add' => ['nullable', 'string', 'max:50'],
            'refraction.left.sphere' => ['nullable', 'string', 'max:50'],
            'refraction.left.cylinder' => ['nullable', 'string', 'max:50'],
            'refraction.left.axis' => ['nullable', 'string', 'max:50'],
            'refraction.left.vision' => ['nullable', 'string', 'max:50'],
            'refraction.left.near_vision' => ['nullable', 'string', 'max:50'],
            'refraction.left.add' => ['nullable', 'string', 'max:50'],

            'eye_examination' => ['nullable', 'array'],
            'eye_examination.right' => ['nullable', 'array'],
            'eye_examination.left' => ['nullable', 'array'],
            'eye_examination.right.lid' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.conjunctiva' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.cornea' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.anterior_chamber' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.iris' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.pupil' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.lens' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.vitreous' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.fundus' => ['nullable', 'string', 'max:255'],
            'eye_examination.right.colour_vision' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.lid' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.conjunctiva' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.cornea' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.anterior_chamber' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.iris' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.pupil' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.lens' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.vitreous' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.fundus' => ['nullable', 'string', 'max:255'],
            'eye_examination.left.colour_vision' => ['nullable', 'string', 'max:255'],

            'intraocular_pressure' => ['nullable', 'array'],
            'intraocular_pressure.right' => ['nullable', 'numeric', 'between:0,99.99'],
            'intraocular_pressure.left' => ['nullable', 'numeric', 'between:0,99.99'],
            'intraocular_pressure.unit' => ['nullable', Rule::in(['mmHg'])],

            'medical_history' => ['nullable', 'array'],
            'medical_history.tbut' => ['nullable', 'array'],
            'medical_history.blood_pressure' => ['nullable', 'array'],
            'medical_history.past_history' => ['nullable', 'array'],
            'medical_history.other_diseases' => ['nullable', 'array'],
            'medical_history.tbut.checked' => ['nullable', 'boolean'],
            'medical_history.tbut.notes' => ['nullable', 'string', 'max:1000'],
            'medical_history.blood_pressure.checked' => ['nullable', 'boolean'],
            'medical_history.blood_pressure.notes' => ['nullable', 'string', 'max:1000'],
            'medical_history.past_history.checked' => ['nullable', 'boolean'],
            'medical_history.past_history.notes' => ['nullable', 'string', 'max:1000'],
            'medical_history.other_diseases.checked' => ['nullable', 'boolean'],
            'medical_history.other_diseases.notes' => ['nullable', 'string', 'max:1000'],

            'diagnosis' => ['nullable', 'array'],
            'diagnosis.primary' => ['nullable', 'string', 'max:255'],
            'diagnosis.secondary' => ['nullable', 'array'],
            'diagnosis.secondary.*' => ['nullable', 'string', 'max:255'],
            'diagnosis.summary' => ['nullable', 'string', 'max:2000'],

            'medicines' => ['nullable', 'array'],
            'medicines.*' => ['nullable', 'array'],
            'medicines.*.medicine' => ['required_with:medicines.*', 'string', 'max:255'],
            'medicines.*.eye' => ['nullable', Rule::in(self::MEDICINE_EYE_OPTIONS)],
            'medicines.*.frequency' => ['nullable', 'string', 'max:255'],
            'medicines.*.duration' => ['nullable', 'string', 'max:255'],
            'medicines.*.instructions' => ['nullable', 'string', 'max:1000'],

            'glasses_prescription' => ['nullable', 'array'],
            'glasses_prescription.right' => ['nullable', 'array'],
            'glasses_prescription.left' => ['nullable', 'array'],
            'glasses_prescription.right.sphere' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.right.cylinder' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.right.axis' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.right.vision' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.right.near_vision' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.right.add' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.left.sphere' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.left.cylinder' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.left.axis' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.left.vision' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.left.near_vision' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.left.add' => ['nullable', 'string', 'max:50'],
            'glasses_prescription.notes' => ['nullable', 'string', 'max:1000'],

            'eye_diagram' => ['nullable', 'array'],
            'eye_diagram.image_path' => ['nullable', 'string', 'max:2048'],
            'eye_diagram.annotations' => ['nullable', 'array'],
            'eye_diagram.notes' => ['nullable', 'string', 'max:1000'],

            'examiner_details' => ['nullable', 'array'],
            'examiner_details.examiner_name' => ['nullable', 'string', 'max:255'],
            'examiner_details.examiner_role' => ['nullable', 'string', 'max:255'],
            'examiner_details.license_number' => ['nullable', 'string', 'max:255'],
            'examiner_details.signature_path' => ['nullable', 'string', 'max:2048'],
            'examiner_details.examiner_user_id' => ['nullable', 'integer', $this->tenantDoctorRule()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'doctor_name' => $this->normalizeIntegerInput($this->input('doctor_name')),
            'patient_id' => $this->normalizeIntegerInput($this->input('patient_id')),
            'complaints' => $this->normalizeComplaints($this->input('complaints')),
            'visual_acuity' => $this->cleanNestedStructure($this->input('visual_acuity')),
            'refraction' => $this->cleanNestedStructure($this->input('refraction')),
            'eye_examination' => $this->cleanNestedStructure($this->input('eye_examination')),
            'intraocular_pressure' => $this->cleanNestedStructure($this->input('intraocular_pressure')),
            'medical_history' => $this->normalizeMedicalHistory($this->input('medical_history')),
            'diagnosis' => $this->normalizeDiagnosis($this->input('diagnosis')),
            'medicines' => $this->normalizeMedicines($this->input('medicines')),
            'glasses_prescription' => $this->cleanNestedStructure($this->input('glasses_prescription')),
            'eye_diagram' => $this->cleanNestedStructure($this->input('eye_diagram')),
            'examiner_details' => $this->normalizeExaminerDetails($this->input('examiner_details')),
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $complaints = $this->input('complaints');

                if (! is_array($complaints)) {
                    return;
                }

                $items = array_values(array_filter($complaints['items'] ?? [], fn ($item) => filled($item)));
                $custom = $complaints['custom'] ?? null;

                if ($items === [] && blank($custom)) {
                    $validator->errors()->add('complaints.items', __('Select at least one complaint or provide a custom complaint.'));
                }
            },
        ];
    }

    protected function tenantPatientRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $exists = EyePatient::query()
                ->whereKey($value)
                ->where('created_by', creatorId())
                ->exists();

            if (! $exists) {
                $fail(__('The selected patient is invalid.'));
            }
        };
    }

    protected function tenantDoctorRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $exists = User::query()
                ->whereKey($value)
                ->where('type', 'doctor')
                ->where('created_by', creatorId())
                ->whereExists(function ($query) use ($value) {
                    $query->selectRaw('1')
                        ->from((new OpticalDoctor())->getTable())
                        ->whereColumn('hospital_doctors.user_id', 'users.id')
                        ->where('hospital_doctors.user_id', $value)
                        ->where('hospital_doctors.created_by', creatorId())
                        ->where('hospital_doctors.status', '0');
                })
                ->exists();

            if (! $exists) {
                $fail(__('The selected doctor is invalid.'));
            }
        };
    }

    protected function normalizeIntegerInput(mixed $value): mixed
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return is_numeric($value) ? (int) $value : $value;
    }

    protected function cleanNestedStructure(mixed $value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        $cleaned = $this->cleanNestedValue($value);

        return is_array($cleaned) && $cleaned !== [] ? $cleaned : null;
    }

    protected function cleanNestedValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $cleaned = [];

            foreach ($value as $key => $nestedValue) {
                $normalized = $this->cleanNestedValue($nestedValue);

                if ($normalized === null || $normalized === [] || $normalized === '') {
                    continue;
                }

                $cleaned[$key] = $normalized;
            }

            return $cleaned;
        }

        if (is_string($value)) {
            $value = trim($value);

            return $value === '' ? null : $value;
        }

        return $value;
    }

    protected function normalizeComplaints(mixed $value): ?array
    {
        $complaints = $this->cleanNestedStructure($value);

        if ($complaints === null) {
            return null;
        }

        $complaints['items'] = array_values(array_filter($complaints['items'] ?? [], fn ($item) => filled($item)));

        return $complaints;
    }

    protected function normalizeMedicalHistory(mixed $value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        $normalized = [];

        foreach (['tbut', 'blood_pressure', 'past_history', 'other_diseases'] as $section) {
            $sectionValue = $value[$section] ?? null;

            if (! is_array($sectionValue)) {
                continue;
            }

            $checked = filter_var($sectionValue['checked'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            $notes = isset($sectionValue['notes']) && is_string($sectionValue['notes'])
                ? trim($sectionValue['notes'])
                : null;

            if (! $checked && blank($notes)) {
                continue;
            }

            $normalized[$section] = [
                'checked' => $checked,
                'notes' => blank($notes) ? null : $notes,
            ];
        }

        return $normalized === [] ? null : $normalized;
    }

    protected function normalizeDiagnosis(mixed $value): ?array
    {
        $diagnosis = $this->cleanNestedStructure($value);

        if ($diagnosis === null) {
            return null;
        }

        if (isset($diagnosis['secondary']) && is_array($diagnosis['secondary'])) {
            $diagnosis['secondary'] = array_values(array_filter($diagnosis['secondary'], fn ($item) => filled($item)));

            if ($diagnosis['secondary'] === []) {
                unset($diagnosis['secondary']);
            }
        }

        return $diagnosis === [] ? null : $diagnosis;
    }

    protected function normalizeMedicines(mixed $value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        $medicines = [];

        foreach ($value as $row) {
            $normalizedRow = $this->cleanNestedStructure($row);

            if ($normalizedRow === null) {
                continue;
            }

            $medicines[] = $normalizedRow;
        }

        return $medicines === [] ? null : array_values($medicines);
    }

    protected function normalizeExaminerDetails(mixed $value): ?array
    {
        $details = $this->cleanNestedStructure($value);

        if ($details === null) {
            return null;
        }

        if (array_key_exists('examiner_user_id', $details)) {
            $details['examiner_user_id'] = $this->normalizeIntegerInput($details['examiner_user_id']);
        }

        return $details;
    }
}
