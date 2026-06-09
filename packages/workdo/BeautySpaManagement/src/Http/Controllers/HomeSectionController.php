<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreHomeSectionRequest;

class HomeSectionController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-home-section')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/home-section', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreHomeSectionRequest $request)
    {
        if (Auth::user()->can('edit-beauty-home-section')) {
            $validated = $request->validated();

            $homeSectionData = [
                'services_title' => $validated['services_title'] ?? '',
                'services_description' => $validated['services_description'] ?? '',
                'offers_title' => $validated['offers_title'] ?? '',
                'offers_description' => $validated['offers_description'] ?? '',
            ];

            BeautySetup::updateOrCreate(
                ['key' => 'home_section', 'created_by' => creatorId()],
                [
                    'value' => json_encode($homeSectionData),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The home section has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}