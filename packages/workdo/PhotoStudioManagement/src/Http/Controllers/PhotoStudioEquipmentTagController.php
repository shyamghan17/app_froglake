<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentTag;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioEquipmentTagRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioEquipmentTagRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioEquipmentTag;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioEquipmentTag;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioEquipmentTag;

class PhotoStudioEquipmentTagController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-equipment-tag')) {
            $equipmentTags = PhotoStudioEquipmentTag::where(function ($q) {
                if (Auth::user()->can('manage-any-photo-studio-equipment-tag')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-photo-studio-equipment-tag')) {
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

            return Inertia::render('PhotoStudioManagement/SystemSetup/EquipmentTags/Index', [
                'equipmentTags' => $equipmentTags,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioEquipmentTagRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-equipment-tag')) {
            $validated = $request->validated();

            $equipmentTag              = new PhotoStudioEquipmentTag();
            $equipmentTag->name        = $validated['name'];
            $equipmentTag->description = $validated['description'] ?? null;
            $equipmentTag->status      = $validated['status'];
            $equipmentTag->creator_id  = Auth::id();
            $equipmentTag->created_by  = creatorId();
            $equipmentTag->save();

            CreatePhotoStudioEquipmentTag::dispatch($request, $equipmentTag);

            return redirect()->back()->with('success', __('The equipment tag has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioEquipmentTagRequest $request, PhotoStudioEquipmentTag $equipment_tag)
    {
        if (Auth::user()->can('edit-photo-studio-equipment-tag')) {
            $validated = $request->validated();

            $equipment_tag->name        = $validated['name'];
            $equipment_tag->description = $validated['description'] ?? $equipment_tag->description;
            $equipment_tag->status      = $validated['status'];
            $equipment_tag->save();

            UpdatePhotoStudioEquipmentTag::dispatch($request, $equipment_tag);

            return redirect()->back()->with('success', __('The equipment tag has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioEquipmentTag $equipment_tag)
    {
        if (Auth::user()->can('delete-photo-studio-equipment-tag')) {
            DestroyPhotoStudioEquipmentTag::dispatch($equipment_tag);
            $equipment_tag->delete();

            return redirect()->back()->with('success', __('The equipment tag has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
