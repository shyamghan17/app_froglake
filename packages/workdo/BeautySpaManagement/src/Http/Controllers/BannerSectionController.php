<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreBannerSectionRequest;

class BannerSectionController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-banner-section')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/banner-section', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBannerSectionRequest $request)
    {
        if (Auth::user()->can('edit-beauty-banner-section')) {
            $validated = $request->validated();

            BeautySetup::updateOrCreate(
                ['key' => 'banner_section', 'created_by' => creatorId()],
                [
                    'value'      => json_encode($validated),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The banner section has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}