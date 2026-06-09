<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioGalleryType;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioGalleryTypeRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioGalleryTypeRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioGalleryType;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioGalleryType;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioGalleryType;

class PhotoStudioGalleryTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-gallery-type')) {
            $galleryTypes = PhotoStudioGalleryType::where(function ($q) {
                if (Auth::user()->can('manage-any-photo-studio-gallery-type')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-photo-studio-gallery-type')) {
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

            return Inertia::render('PhotoStudioManagement/SystemSetup/GalleryTypes/Index', [
                'galleryTypes' => $galleryTypes,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioGalleryTypeRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-gallery-type')) {
            $validated = $request->validated();

            $galleryType              = new PhotoStudioGalleryType();
            $galleryType->name        = $validated['name'];
            $galleryType->description = $validated['description'] ?? null;
            $galleryType->status      = $validated['status'];
            $galleryType->creator_id  = Auth::id();
            $galleryType->created_by  = creatorId();
            $galleryType->save();

            CreatePhotoStudioGalleryType::dispatch($request, $galleryType);

            return redirect()->back()->with('success', __('The gallery type has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioGalleryTypeRequest $request, PhotoStudioGalleryType $gallery_type)
    {
        if (Auth::user()->can('edit-photo-studio-gallery-type')) {
            $validated = $request->validated();

            $gallery_type->name        = $validated['name'];
            $gallery_type->description = $validated['description'] ?? $gallery_type->description;
            $gallery_type->status      = $validated['status'];
            $gallery_type->save();

            UpdatePhotoStudioGalleryType::dispatch($request, $gallery_type);

            return redirect()->back()->with('success', __('The gallery type has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioGalleryType $gallery_type)
    {
        if (Auth::user()->can('delete-photo-studio-gallery-type')) {
            DestroyPhotoStudioGalleryType::dispatch($gallery_type);
            $gallery_type->delete();

            return redirect()->back()->with('success', __('The gallery type has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
