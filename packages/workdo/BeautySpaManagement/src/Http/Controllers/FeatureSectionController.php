<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreFeatureSectionRequest;

class FeatureSectionController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-feature-section')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/feature-section', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreFeatureSectionRequest $request)
    {
        if (Auth::user()->can('edit-beauty-feature-section')) {
            $validated = $request->validated();

            BeautySetup::updateOrCreate(
                ['key' => 'feature_section', 'created_by' => creatorId()],
                [
                    'value'      => json_encode($validated),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The feature section has been updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.feature-section.index')->with('error', __('Permission denied'));
        }
    }
}