<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Http\Requests\StoreCertificationRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateCertificationRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyCertification;
use Workdo\BeautySpaManagement\Events\DestroyBeautyCertification;
use Workdo\BeautySpaManagement\Events\UpdateBeautyCertification;
use Workdo\BeautySpaManagement\Models\BeautyCertification;
use Workdo\BeautySpaManagement\Models\BeautyTraining;

class BeautyCertificationController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-certifications')) {
            $certifications = BeautyCertification::query()
                ->with(['training'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-certifications')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-certifications')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('employee_name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('employee_name', 'like', '%' . request('employee_name') . '%');
                        $query->orWhere('certificate_name', 'like', '%' . request('employee_name') . '%');
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Certifications/Index', [
                'certifications' => $certifications,
                'trainings'      => BeautyTraining::where('created_by', creatorId())->select('id', 'training_name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreCertificationRequest $request)
    {
        if (Auth::user()->can('create-beauty-certifications')) {
            $validated = $request->validated();

            $certification                   = new BeautyCertification();
            $certification->employee_name    = $validated['employee_name'];
            $certification->certificate_name = $validated['certificate_name'];
            $certification->issued_date      = $validated['issued_date'];
            $certification->expiry_date      = $validated['expiry_date'];
            $certification->training_id      = $validated['training_id'];

            $certification->creator_id = Auth::id();
            $certification->created_by = creatorId();
            $certification->save();
            CreateBeautyCertification::dispatch($request, $certification);

            return redirect()->route('beauty-spa-management.certifications.index')->with('success', __('The certification has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.certifications.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateCertificationRequest $request, BeautyCertification $certification)
    {
        if (Auth::user()->can('edit-beauty-certifications')) {
            $validated = $request->validated();

            $certification->employee_name    = $validated['employee_name'];
            $certification->certificate_name = $validated['certificate_name'];
            $certification->issued_date      = $validated['issued_date'];
            $certification->expiry_date      = $validated['expiry_date'];
            $certification->training_id      = $validated['training_id'];

            $certification->save();
            UpdateBeautyCertification::dispatch($request, $certification);


            return redirect()->back()->with('success', __('The certification details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.certifications.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyCertification $certification)
    {
        if (Auth::user()->can('delete-beauty-certifications')) {
            DestroyBeautyCertification::dispatch($certification);
            $certification->delete();

            return redirect()->back()->with('success', __('The certification has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.certifications.index')->with('error', __('Permission denied'));
        }
    }
}
