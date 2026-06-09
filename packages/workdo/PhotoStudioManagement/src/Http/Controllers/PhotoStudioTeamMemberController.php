<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use App\Models\User;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioTeamMemberRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioTeamMemberRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioTeamMember;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioTeamMember;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioTeamMember;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PhotoStudioTeamMemberController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-team-members')) {
            $teamMembers = PhotoStudioTeamMember::with('user')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-photo-studio-team-members')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-photo-studio-team-members')) {
                        $q->where(function ($subQ) {
                            $subQ->where('creator_id', Auth::id())->orWhere('user_id', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function ($q) {
                    $term = request('search');
                    $q->where(function ($query) use ($term) {
                        $query->where('designation', 'like', '%' . $term . '%')
                            ->orWhere('skills', 'like', '%' . $term . '%')
                            ->orWhereHas('user', function ($userQuery) use ($term) {
                                $userQuery->where('name', 'like', '%' . $term . '%')
                                    ->orWhere('email', 'like', '%' . $term . '%');
                            });
                    });
                })
                ->when(request('is_active') !== null && request('is_active') !== '', fn($q) => $q->where('is_active', request('is_active')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $assignedUserIds = PhotoStudioTeamMember::pluck('user_id')->filter();

            return Inertia::render('PhotoStudioManagement/TeamMembers/Index', [
                'teamMembers' => $teamMembers,
                'users'       => User::where('created_by', creatorId())
                    ->emp()
                    ->whereNotIn('id', $assignedUserIds)
                    ->select('id', 'name', 'email')
                    ->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StorePhotoStudioTeamMemberRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-team-members')) {
            $validated = $request->validated();

            $teamMember                  = new PhotoStudioTeamMember();
            $teamMember->user_id         = $validated['user_id'];
            $teamMember->designation     = $validated['designation'];
            $teamMember->experience_year = $validated['experience_year'];
            $teamMember->skills          = $validated['skills'] ?? null;
            $teamMember->rate_per_hour   = $validated['rate_per_hour'] ?? null;
            $teamMember->is_active       = $validated['is_active'] ?? true;
            $teamMember->bio             = $validated['bio'] ?? null;
            $teamMember->creator_id      = Auth::id();
            $teamMember->created_by      = creatorId();
            $teamMember->save();

            CreatePhotoStudioTeamMember::dispatch($request, $teamMember);

            return redirect()->route('photo-studio-management.team-members.index')->with('success', __('The team member has been created successfully.'));
        } else {
            return redirect()->route('photo-studio-management.team-members.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdatePhotoStudioTeamMemberRequest $request, PhotoStudioTeamMember $teamMember)
    {
        if (Auth::user()->can('edit-photo-studio-team-members')) {
            $validated = $request->validated();

            $teamMember->user_id         = $validated['user_id'];
            $teamMember->designation     = $validated['designation'];
            $teamMember->experience_year = $validated['experience_year'];
            $teamMember->skills          = $validated['skills'] ?? null;
            $teamMember->rate_per_hour   = $validated['rate_per_hour'] ?? null;
            $teamMember->is_active       = $validated['is_active'] ?? true;
            $teamMember->bio             = $validated['bio'] ?? null;
            $teamMember->save();

            UpdatePhotoStudioTeamMember::dispatch($request, $teamMember);

            return redirect()->back()->with('success', __('The team member has been updated successfully.'));
        } else {
            return redirect()->route('photo-studio-management.team-members.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(PhotoStudioTeamMember $teamMember)
    {
        if (Auth::user()->can('delete-photo-studio-team-members')) {
            DestroyPhotoStudioTeamMember::dispatch($teamMember);
            $teamMember->delete();

            return redirect()->back()->with('success', __('The team member has been deleted.'));
        } else {
            return redirect()->route('photo-studio-management.team-members.index')->with('error', __('Permission denied'));
        }
    }
}
