<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Http\Requests\StoreCustomPageRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateCustomPageRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautyCustomPage;

class BeautyCustomPageController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-custom-pages')) {
            $custompages = BeautyCustomPage::where('created_by', creatorId())->get();

            return Inertia::render('BeautySpaManagement/SystemSetup/CustomPages/Index', [
                'custompages' => $custompages
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreCustomPageRequest $request)
    {
        if (Auth::user()->can('create-beauty-custom-pages')) {
            $validated = $request->validated();

            $custompage              = new BeautyCustomPage();
            $custompage->title       = $validated['title'];
            $custompage->slug        = $validated['slug'];
            $custompage->contents    = $validated['contents'];
            $custompage->description = $validated['description'];

            $custompage->creator_id = Auth::id();
            $custompage->created_by = creatorId();
            $custompage->save();


            return redirect()->route('beauty-spa-management.custom-pages.index')->with('success', __('The custom page has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.custom-pages.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateCustomPageRequest $request, BeautyCustomPage $custompage)
    {
        if (Auth::user()->can('edit-beauty-custom-pages')) {
            $validated = $request->validated();

            $custompage->title       = $validated['title'];
            $custompage->slug        = $validated['slug'];
            $custompage->contents    = $validated['contents'];
            $custompage->description = $validated['description'];

            $custompage->save();

            return redirect()->route('beauty-spa-management.custom-pages.index')->with('success', __('The custom page details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.custom-pages.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyCustomPage $custompage)
    {
        if (Auth::user()->can('delete-beauty-custom-pages')) {
            $custompage->delete();

            return redirect()->route('beauty-spa-management.custom-pages.index')->with('success', __('The custom page has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.custom-pages.index')->with('error', __('Permission denied'));
        }
    }
}
