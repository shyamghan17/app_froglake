<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCameraKit;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioServiceRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioServiceRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioService;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioService;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioService;

class PhotoStudioServiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-service')) {
            $services = PhotoStudioService::where(function ($q) {
                if (Auth::user()->can('manage-any-photo-studio-service')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-photo-studio-service')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->when(request('search'), fn($q) => $q->where('name', 'like', '%' . request('search') . '%'))
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', (bool) request('status')))
                ->when(request('category_id'), fn($q) => $q->whereJsonContains('service_category_ids', (string) request('category_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $serviceCategories = PhotoStudioServiceCategory::where('created_by', creatorId())
                ->where('status', true)
                ->get(['id', 'name']);

            $cameraKits = PhotoStudioCameraKit::where('created_by', creatorId())
                ->where('status', 'available')
                ->get(['id', 'name']);

            return Inertia::render('PhotoStudioManagement/Services/Index', [
                'services'          => $services,
                'serviceCategories' => $serviceCategories,
                'cameraKits'        => $cameraKits,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioServiceRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-service')) {
            $validated = $request->validated();

            $service                       = new PhotoStudioService();
            $service->name                 = $validated['name'];
            $service->service_category_ids = $validated['service_category_ids'];
            $service->description          = $validated['description'] ?? null;
            $service->image                = isset($validated['image']) ? basename($validated['image']) : null;
            $service->price                = $validated['price'];
            $service->status               = $validated['status'];
            $service->camera_kit_ids       = $validated['camera_kit_ids'] ?? [];
            $service->creator_id           = Auth::id();
            $service->created_by           = creatorId();
            $service->save();

            CreatePhotoStudioService::dispatch($request, $service);

            return redirect()->back()->with('success', __('The service has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioServiceRequest $request, PhotoStudioService $service)
    {
        if (Auth::user()->can('edit-photo-studio-service')) {
            $validated = $request->validated();

            $service->name                 = $validated['name'];
            $service->service_category_ids = $validated['service_category_ids'];
            $service->description          = $validated['description'] ?? null;
            $service->image                = isset($validated['image']) ? basename($validated['image']) : $service->image;
            $service->price                = $validated['price'];
            $service->status               = $validated['status'];
            $service->camera_kit_ids       = $validated['camera_kit_ids'] ?? [];
            $service->save();

            UpdatePhotoStudioService::dispatch($request, $service);

            return redirect()->back()->with('success', __('The service has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioService $service)
    {
        if (Auth::user()->can('delete-photo-studio-service')) {
            DestroyPhotoStudioService::dispatch($service);
            $service->delete();

            return redirect()->back()->with('success', __('The service has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
