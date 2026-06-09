<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCameraKit;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentTag;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioCameraKitRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioCameraKitRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioCameraKit;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioCameraKit;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioCameraKit;

class PhotoStudioCameraKitController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-camera-kit')) {
            $cameraKits = PhotoStudioCameraKit::with('equipmentType')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-photo-studio-camera-kit')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-photo-studio-camera-kit')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), fn($q) => $q->where('name', 'like', '%' . request('search') . '%'))
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('equipment_type_id'), fn($q) => $q->where('equipment_type_id', request('equipment_type_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $equipmentTags = PhotoStudioEquipmentTag::where('created_by', creatorId())
                ->where('status', true)
                ->get(['id', 'name']);

            $equipmentTypes = PhotoStudioEquipmentType::where('created_by', creatorId())
                ->where('status', true)
                ->get(['id', 'name']);

            return Inertia::render('PhotoStudioManagement/CameraKits/Index', [
                'cameraKits'     => $cameraKits,
                'equipmentTags'  => $equipmentTags,
                'equipmentTypes' => $equipmentTypes,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioCameraKitRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-camera-kit')) {
            $validated = $request->validated();

            $cameraKit                    = new PhotoStudioCameraKit();
            $cameraKit->name              = $validated['name'];
            $cameraKit->image             = basename($validated['image']);
            $cameraKit->description       = $validated['description'];
            $cameraKit->tags              = $validated['tags'];
            $cameraKit->specifications    = $validated['specifications'];
            $cameraKit->equipment_type_id = $validated['equipment_type_id'];
            $cameraKit->status            = $validated['status'];
            $cameraKit->creator_id        = Auth::id();
            $cameraKit->created_by        = creatorId();
            $cameraKit->save();

            CreatePhotoStudioCameraKit::dispatch($request, $cameraKit);

            return redirect()->back()->with('success', __('The camera kit has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioCameraKitRequest $request, PhotoStudioCameraKit $camera_kit)
    {
        if (Auth::user()->can('edit-photo-studio-camera-kit')) {
            $validated = $request->validated();

            $camera_kit->name              = $validated['name'];
            $camera_kit->image             = basename($validated['image']);
            $camera_kit->description       = $validated['description'];
            $camera_kit->tags              = $validated['tags'];
            $camera_kit->specifications    = $validated['specifications'];
            $camera_kit->equipment_type_id = $validated['equipment_type_id'];
            $camera_kit->status            = $validated['status'];
            $camera_kit->save();

            UpdatePhotoStudioCameraKit::dispatch($request, $camera_kit);

            return redirect()->back()->with('success', __('The camera kit has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioCameraKit $camera_kit)
    {
        if (Auth::user()->can('delete-photo-studio-camera-kit')) {
            DestroyPhotoStudioCameraKit::dispatch($camera_kit);
            $camera_kit->delete();

            return redirect()->back()->with('success', __('The camera kit has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
