<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use App\Models\User;
use Workdo\OpticalAndEyeCareCenter\Models\EyeCareAppoinment;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\StoreEyeCareAppoinmentRequest;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\UpdateEyeCareAppoinmentRequest;
use Workdo\OpticalAndEyeCareCenter\Events\CreateEyeCareAppoinment;
use Workdo\OpticalAndEyeCareCenter\Events\UpdateEyeCareAppoinment;
use Workdo\OpticalAndEyeCareCenter\Events\DestroyEyeCareAppoinment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;

class EyeCareAppoinmentController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-eye-care-appoinments')){
            $eyecareappoinments = EyeCareAppoinment::query()
                ->with(['patient', 'doctor:id,name'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-eye-care-appoinments')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-eye-care-appoinments')) {
                        $q->where(function($subQ) {
                            $subQ->where('creator_id', Auth::id())
                                 ->orWhere('doctor_name', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('doctor_name'), function($q) {
                    $q->where(function($query) {
                    $query->where('doctor_name', 'like', '%' . request('doctor_name') . '%');
                    });
                })
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('appointment_type') !== null && request('appointment_type') !== '', fn($q) => $q->where('appointment_type', request('appointment_type')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('OpticalAndEyeCareCenter/EyeCareAppoinments/Index', [
                'eyecareappoinments' => $eyecareappoinments,
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

    public function store(StoreEyeCareAppoinmentRequest $request)
    {
        if(Auth::user()->can('create-eye-care-appoinments')){
            $validated = $request->validated();

            $eyecareappoinment                       = new EyeCareAppoinment();
            $eyecareappoinment->doctor_name          = $validated['doctor_name'];
            $eyecareappoinment->appointment_datetime = $validated['appointment_datetime'];
            $eyecareappoinment->status               = $validated['status'];
            $eyecareappoinment->appointment_type     = $validated['appointment_type'];
            $eyecareappoinment->notes                = $validated['notes'];
            $eyecareappoinment->patient_id           = $validated['patient_id'];
            $eyecareappoinment->creator_id           = Auth::id();
            $eyecareappoinment->created_by           = creatorId();
            $eyecareappoinment->save();

            CreateEyeCareAppoinment::dispatch($request, $eyecareappoinment);

            return redirect()->route('optical-and-eye-care-center.eye-care-appoinments.index')->with('success', __('The eye care appoinment has been created successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-care-appoinments.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateEyeCareAppoinmentRequest $request, EyeCareAppoinment $eyecareappoinment)
    {
        if(Auth::user()->can('edit-eye-care-appoinments')){
            $validated = $request->validated();

            $eyecareappoinment->doctor_name          = $validated['doctor_name'];
            $eyecareappoinment->appointment_datetime = $validated['appointment_datetime'];
            $eyecareappoinment->status               = $validated['status'];
            $eyecareappoinment->appointment_type     = $validated['appointment_type'];
            $eyecareappoinment->notes                = $validated['notes'];
            $eyecareappoinment->patient_id           = $validated['patient_id'];

            $eyecareappoinment->save();

            UpdateEyeCareAppoinment::dispatch($request, $eyecareappoinment);

            return redirect()->back()->with('success', __('The eye care appoinment details are updated successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-care-appoinments.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(EyeCareAppoinment $eyecareappoinment)
    {
        if(Auth::user()->can('delete-eye-care-appoinments')){
            DestroyEyeCareAppoinment::dispatch($eyecareappoinment);

            $eyecareappoinment->delete();

            return redirect()->back()->with('success', __('The eye care appoinment has been deleted.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eye-care-appoinments.index')->with('error', __('Permission denied'));
        }
    }
}
