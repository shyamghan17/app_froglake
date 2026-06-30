<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use App\Models\User;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\StoreEyePatientRequest;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\UpdateEyePatientRequest;
use Workdo\OpticalAndEyeCareCenter\Events\CreateEyePatient;
use Workdo\OpticalAndEyeCareCenter\Events\UpdateEyePatient;
use Workdo\OpticalAndEyeCareCenter\Events\DestroyEyePatient;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EyePatientController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-eye-patients')){
            $eyepatients = EyePatient::query()
                ->with(['doctor:id,name'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-eye-patients')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-eye-patients')) {
                         $q->where(function($subQ) {
                            $subQ->where('creator_id', Auth::id())
                                 ->orWhere('preferred_doctor', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('patient_name'), function($q) {
                    $q->where(function($query) {
                        $searchValue = trim((string) request('patient_name'));
                        $search = '%' . $searchValue . '%';
                        $normalizedPhoneSearch = $this->normalizePhoneSearch($searchValue);

                        $query->where('patient_name', 'like', $search)
                            ->orWhere('contact_no', 'like', $search);

                        if ($normalizedPhoneSearch !== null) {
                            $query->orWhereRaw(
                                "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(contact_no, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') LIKE ?",
                                ['%' . $normalizedPhoneSearch . '%']
                            );
                        }
                    });
                })
                ->when(request('gender') !== null && request('gender') !== '', fn($q) => $q->where('gender', request('gender')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $doctors = User::where('type', 'doctor')
                ->where('created_by', creatorId())
                ->whereIn('id', OpticalDoctor::where('created_by', creatorId())->where('status', '0')->pluck('user_id'))
                ->select('id', 'name', 'email')
                ->get();

            return Inertia::render('OpticalAndEyeCareCenter/EyePatients/Index', [
                'eyepatients' => $eyepatients,
                'doctors' => $doctors,

            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreEyePatientRequest $request)
    {
        if(Auth::user()->can('create-eye-patients')){
            $validated = $request->validated();

            $eyepatient                         = new EyePatient();
            $eyepatient->patient_name           = $validated['patient_name'];
            $eyepatient->dob                    = $validated['dob'];
            $eyepatient->gender                 = $validated['gender'];
            $eyepatient->contact_no             = $validated['contact_no'];
            $eyepatient->address                = $validated['address'];
            $eyepatient->medical_history        = $validated['medical_history'];
            $eyepatient->previous_prescriptions = $validated['previous_prescriptions'];
            $eyepatient->preferred_doctor       = $validated['preferred_doctor'];
            $eyepatient->creator_id             = Auth::id();
            $eyepatient->created_by             = creatorId();
            $eyepatient->save();

            CreateEyePatient::dispatch($request, $eyepatient);

            return redirect()->route('optical-and-eye-care-center.eye-patients.index')->with('success', __('The eye patient has been created successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-patients.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateEyePatientRequest $request, EyePatient $eyepatient)
    {
        if(Auth::user()->can('edit-eye-patients')){
            $validated = $request->validated();

            $eyepatient->patient_name           = $validated['patient_name'];
            $eyepatient->dob                    = $validated['dob'];
            $eyepatient->gender                 = $validated['gender'];
            $eyepatient->contact_no             = $validated['contact_no'];
            $eyepatient->address                = $validated['address'];
            $eyepatient->medical_history        = $validated['medical_history'];
            $eyepatient->previous_prescriptions = $validated['previous_prescriptions'];
            $eyepatient->preferred_doctor       = $validated['preferred_doctor'];
            $eyepatient->save();

            UpdateEyePatient::dispatch($request, $eyepatient);

            return redirect()->back()->with('success', __('The eye patient details are updated successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-patients.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(EyePatient $eyepatient)
    {
        if(Auth::user()->can('delete-eye-patients')){
            DestroyEyePatient::dispatch($eyepatient);

            $eyepatient->delete();

            return redirect()->back()->with('success', __('The eye patient has been deleted.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-patients.index')->with('error', __('Permission denied'));
        }
    }




    private function normalizePhoneSearch(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value);

        return $digits !== '' ? $digits : null;
    }
}
