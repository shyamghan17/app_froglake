<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\Branch;
use Workdo\Rotas\Http\Requests\StoreRotasBranchRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasBranchRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Events\CreateBranch;
use Workdo\Rotas\Events\UpdateBranch;
use Workdo\Rotas\Events\DestroyBranch;

class RotasBranchController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-rotas-branches')){
            $branches = Branch::select('id', 'branch_name', 'created_at')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-branches')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-branches')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Rotas/SystemSetup/Branches/Index', [
                'branches' => $branches,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasBranchRequest $request)
    {
        if(Auth::user()->can('create-rotas-branches')){
            $validated = $request->validated();
            $branch = new Branch();
            $branch->branch_name = $validated['branch_name'];

            $branch->creator_id = Auth::id();
            $branch->created_by = creatorId();
            $branch->save();

            CreateBranch::dispatch($request, $branch);
            return back()->with('success', __('The branch has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasBranchRequest $request, Branch $branch)
    {
        if(Auth::user()->can('edit-rotas-branches')){
            $validated = $request->validated();
            $branch->branch_name = $validated['branch_name'];
            $branch->save();

            UpdateBranch::dispatch($request, $branch);

            return back()->with('success', __('The branch details are updated successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Branch $branch)
    {
        if(Auth::user()->can('delete-rotas-branches')){
            // Dispatch event for packages to handle their fields
            DestroyBranch::dispatch($branch);
            $branch->delete();

            return back()->with('success', __('The branch has been deleted.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }


}