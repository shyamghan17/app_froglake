<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyLoyaltyProgram;
use Workdo\BeautySpaManagement\Http\Requests\StoreBeautyLoyaltyProgramRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateBeautyLoyaltyProgramRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyLoyaltyProgram;
use Workdo\BeautySpaManagement\Events\DestroyBeautyLoyaltyProgram;
use Workdo\BeautySpaManagement\Events\UpdateBeautyLoyaltyProgram;

class BeautyLoyaltyProgramController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-loyalty-programs')) {
            $beautyloyaltyprograms = BeautyLoyaltyProgram::query()

                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-loyalty-programs')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-loyalty-programs')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('customer_name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('customer_name', 'like', '%' . request('customer_name') . '%');
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/LoyaltyPrograms/Index', [
                'beautyloyaltyprograms' => $beautyloyaltyprograms,

            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBeautyLoyaltyProgramRequest $request)
    {
        if (Auth::user()->can('create-beauty-loyalty-programs')) {
            $validated = $request->validated();

            $beautyloyaltyprogram                  = new BeautyLoyaltyProgram();
            $beautyloyaltyprogram->customer_name   = $validated['customer_name'];
            $beautyloyaltyprogram->points_earned   = $validated['points_earned'];
            $beautyloyaltyprogram->points_redeemed = $validated['points_redeemed'];
            $beautyloyaltyprogram->last_updated    = $validated['last_updated'];

            $beautyloyaltyprogram->creator_id = Auth::id();
            $beautyloyaltyprogram->created_by = creatorId();
            $beautyloyaltyprogram->save();
            CreateBeautyLoyaltyProgram::dispatch($request, $beautyloyaltyprogram);

            return redirect()->route('beauty-spa-management.beauty-loyalty-programs.index')->with('success', __('The loyalty program has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-loyalty-programs.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBeautyLoyaltyProgramRequest $request, BeautyLoyaltyProgram $beautyloyaltyprogram)
    {
        if (Auth::user()->can('edit-beauty-loyalty-programs')) {
            $validated = $request->validated();

            $beautyloyaltyprogram->customer_name   = $validated['customer_name'];
            $beautyloyaltyprogram->points_earned   = $validated['points_earned'];
            $beautyloyaltyprogram->points_redeemed = $validated['points_redeemed'];
            $beautyloyaltyprogram->last_updated    = $validated['last_updated'];

            $beautyloyaltyprogram->save();
            UpdateBeautyLoyaltyProgram::dispatch($request, $beautyloyaltyprogram);

            return redirect()->back()->with('success', __('The loyalty program details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-loyalty-programs.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyLoyaltyProgram $beautyloyaltyprogram)
    {
        if (Auth::user()->can('delete-beauty-loyalty-programs')) {
            DestroyBeautyLoyaltyProgram::dispatch($beautyloyaltyprogram);

            $beautyloyaltyprogram->delete();

            return redirect()->back()->with('success', __('The loyalty program has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.beauty-loyalty-programs.index')->with('error', __('Permission denied'));
        }
    }
}
