<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Workdo\RepairManagementSystem\Models\RepairTechnician;
use Workdo\RepairManagementSystem\Http\Requests\StoreRepairTechnicianRequest;
use Workdo\RepairManagementSystem\Http\Requests\UpdateRepairTechnicianRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\RepairManagementSystem\Events\CreateRepairTechnician;
use Workdo\RepairManagementSystem\Events\UpdateRepairTechnician;
use Workdo\RepairManagementSystem\Events\DestroyRepairTechnician;


class RepairTechnicianController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-repair-technicians')){
            $repairtechnicians = RepairTechnician::query()

                ->where(function($q) {
                    if(Auth::user()->can('manage-any-repair-technicians')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-repair-technicians')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function($q) {
                    $search = request('search');
                    $q->where(function($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                              ->orWhere('email', 'like', '%' . $search . '%')
                              ->orWhere('mobile_no', 'like', '%' . $search . '%');
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('RepairManagementSystem/RepairTechnicians/Index', [
                'repairtechnicians' => $repairtechnicians,

            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRepairTechnicianRequest $request)
    {
        if(Auth::user()->can('create-repair-technicians')){
            $validated = $request->validated();

            $repairtechnician = new RepairTechnician();
            $repairtechnician->name = $validated['name'];
            $repairtechnician->email = $validated['email'];
            $repairtechnician->mobile_no = $validated['mobile_no'];

            $repairtechnician->creator_id = Auth::id();
            $repairtechnician->created_by = creatorId();
            $repairtechnician->save();

            CreateRepairTechnician::dispatch($request, $repairtechnician);

            return redirect()->route('repair-management-system.repair-technicians.index')->with('success', __('The Technician has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRepairTechnicianRequest $request, RepairTechnician $repair_technician)
    {
        if(Auth::user()->can('edit-repair-technicians')){
            if ($repair_technician->created_by == creatorId()) {
                $validated = $request->validated();

                $repair_technician->name = $validated['name'];
                $repair_technician->email = $validated['email'];
                $repair_technician->mobile_no = $validated['mobile_no'];
                $repair_technician->save();

                UpdateRepairTechnician::dispatch($request, $repair_technician);

                return back()->with('success', __('The Technician has been updated successfully.'));
            } else {
                return back()->with('error', __('Permission denied'));
            }
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(RepairTechnician $repair_technician)
    {
        if(Auth::user()->can('delete-repair-technicians')){
            if ($repair_technician->created_by == creatorId()) {
                DestroyRepairTechnician::dispatch($repair_technician);
                
                $repair_technician->delete();

                return back()->with('success', __('The Technician has been deleted.'));
            } else {
                return back()->with('error', __('Permission denied'));
            }
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }




}