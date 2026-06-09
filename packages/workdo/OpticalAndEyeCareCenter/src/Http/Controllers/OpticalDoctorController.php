<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use App\Models\User;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\StoreOpticalDoctorRequest;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\UpdateOpticalDoctorRequest;
use Workdo\OpticalAndEyeCareCenter\Events\CreateOpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Events\UpdateOpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Events\DestroyOpticalDoctor;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OpticalDoctorController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-optical-doctors')) {
            $opticaldoctors = OpticalDoctor::query()
                ->with(['user'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-optical-doctors')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-optical-doctors')) {
                       $q->where(function($subQ) {
                            $subQ->where('creator_id', Auth::id())
                                 ->orWhere('user_id', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('doctor_code'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('doctor_code', 'like', '%' . request('doctor_code') . '%')
                            ->orWhere('license_number', 'like', '%' . request('doctor_code') . '%')
                            ->orWhereHas('user', function ($userQuery) {
                                $userQuery->where('name', 'like', '%' . request('doctor_code') . '%')
                                    ->orWhere('email', 'like', '%' . request('doctor_code') . '%');
                            });
                    });
                })
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('gender') !== null && request('gender') !== '', fn($q) => $q->where('gender', request('gender')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $availableUsers = User::where('created_by', creatorId())
                ->where('type', 'doctor')
                ->where('created_by', creatorId())
                ->whereNotIn('id', OpticalDoctor::pluck('user_id'))
                ->select('id', 'name', 'email')
                ->get();

            return Inertia::render('OpticalAndEyeCareCenter/OpticalDoctors/Index', [
                'opticaldoctors' => $opticaldoctors,
                'users' => $availableUsers,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreOpticalDoctorRequest $request)
    {
        if (Auth::user()->can('create-optical-doctors')) {
            $validated = $request->validated();

            $opticaldoctor                             = new OpticalDoctor();
            $opticaldoctor->license_number             = $validated['license_number'];
            $opticaldoctor->gender                     = $validated['gender'];
            $opticaldoctor->years_of_experience        = $validated['years_of_experience'];
            $opticaldoctor->consultation_fee           = $validated['consultation_fee'];
            $opticaldoctor->qualifications             = $validated['qualifications'];
            $opticaldoctor->status                     = $validated['status'];
            $opticaldoctor->user_id                    = $validated['user_id'];
            $opticaldoctor->creator_id                 = Auth::id();
            $opticaldoctor->created_by                 = creatorId();
            $opticaldoctor->save();

            CreateOpticalDoctor::dispatch($request, $opticaldoctor);

            return redirect()->route('optical-and-eye-care-center.optical-doctors.index')->with('success', __('The doctor has been created successfully.'));
        } else {
            return redirect()->route('optical-and-eye-care-center.optical-doctors.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateOpticalDoctorRequest $request, OpticalDoctor $opticaldoctor)
    {
        if (Auth::user()->can('edit-optical-doctors')) {
            $validated = $request->validated();

            $opticaldoctor->license_number             = $validated['license_number'];
            $opticaldoctor->gender                     = $validated['gender'];
            $opticaldoctor->years_of_experience        = $validated['years_of_experience'];
            $opticaldoctor->consultation_fee           = $validated['consultation_fee'];
            $opticaldoctor->qualifications             = $validated['qualifications'];
            $opticaldoctor->status                     = $validated['status'];
            $opticaldoctor->user_id                    = $validated['user_id'];
                $opticaldoctor->save();

            UpdateOpticalDoctor::dispatch($request, $opticaldoctor);

            return redirect()->back()->with('success', __('The doctor details are updated successfully.'));
        } else {
            return redirect()->route('optical-and-eye-care-center.optical-doctors.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(OpticalDoctor $opticaldoctor)
    {
        if (Auth::user()->can('delete-optical-doctors')) {
            DestroyOpticalDoctor::dispatch($opticaldoctor);

            $opticaldoctor->delete();

            return redirect()->back()->with('success', __('The doctor has been deleted.'));
        } else {
            return redirect()->route('optical-and-eye-care-center.optical-doctors.index')->with('error', __('Permission denied'));
        }
    }
}
