<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreSocialLinksRequest;

class SocialLinksController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-social-links')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/social-links', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreSocialLinksRequest $request)
    {
        if (Auth::user()->can('edit-beauty-social-links')) {
            $validated = $request->validated();

            $socialLinksData = [
                'social_links' => $validated['social_links'],
            ];

            BeautySetup::updateOrCreate(
                ['key' => 'social_links', 'created_by' => creatorId()],
                [
                    'value'      => json_encode($socialLinksData),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The social links have been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}