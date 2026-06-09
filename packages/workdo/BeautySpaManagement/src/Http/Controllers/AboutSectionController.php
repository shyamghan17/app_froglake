<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreAboutSectionRequest;

class AboutSectionController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-about-section')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/about-section', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreAboutSectionRequest $request)
    {
        if (Auth::user()->can('edit-beauty-about-section')) {
            $validated = $request->validated();
            
            if (isset($validated['about_image']) && $validated['about_image']) {
                $validated['about_image'] = basename($validated['about_image']);
            }

            $aboutSectionData = [
                'about_image' => $validated['about_image'],
                'main_title'  => $validated['main_title'],
                'content'     => $validated['content'],
                'sub_text'    => $validated['sub_text'],
                'purpose_title' => $validated['purpose_title'] ?? '',
                'purpose_description' => $validated['purpose_description'] ?? '',
                'about_stats' => $validated['about_stats'],
            ];

            BeautySetup::updateOrCreate(
                ['key' => 'about_section', 'created_by' => creatorId()],
                [
                    'value'      => json_encode($aboutSectionData),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The about section has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}