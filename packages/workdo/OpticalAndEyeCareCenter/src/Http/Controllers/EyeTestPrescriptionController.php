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
            $eyetestprescriptions = EyeTestPrescription::query()
                ->with(['patient', 'doctor:id,name'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-eye-test-prescriptions')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-eye-test-prescriptions')) {
                        $q->where(function($subQ) {
                            $subQ->where('creator_id', Auth::id())
                                 ->orWhere('doctor_name', Auth::id());
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
                        $query->where('doctor_name', 'like', '%' . request('search') . '%')
                              ->orWhereHas('patient', function($q) {
                                  $q->where('patient_name', 'like', '%' . request('search') . '%');
                              });
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('OpticalAndEyeCareCenter/EyeTestPrescriptions/Index', [
                'eyetestprescriptions' => $eyetestprescriptions,
                'eyepatients' => EyePatient::where('created_by', creatorId())->with('doctor')->select('id', 'patient_name', 'preferred_doctor')->get(),
                'opticaldoctors' => User::where('type', 'doctor')
                    ->where('created_by', creatorId())
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

            $eyetestprescription                           = new EyeTestPrescription();
            $eyetestprescription->doctor_name              = $validated['doctor_name'];
            $eyetestprescription->test_date                = $validated['test_date'];
            $eyetestprescription->test_results             = $validated['test_results'];
            $eyetestprescription->prescription_details     = $validated['prescription_details'];
            $eyetestprescription->prescription_expiry_date = $validated['prescription_expiry_date'];
            $eyetestprescription->notes                    = $validated['notes'];
            $eyetestprescription->patient_id               = $validated['patient_id'];
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
            $validated = $request->validated();

            $eyetestprescription->doctor_name              = $validated['doctor_name'];
            $eyetestprescription->test_date                = $validated['test_date'];
            $eyetestprescription->test_results             = $validated['test_results'];
            $eyetestprescription->prescription_details     = $validated['prescription_details'];
            $eyetestprescription->prescription_expiry_date = $validated['prescription_expiry_date'];
            $eyetestprescription->notes                    = $validated['notes'];
            $eyetestprescription->patient_id               = $validated['patient_id'];

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
            DestroyEyeTestPrescription::dispatch($eyetestprescription);

            $eyetestprescription->delete();

            return redirect()->back()->with('success', __('The eye test prescription has been deleted.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-test-prescriptions.index')->with('error', __('Permission denied'));
        }
    }




}
