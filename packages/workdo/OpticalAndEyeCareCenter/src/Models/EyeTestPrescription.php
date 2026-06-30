<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;

class EyeTestPrescription extends Model
{
    use HasFactory;

    protected $appends = [
        'integration_artifacts',
    ];

    protected $fillable = [
        'doctor_name',
        'test_date',
        'follow_up_date',
        'test_results',
        'prescription_details',
        'prescription_expiry_date',
        'notes',
        'clinical_schema_version',
        'complaints',
        'visual_acuity',
        'refraction',
        'eye_examination',
        'intraocular_pressure',
        'medical_history',
        'diagnosis',
        'medicines',
        'glasses_prescription',
        'eye_diagram',
        'examiner_details',
        'patient_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
            'follow_up_date' => 'date',
            'prescription_expiry_date' => 'date',
            'clinical_schema_version' => 'integer',
            'complaints' => 'array',
            'visual_acuity' => 'array',
            'refraction' => 'array',
            'eye_examination' => 'array',
            'intraocular_pressure' => 'array',
            'medical_history' => 'array',
            'diagnosis' => 'array',
            'medicines' => 'array',
            'glasses_prescription' => 'array',
            'eye_diagram' => 'array',
            'examiner_details' => 'array',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(EyePatient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_name');
    }

    public function getIntegrationArtifactsAttribute(): array
    {
        return [
            'legacy_text' => [
                'test_results' => $this->test_results,
                'prescription_details' => $this->prescription_details,
                'notes' => $this->notes,
                'source' => $this->clinical_schema_version ? 'compatibility-fallback' : 'legacy-record',
            ],
            'eyewear_order' => $this->buildEyewearOrderArtifact(),
            'print_layout' => $this->buildPrintLayoutArtifact(),
        ];
    }

    protected function buildEyewearOrderArtifact(): array
    {
        $glassesPrescription = is_array($this->glasses_prescription) ? $this->glasses_prescription : [];
        $legacyText = $this->prescription_details;
        $right = $this->normalizeEyeValues($glassesPrescription['right'] ?? null);
        $left = $this->normalizeEyeValues($glassesPrescription['left'] ?? null);
        $notes = $this->normalizeNullableString($glassesPrescription['notes'] ?? null);
        $hasStructuredValues = $right !== [] || $left !== [] || filled($notes);

        return [
            'ready' => $hasStructuredValues || filled($legacyText),
            'source' => $hasStructuredValues ? 'structured' : (filled($legacyText) ? 'legacy-text' : 'none'),
            'order_prefill' => [
                'prescription_id' => $this->id,
                'patient_id' => $this->patient_id,
                'doctor_user_id' => $this->doctor_name ? (int) $this->doctor_name : null,
                'examiner_user_id' => $this->normalizeNullableInt(data_get($this->examiner_details, 'examiner_user_id')),
                'prescription_expiry_date' => optional($this->prescription_expiry_date)->toDateString(),
                'right' => $right,
                'left' => $left,
                'notes' => $notes,
                'legacy_prescription_details' => $legacyText,
                'summary' => $hasStructuredValues
                    ? $this->buildEyewearSummary($right, $left, $notes)
                    : $legacyText,
            ],
        ];
    }

    protected function buildPrintLayoutArtifact(): array
    {
        return [
            'status' => 'available',
            'layout_key' => 'eye-test-prescription-clinical-v1',
            'page_component' => 'OpticalAndEyeCareCenter/EyeTestPrescriptions/Print',
            'sections' => [
                $this->buildPrintSection('patient_information', 'Patient Information', filled($this->patient_id) || filled($this->doctor_name) || filled($this->test_date)),
                $this->buildPrintSection('complaints', 'Chief Complaints', $this->sectionHasContent($this->complaints)),
                $this->buildPrintSection('vision_refraction', 'Vision & Refraction', $this->sectionHasContent($this->visual_acuity) || $this->sectionHasContent($this->refraction)),
                $this->buildPrintSection('eye_examination', 'Eye Examination', $this->sectionHasContent($this->eye_examination)),
                $this->buildPrintSection('intraocular_pressure', 'Intraocular Pressure', $this->sectionHasContent($this->intraocular_pressure)),
                $this->buildPrintSection('medical_history', 'Medical History', $this->sectionHasContent($this->medical_history)),
                $this->buildPrintSection('diagnosis', 'Diagnosis', $this->sectionHasContent($this->diagnosis)),
                $this->buildPrintSection('medicines', 'Medicines', $this->sectionHasContent($this->medicines)),
                $this->buildPrintSection('glasses_prescription', 'Glasses Prescription', $this->sectionHasContent($this->glasses_prescription) || filled($this->prescription_details)),
                $this->buildPrintSection('notes', 'Notes', filled($this->notes)),
                $this->buildPrintSection('examiner_details', 'Examiner Details', $this->sectionHasContent($this->examiner_details)),
                $this->buildPrintSection('legacy_text', 'Legacy Compatibility Text', filled($this->test_results) || filled($this->prescription_details)),
            ],
        ];
    }

    protected function buildPrintSection(string $key, string $label, bool $hasContent): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'has_content' => $hasContent,
        ];
    }

    protected function buildEyewearSummary(array $right, array $left, ?string $notes): ?string
    {
        $parts = array_filter([
            $this->buildEyeSummary('Right', $right),
            $this->buildEyeSummary('Left', $left),
            filled($notes) ? 'Notes: ' . $notes : null,
        ]);

        return $parts === [] ? null : implode('; ', $parts);
    }

    protected function buildEyeSummary(string $label, array $values): ?string
    {
        if ($values === []) {
            return null;
        }

        $segments = [];

        foreach ([
            'sphere' => 'SPH',
            'cylinder' => 'CYL',
            'axis' => 'Axis',
            'vision' => 'Vision',
            'near_vision' => 'Near Vision',
            'add' => 'ADD',
        ] as $key => $display) {
            if (filled($values[$key] ?? null)) {
                $segments[] = $display . ': ' . $values[$key];
            }
        }

        return $segments === [] ? null : $label . ' [' . implode(', ', $segments) . ']';
    }

    protected function normalizeEyeValues(mixed $section): array
    {
        if (! is_array($section)) {
            return [];
        }

        $values = [];

        foreach (['sphere', 'cylinder', 'axis', 'vision', 'near_vision', 'add'] as $key) {
            $normalized = $this->normalizeNullableString($section[$key] ?? null);

            if ($normalized !== null) {
                $values[$key] = $normalized;
            }
        }

        return $values;
    }

    protected function sectionHasContent(mixed $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $nestedValue) {
                if ($this->sectionHasContent($nestedValue)) {
                    return true;
                }
            }

            return false;
        }

        return filled($value);
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    protected function normalizeNullableInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }
}
