<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreContactInfoRequest;

class ContactInfoController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-contact-info')) {
            $beautysetups = BeautySetup::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/contact-info', [
                'beautysetups' => $beautysetups,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreContactInfoRequest $request)
    {
        if (Auth::user()->can('edit-beauty-contact-info')) {
            $validated = $request->validated();

            $contactInfoData = [
                'header_title'          => $validated['header_title'],
                'header_description'    => $validated['header_description'],
                'location'              => $validated['location'],
                'phone_number'          => $validated['phone_number'],
                'email_address'         => $validated['email_address'],
                'location_icon'         => $validated['location_icon'],
                'phone_icon'            => $validated['phone_icon'],
                'email_icon'            => $validated['email_icon'],
                'map_title'             => $validated['map_title'],
                'map_subtext'           => $validated['map_subtext'],
                'map_iframe'            => $validated['map_iframe'],
                'follow_us_description' => $validated['follow_us_description'],
                'cta_title'             => $validated['cta_title'] ?? '',
                'cta_description'       => $validated['cta_description'] ?? '',
            ];

            BeautySetup::updateOrCreate(
                ['key' => 'contact_info', 'created_by' => creatorId()],
                [
                    'value'      => json_encode($contactInfoData),
                    'creator_id' => Auth::id()
                ]
            );

            return redirect()->back()->with('success', __('The contact info has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}