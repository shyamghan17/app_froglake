<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioServiceCategoryRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioServiceCategoryRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioServiceCategory;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioServiceCategory;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioServiceCategory;

class PhotoStudioServiceCategoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-service-category')) {
            $serviceCategories = PhotoStudioServiceCategory::where(function ($q) {
                if (Auth::user()->can('manage-any-photo-studio-service-category')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-photo-studio-service-category')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->when(request('search'), fn($q) => $q->where('name', 'like', '%' . request('search') . '%'))
                ->when(request('status') && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('PhotoStudioManagement/SystemSetup/ServiceCategories/Index', [
                'serviceCategories' => $serviceCategories,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioServiceCategoryRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-service-category')) {
            $validated = $request->validated();

            $serviceCategory              = new PhotoStudioServiceCategory();
            $serviceCategory->name        = $validated['name'];
            $serviceCategory->description = $validated['description'] ?? null;
            $serviceCategory->status      = $validated['status'];
            $serviceCategory->creator_id  = Auth::id();
            $serviceCategory->created_by  = creatorId();
            $serviceCategory->save();

            CreatePhotoStudioServiceCategory::dispatch($request, $serviceCategory);

            return redirect()->back()->with('success', __('The service category has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioServiceCategoryRequest $request, PhotoStudioServiceCategory $service_category)
    {
        if (Auth::user()->can('edit-photo-studio-service-category')) {
            $validated = $request->validated();
            $service_category->name        = $validated['name'];
            $service_category->description = $validated['description'] ?? $service_category->description;
            $service_category->status      = $validated['status'];
            $service_category->save();

            UpdatePhotoStudioServiceCategory::dispatch($request, $service_category);

            return redirect()->back()->with('success', __('The service category has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioServiceCategory $service_category)
    {
        if (Auth::user()->can('delete-photo-studio-service-category')) {
            DestroyPhotoStudioServiceCategory::dispatch($service_category);
            $service_category->delete();

            return redirect()->back()->with('success', __('The service category has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
