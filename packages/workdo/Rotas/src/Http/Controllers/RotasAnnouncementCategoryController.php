<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\AnnouncementCategory;
use Workdo\Rotas\Http\Requests\StoreRotasAnnouncementCategoryRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasAnnouncementCategoryRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Events\CreateAnnouncementCategory;
use Workdo\Rotas\Events\DestroyAnnouncementCategory;
use Workdo\Rotas\Events\UpdateAnnouncementCategory;


class RotasAnnouncementCategoryController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-rotas-announcement-categories')){
            $announcementcategories = AnnouncementCategory::select('id', 'announcement_category', 'created_at')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-rotas-announcement-categories')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-rotas-announcement-categories')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Rotas/SystemSetup/AnnouncementCategories/Index', [
                'announcementcategories' => $announcementcategories,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasAnnouncementCategoryRequest $request)
    {
        if(Auth::user()->can('create-rotas-announcement-categories')){
            $validated = $request->validated();
            $announcementCategory = new AnnouncementCategory();
            $announcementCategory->announcement_category = $validated['announcement_category'];
            $announcementCategory->creator_id = Auth::id();
            $announcementCategory->created_by = creatorId();
            $announcementCategory->save();

            CreateAnnouncementCategory::dispatch($request, $announcementCategory);

            return back()->with('success', __('The announcement category has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasAnnouncementCategoryRequest $request, AnnouncementCategory $announcementCategory)
    {
        if(Auth::user()->can('edit-rotas-announcement-categories')){
            $validated = $request->validated();
            $announcementCategory->announcement_category = $validated['announcement_category'];
            $announcementCategory->save();

            UpdateAnnouncementCategory::dispatch($request, $announcementCategory);

            return back()->with('success', __('The announcement category details are updated successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy($announcementCategory)
    {
        if (Auth::user()->can('delete-rotas-announcement-categories')) {
            $announcementCategory = AnnouncementCategory::find($announcementCategory);
            DestroyAnnouncementCategory::dispatch($announcementCategory);
            $announcementCategory->delete();

            return back()->with('success', __('The announcement category has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
