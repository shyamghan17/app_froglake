<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\Announcement;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Http\Requests\StoreRotasAnnouncementRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasAnnouncementRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Workdo\Rotas\Models\AnnouncementCategory;
use Workdo\Rotas\Models\Department;
use Workdo\Rotas\Events\CreateAnnouncement;
use Workdo\Rotas\Events\DestroyAnnouncement;
use Workdo\Rotas\Events\UpdateAnnouncement;

class RotasAnnouncementController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-announcements')) {
            $announcements = Announcement::query()
                ->with(['announcementCategory', 'departments', 'approvedBy'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-announcements')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-announcements')) {
                        // Get current user's employee record to find their department
                        $employee = Employee::where('user_id', Auth::id())->first();
                        if ($employee && $employee->department_id) {
                            // Show announcements that are assigned to user's department
                            $q->whereHas('departments', function ($query) use ($employee) {
                                $query->where('department_id', $employee->department_id)->where('status', 'active');
                            });
                        } else {
                            // If user has no department, show no announcements
                            $q->whereRaw('1 = 0');
                        }
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('title'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('title', 'like', '%' . request('title') . '%');
                        $query->orWhere('description', 'like', '%' . request('title') . '%');
                    });
                })
                ->when(request('announcement_category_id'), fn($q) => $q->where('announcement_category_id', request('announcement_category_id')))
                ->when(request('priority') !== null && request('priority') !== '', fn($q) => $q->where('priority', request('priority')))
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Rotas/Announcements/Index', [
                'announcements' => $announcements,
                'announcementcategories' => AnnouncementCategory::where('created_by', creatorId())->select('id', 'announcement_category as name')->get(),
                'departments' => Department::where('created_by', creatorId())->select('id', 'department_name as name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasAnnouncementRequest $request)
    {
        if (Auth::user()->can('create-rotas-announcements')) {
            $validated = $request->validated();



            $announcement = new Announcement();
            $announcement->title = $validated['title'];
            $announcement->description = $validated['description'];
            $announcement->start_date = $validated['start_date'];
            $announcement->end_date = $validated['end_date'];
            $announcement->priority = $validated['priority'];
            $announcement->status = 'draft';
            $announcement->announcement_category_id = $validated['announcement_category_id'];

            $announcement->creator_id = Auth::id();
            $announcement->created_by = creatorId();
            $announcement->save();

            CreateAnnouncement::dispatch($request, $announcement);

            // Sync departments with creator info
            if (isset($validated['departments'])) {
                $departmentData = [];
                foreach ($validated['departments'] as $departmentId) {
                    $departmentData[$departmentId] = [
                        'creator_id' => Auth::id(),
                        'created_by' => creatorId(),
                    ];
                }
                $announcement->departments()->sync($departmentData);
            }

            return back()->with('success', __('The announcement has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasAnnouncementRequest $request, Announcement $announcement)
    {
        if (Auth::user()->can('edit-rotas-announcements')) {
            $validated = $request->validated();



            $announcement->title = $validated['title'];
            $announcement->description = $validated['description'];
            $announcement->start_date = $validated['start_date'];
            $announcement->end_date = $validated['end_date'];
            $announcement->priority = $validated['priority'];
            $announcement->announcement_category_id = $validated['announcement_category_id'];

            $announcement->save();

            UpdateAnnouncement::dispatch($request, $announcement);

            // Sync departments with creator info
            if (isset($validated['departments'])) {
                $departmentData = [];
                foreach ($validated['departments'] as $departmentId) {
                    $departmentData[$departmentId] = [
                        'creator_id' => Auth::id(),
                        'created_by' => creatorId(),
                    ];
                }
                $announcement->departments()->sync($departmentData);
            }

            return back()->with('success', __('The announcement details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function updateStatus(Request $request, Announcement $announcement)
    {
        if (Auth::user()->can('manage-rotas-announcements-status')) {
            $request->validate([
                'status' => 'required|in:draft,active,inactive'
            ]);

            $announcement->status = $request->status;
            $announcement->approved_by = Auth::id();
            $announcement->save();

            return back()->with('success', __('The announcement status has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->can('delete-rotas-announcements')) {
            DestroyAnnouncement::dispatch($announcement);
            $announcement->delete();

            return back()->with('success', __('The announcement has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

}
