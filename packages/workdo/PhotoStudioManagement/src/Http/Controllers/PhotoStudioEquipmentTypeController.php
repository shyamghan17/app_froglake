<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioEquipmentTypeRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioEquipmentTypeRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioEquipmentType;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioEquipmentType;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioEquipmentType;

class PhotoStudioEquipmentTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-equipment-type')) {
            $equipmentTypes = PhotoStudioEquipmentType::where(function ($q) {
                if (Auth::user()->can('manage-any-photo-studio-equipment-type')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-photo-studio-equipment-type')) {
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

            return Inertia::render('PhotoStudioManagement/SystemSetup/EquipmentTypes/Index', [
                'equipmentTypes' => $equipmentTypes,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioEquipmentTypeRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-equipment-type')) {
            $validated = $request->validated();

            $equipmentType              = new PhotoStudioEquipmentType();
            $equipmentType->name        = $validated['name'];
            $equipmentType->description = $validated['description'] ?? null;
            $equipmentType->status      = $validated['status'];
            $equipmentType->creator_id  = Auth::id();
            $equipmentType->created_by  = creatorId();
            $equipmentType->save();

            CreatePhotoStudioEquipmentType::dispatch($request, $equipmentType);

            return redirect()->back()->with('success', __('The equipment type has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioEquipmentTypeRequest $request, PhotoStudioEquipmentType $equipment_type)
    {
        if (Auth::user()->can('edit-photo-studio-equipment-type')) {
            $validated = $request->validated();

            $equipment_type->name        = $validated['name'];
            $equipment_type->description = $validated['description'] ?? $equipment_type->description;
            $equipment_type->status      = $validated['status'];
            $equipment_type->save();

            UpdatePhotoStudioEquipmentType::dispatch($request, $equipment_type);

            return redirect()->back()->with('success', __('The equipment type has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioEquipmentType $equipment_type)
    {
        if (Auth::user()->can('delete-photo-studio-equipment-type')) {
            DestroyPhotoStudioEquipmentType::dispatch($equipment_type);
            $equipment_type->delete();

            return redirect()->back()->with('success', __('The equipment type has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
