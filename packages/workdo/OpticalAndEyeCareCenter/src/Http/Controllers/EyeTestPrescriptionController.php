<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use App\Models\User;
use Workdo\OpticalAndEyeCareCenter\Models\EyeTestPrescription;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\StoreEyeTestPrescriptionRequest;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\UpdateEyeTestPrescriptionRequest;
use Workdo\OpticalAndEyeCareCenter\Events\CreateEyeTestPrescription;
use Workdo\OpticalAndEyeCareCenter\Events\UpdateEyeTestPrescription;
use Workdo\OpticalAndEyeCareCenter\Events\DestroyEyeTestPrescription;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;

class EyeTestPrescriptionController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-eye-test-prescriptions')){
            $user = Auth::user();
            $creatorId = creatorId();

            $eyetestprescriptions = EyeTestPrescription::query()
                ->with(['patient', 'doctor:id,name'])
                ->where('created_by', $creatorId)
                ->where(function($q) use ($user) {
                    if($user->can('manage-any-eye-test-prescriptions')) {
                        return;
                    }

                    if($user->can('manage-own-eye-test-prescriptions')) {
                        $q->where(function($subQ) use ($user) {
                            $subQ->where('creator_id', $user->id)
                                 ->orWhere('doctor_name', $user->id);
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('patient_id'), function($q) {
                    $q->where('patient_id', request('patient_id'));
                })
                ->when(request('doctor_name'), function($q) {
                    $q->where('doctor_name', request('doctor_name'));
                })
                ->when(request('test_date'), function($q) {
                    $q->whereDate('test_date', request('test_date'));
                })
                ->when(request('search'), function($q) {
                    $q->where(function($query) {
                        $searchValue = trim((string) request('search'));
                        $search = '%' . $searchValue . '%';
                        $normalizedPhoneSearch = $this->normalizePhoneSearch($searchValue);

                        $query->whereHas('doctor', function($doctorQuery) use ($search) {
                            $doctorQuery->where('name', 'like', $search);
                        })
                              ->orWhereHas('patient', function($q) use ($search) {
                                  $q->where('patient_name', 'like', $search);
                              })
                              ->orWhereHas('patient', function($q) use ($search) {
                                  $q->where('contact_no', 'like', $search);
                              });

                        if ($normalizedPhoneSearch !== null) {
                            $query->orWhereHas('patient', function($patientQuery) use ($normalizedPhoneSearch) {
                                $patientQuery->whereRaw(
                                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(contact_no, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') LIKE ?",
                                    ['%' . $normalizedPhoneSearch . '%']
                                );
                            });
                        }
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('OpticalAndEyeCareCenter/EyeTestPrescriptions/Index', [
                'eyetestprescriptions' => $eyetestprescriptions,
                'eyepatients' => EyePatient::where('created_by', $creatorId)->with('doctor')->select('id', 'patient_name', 'preferred_doctor')->get(),
                'opticaldoctors' => User::where('type', 'doctor')
                    ->where('created_by', $creatorId)
                    ->whereIn('id', OpticalDoctor::where('status', '0')->pluck('user_id'))
                    ->select('id', 'name', 'email')
                    ->get(),
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreEyeTestPrescriptionRequest $request)
    {
        if(Auth::user()->can('create-eye-test-prescriptions')){
            $validated = $request->validated();

            $eyetestprescription = new EyeTestPrescription();

            $this->applyStructuredPrescriptionPayload($eyetestprescription, $validated);
            $eyetestprescription->creator_id               = Auth::id();
            $eyetestprescription->created_by               = creatorId();
            $eyetestprescription->save();

            CreateEyeTestPrescription::dispatch($request, $eyetestprescription);

            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('success', __('The eye test prescription has been created successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateEyeTestPrescriptionRequest $request, EyeTestPrescription $eyetestprescription)
    {
        if(Auth::user()->can('edit-eye-test-prescriptions')){
            $this->ensureTenantOwnedPrescription($eyetestprescription);

            if (! $this->canManagePrescription($eyetestprescription)) {
                return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            $this->applyStructuredPrescriptionPayload($eyetestprescription, $validated);

            $eyetestprescription->save();

            UpdateEyeTestPrescription::dispatch($request, $eyetestprescription);

            return redirect()->back()->with('success', __('The eye test prescription details are updated successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(EyeTestPrescription $eyetestprescription)
    {
        if(Auth::user()->can('delete-eye-test-prescriptions')){
            $this->ensureTenantOwnedPrescription($eyetestprescription);

            if (! $this->canManagePrescription($eyetestprescription)) {
                return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
            }

            DestroyEyeTestPrescription::dispatch($eyetestprescription);

            $eyetestprescription->delete();

            return redirect()->back()->with('success', __('The eye test prescription has been deleted.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
        }
    }

    public function print(EyeTestPrescription $eyetestprescription)
    {
        if (! Auth::user()->can('view-eye-test-prescriptions')) {
            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
        }

        $this->ensureTenantOwnedPrescription($eyetestprescription);

        if (! $this->canManagePrescription($eyetestprescription)) {
            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
        }

        $eyetestprescription->load([
            'patient:id,patient_name,dob,gender,contact_no,address',
            'doctor:id,name,email',
        ]);

        return Inertia::render('OpticalAndEyeCareCenter/EyeTestPrescriptions/Print', [
            'eyetestprescription' => $eyetestprescription,
        ]);
    }



    private function applyStructuredPrescriptionPayload(EyeTestPrescription $eyetestprescription, array $validated): void
    {
        $structuredFields = [
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
        ];

        $eyetestprescription->patient_id = $validated['patient_id'];
        $eyetestprescription->doctor_name = $validated['doctor_name'];
        $eyetestprescription->test_date = $validated['test_date'];
        $eyetestprescription->follow_up_date = $validated['follow_up_date'] ?? null;
        $eyetestprescription->prescription_expiry_date = $validated['prescription_expiry_date'] ?? null;
        $eyetestprescription->notes = $validated['notes'] ?? null;

        foreach ($structuredFields as $field) {
            $eyetestprescription->{$field} = $this->normalizeStructuredSection($validated[$field] ?? null);
        }

        $hasStructuredPayload = $this->hasStructuredPayload($validated);

        $eyetestprescription->clinical_schema_version = $hasStructuredPayload
            ? 2
            : $eyetestprescription->clinical_schema_version;

        $eyetestprescription->test_results = $hasStructuredPayload
            ? $this->buildTestResultsFallback($validated) ?? ($validated['test_results'] ?? null)
            : ($validated['test_results'] ?? null);

        $eyetestprescription->prescription_details = $hasStructuredPayload
            ? $this->buildPrescriptionDetailsFallback($validated) ?? ($validated['prescription_details'] ?? null)
            : ($validated['prescription_details'] ?? null);
    }

    private function ensureTenantOwnedPrescription(EyeTestPrescription $eyetestprescription): void
    {
        abort_unless((int) $eyetestprescription->created_by === (int) creatorId(), 404);
    }

    private function canManagePrescription(EyeTestPrescription $eyetestprescription): bool
    {
        if (Auth::user()->can('manage-any-eye-test-prescriptions')) {
            return true;
        }

        if (! Auth::user()->can('manage-own-eye-test-prescriptions')) {
            return false;
        }

        return (int) $eyetestprescription->creator_id === (int) Auth::id()
            || (int) $eyetestprescription->doctor_name === (int) Auth::id();
    }

    private function normalizePhoneSearch(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value);

        return $digits !== '' ? $digits : null;
    }

    private function hasStructuredPayload(array $validated): bool
    {
        foreach ([
            'follow_up_date',
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
        ] as $field) {
            $value = $validated[$field] ?? null;

            if (is_array($value) && $value !== []) {
                return true;
            }

            if (! is_array($value) && filled($value)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeStructuredSection(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $normalized = [];

        foreach ($value as $key => $nestedValue) {
            $cleaned = $this->normalizeStructuredSection($nestedValue);

            if ($cleaned === null || $cleaned === [] || $cleaned === '') {
                continue;
            }

            $normalized[$key] = $cleaned;
        }

        return $normalized === [] ? null : $normalized;
    }

    private function buildTestResultsFallback(array $validated): ?string
    {
        $lines = array_filter([
            $this->formatComplaintsFallback($validated['complaints'] ?? null),
            $this->formatPerEyeFallback('Visual Acuity', $validated['visual_acuity'] ?? null, [
                'distance' => 'Distance',
                'near' => 'Near',
                'pinhole' => 'Pinhole',
                'with_glasses' => 'With Glasses',
                'without_glasses' => 'Without Glasses',
            ]),
            $this->formatPerEyeFallback('Refraction', $validated['refraction'] ?? null, [
                'sphere' => 'SPH',
                'cylinder' => 'CYL',
                'axis' => 'Axis',
                'vision' => 'Vision',
                'near_vision' => 'Near Vision',
                'add' => 'ADD',
            ]),
            $this->formatPerEyeFallback('Eye Examination', $validated['eye_examination'] ?? null, [
                'lid' => 'Lid',
                'conjunctiva' => 'Conjunctiva',
                'cornea' => 'Cornea',
                'anterior_chamber' => 'Anterior Chamber',
                'iris' => 'Iris',
                'pupil' => 'Pupil',
                'lens' => 'Lens',
                'vitreous' => 'Vitreous',
                'fundus' => 'Fundus',
                'colour_vision' => 'Colour Vision',
            ]),
            $this->formatIntraocularPressureFallback($validated['intraocular_pressure'] ?? null),
            $this->formatDiagnosisFallback($validated['diagnosis'] ?? null),
        ]);

        return $lines === [] ? null : implode("\n", $lines);
    }

    private function buildPrescriptionDetailsFallback(array $validated): ?string
    {
        $lines = array_filter([
            $this->formatMedicinesFallback($validated['medicines'] ?? null),
            $this->formatPerEyeFallback('Glasses Prescription', $validated['glasses_prescription'] ?? null, [
                'sphere' => 'SPH',
                'cylinder' => 'CYL',
                'axis' => 'Axis',
                'vision' => 'Vision',
                'near_vision' => 'Near Vision',
                'add' => 'ADD',
            ], ['notes']),
            $this->formatMedicalHistoryFallback($validated['medical_history'] ?? null),
            $this->formatExaminerFallback($validated['examiner_details'] ?? null),
            filled($validated['follow_up_date'] ?? null) ? 'Follow Up Date: ' . $validated['follow_up_date'] : null,
        ]);

        return $lines === [] ? null : implode("\n", $lines);
    }

    private function formatComplaintsFallback(?array $complaints): ?string
    {
        if (! is_array($complaints)) {
            return null;
        }

        $parts = [];
        $items = array_filter($complaints['items'] ?? []);

        if ($items !== []) {
            $parts[] = 'Items: ' . implode(', ', $items);
        }

        if (filled($complaints['custom'] ?? null)) {
            $parts[] = 'Custom: ' . $complaints['custom'];
        }

        if (filled($complaints['affected_eye'] ?? null)) {
            $parts[] = 'Affected Eye: ' . ucfirst((string) $complaints['affected_eye']);
        }

        return $parts === [] ? null : 'Complaints - ' . implode('; ', $parts);
    }

    private function formatPerEyeFallback(string $label, ?array $section, array $fieldLabels, array $topLevelKeys = []): ?string
    {
        if (! is_array($section)) {
            return null;
        }

        $parts = [];

        foreach (['right' => 'Right', 'left' => 'Left'] as $eyeKey => $eyeLabel) {
            if (! isset($section[$eyeKey]) || ! is_array($section[$eyeKey])) {
                continue;
            }

            $values = [];

            foreach ($fieldLabels as $field => $fieldLabel) {
                if (filled($section[$eyeKey][$field] ?? null)) {
                    $values[] = $fieldLabel . ': ' . $section[$eyeKey][$field];
                }
            }

            if ($values !== []) {
                $parts[] = $eyeLabel . ' [' . implode(', ', $values) . ']';
            }
        }

        foreach ($topLevelKeys as $key) {
            if (filled($section[$key] ?? null)) {
                $parts[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $section[$key];
            }
        }

        return $parts === [] ? null : $label . ' - ' . implode('; ', $parts);
    }

    private function formatIntraocularPressureFallback(?array $section): ?string
    {
        if (! is_array($section)) {
            return null;
        }

        $parts = [];
        $unit = $section['unit'] ?? null;

        foreach (['right' => 'Right', 'left' => 'Left'] as $eyeKey => $eyeLabel) {
            if (filled($section[$eyeKey] ?? null)) {
                $suffix = filled($unit) ? ' ' . $unit : '';
                $parts[] = $eyeLabel . ': ' . $section[$eyeKey] . $suffix;
            }
        }

        return $parts === [] ? null : 'IOP - ' . implode(', ', $parts);
    }

    private function formatMedicalHistoryFallback(?array $section): ?string
    {
        if (! is_array($section)) {
            return null;
        }

        $parts = [];

        foreach ([
            'tbut' => 'TBUT',
            'blood_pressure' => 'Blood Pressure',
            'past_history' => 'Past History',
            'other_diseases' => 'Other Diseases',
        ] as $key => $label) {
            if (! isset($section[$key]) || ! is_array($section[$key])) {
                continue;
            }

            $entry = [];

            if (($section[$key]['checked'] ?? false) === true) {
                $entry[] = 'checked';
            }

            if (filled($section[$key]['notes'] ?? null)) {
                $entry[] = $section[$key]['notes'];
            }

            if ($entry !== []) {
                $parts[] = $label . ' (' . implode('; ', $entry) . ')';
            }
        }

        return $parts === [] ? null : 'Medical History - ' . implode(', ', $parts);
    }

    private function formatDiagnosisFallback(?array $section): ?string
    {
        if (! is_array($section)) {
            return null;
        }

        $parts = [];

        if (filled($section['primary'] ?? null)) {
            $parts[] = 'Primary: ' . $section['primary'];
        }

        if (! empty($section['secondary']) && is_array($section['secondary'])) {
            $parts[] = 'Secondary: ' . implode(', ', array_filter($section['secondary']));
        }

        if (filled($section['summary'] ?? null)) {
            $parts[] = 'Summary: ' . $section['summary'];
        }

        return $parts === [] ? null : 'Diagnosis - ' . implode('; ', $parts);
    }

    private function formatMedicinesFallback(?array $medicines): ?string
    {
        if (! is_array($medicines) || $medicines === []) {
            return null;
        }

        $rows = [];

        foreach ($medicines as $medicine) {
            if (! is_array($medicine) || blank($medicine['medicine'] ?? null)) {
                continue;
            }

            $parts = [$medicine['medicine']];

            if (filled($medicine['eye'] ?? null)) {
                $parts[] = 'Eye: ' . ucfirst((string) $medicine['eye']);
            }

            if (filled($medicine['frequency'] ?? null)) {
                $parts[] = 'Frequency: ' . $medicine['frequency'];
            }

            if (filled($medicine['duration'] ?? null)) {
                $parts[] = 'Duration: ' . $medicine['duration'];
            }

            if (filled($medicine['instructions'] ?? null)) {
                $parts[] = 'Instructions: ' . $medicine['instructions'];
            }

            $rows[] = implode(', ', $parts);
        }

        return $rows === [] ? null : 'Medicines - ' . implode(' | ', $rows);
    }

    private function formatExaminerFallback(?array $section): ?string
    {
        if (! is_array($section)) {
            return null;
        }

        $parts = array_filter([
            filled($section['examiner_name'] ?? null) ? 'Name: ' . $section['examiner_name'] : null,
            filled($section['examiner_role'] ?? null) ? 'Role: ' . $section['examiner_role'] : null,
            filled($section['license_number'] ?? null) ? 'License: ' . $section['license_number'] : null,
        ]);

        return $parts === [] ? null : 'Examiner - ' . implode(', ', $parts);
    }
}
