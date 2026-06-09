<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCustomPage;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioCustomPageRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioCustomPageRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioCustomPage;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioCustomPage;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioCustomPage;

class PhotoStudioCustomPageController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-custom-pages')) {
            $customPages = PhotoStudioCustomPage::where(function ($q) {
                if (Auth::user()->can('manage-any-photo-studio-custom-pages')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-photo-studio-custom-pages')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->get();

            return Inertia::render('PhotoStudioManagement/SystemSetup/CustomPages/Index', [
                'customPages' => $customPages,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioCustomPageRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-custom-pages')) {
            $validated = $request->validated();

            $customPage                     = new PhotoStudioCustomPage();
            $customPage->title              = $validated['title'];
            $customPage->contents           = $validated['contents'];
            $customPage->description        = $validated['description'] ?? null;
            $customPage->slug               = $validated['slug'];
            $customPage->enable_page_footer = $validated['enable_page_footer'] ?? 'off';
            $customPage->creator_id         = Auth::id();
            $customPage->created_by         = creatorId();
            $customPage->save();

            CreatePhotoStudioCustomPage::dispatch($request, $customPage);

            return redirect()->back()->with('success', __('The custom page has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioCustomPageRequest $request, $customPage)
    {
        if (Auth::user()->can('edit-photo-studio-custom-pages')) {
            $customPage = PhotoStudioCustomPage::where('id', $customPage)->where('created_by', creatorId())->firstOrFail();

            $validated = $request->validated();

            $customPage->title              = $validated['title'];
            $customPage->contents           = $validated['contents'];
            $customPage->description        = $validated['description'] ?? $customPage->description;
            $customPage->slug               = $validated['slug'];
            $customPage->enable_page_footer = $validated['enable_page_footer'] ?? 'off';
            $customPage->save();

            UpdatePhotoStudioCustomPage::dispatch($request, $customPage);

            return redirect()->back()->with('success', __('The custom page has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($customPage)
    {
        if (Auth::user()->can('delete-photo-studio-custom-pages')) {
            $customPage = PhotoStudioCustomPage::where('id', $customPage)->where('created_by', creatorId())->firstOrFail();

            DestroyPhotoStudioCustomPage::dispatch($customPage);
            $customPage->delete();

            return redirect()->back()->with('success', __('The custom page has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
