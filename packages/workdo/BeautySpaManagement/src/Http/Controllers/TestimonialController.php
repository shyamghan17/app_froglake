<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreTestimonialRequest;

class TestimonialController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-testimonials')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/testimonials', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreTestimonialRequest $request)
    {
        if (Auth::user()->can('edit-beauty-testimonials')) {
            $validated = $request->validated();

            BeautySetup::updateOrCreate(
                ['key' => 'testimonials', 'created_by' => creatorId()],
                [
                    'value'      => json_encode($validated),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The testimonials have been updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.testimonials.index')->with('error', __('Permission denied'));
        }
    }
}