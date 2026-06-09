<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Http\Requests\StoreBrandSettingRequest;

class BrandSettingController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-beauty-brand-settings')){
            $settings = BeautySetup::where('created_by', creatorId())
                ->whereIn('key', ['logo', 'favicon', 'footer_text', 'footer_description', 'beauty_spa_store_name'])
                ->pluck('value', 'key')
                ->toArray();

            return Inertia::render('BeautySpaManagement/SystemSetup/brand-settings', [
                'settings' => $settings
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBrandSettingRequest $request)
    {
        if(Auth::user()->can('edit-beauty-brand-settings')){
            $validated = $request->validated();
            $logo      = null;
            $favicon   = null;

            if (!empty($validated['logo'])) {
                $logo = basename($validated['logo']);
            }
            if (!empty($validated['favicon'])) {
                $favicon = basename($validated['favicon']);
            }

            $settings = [
                'logo'                  => $logo,
                'favicon'               => $favicon,
                'footer_text'           => $validated['footer_text'],
                'footer_description'    => $validated['footer_description'],
                'beauty_spa_store_name' => $validated['beauty_spa_store_name'],
            ];

            foreach($settings as $key => $value) {
                BeautySetup::updateOrCreate(
                    ['key' => $key, 'created_by' => creatorId()],
                    [
                        'value'      => $value,
                        'creator_id' => Auth::id(),
                        'created_by' => creatorId()
                    ]
                );
            }

            return redirect()->back()->with('success', __('The brand setting details have been saved successfully.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}