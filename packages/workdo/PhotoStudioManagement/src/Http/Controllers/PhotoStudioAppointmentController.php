<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;
use Workdo\PhotoStudioManagement\Http\Requests\StorePhotoStudioAppointmentRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioAppointmentRequest;
use Workdo\PhotoStudioManagement\Http\Requests\AssignPhotoStudioTeamMembersRequest;
use Workdo\PhotoStudioManagement\Http\Requests\UpdatePhotoStudioAppointmentStatusRequest;
use Workdo\PhotoStudioManagement\Events\CreatePhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Events\UpdatePhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioAppointment;

class PhotoStudioAppointmentController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-appointments')) {
            $appointments = PhotoStudioAppointment::with('service:id,name,price')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-photo-studio-appointments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-photo-studio-appointments')) {
                        $teamMemberId = PhotoStudioTeamMember::where('user_id', Auth::id())
                            ->where('created_by', creatorId())
                            ->value('id');
                        $q->where('creator_id', Auth::id())
                            ->when($teamMemberId, fn($q) => $q->orWhereJsonContains('team_member_ids', (string) $teamMemberId));
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), fn($q) => $q->where(function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('email', 'like', '%' . request('search') . '%')
                        ->orWhere('appointment_number', 'like', '%' . request('search') . '%');
                }))
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('payment_status'), fn($q) => $q->where('payment_status', request('payment_status')))
                ->when(request('service_id'), fn($q) => $q->where('service_id', request('service_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $services    = PhotoStudioService::where('created_by', creatorId())->get(['id', 'name', 'price']);
            $teamMembers = PhotoStudioTeamMember::with('user:id,name,avatar')
                ->where('created_by', creatorId())
                ->where('is_active', true)
                ->get(['id', 'user_id']);

            return Inertia::render('PhotoStudioManagement/Appointments/Index', [
                'appointments' => $appointments,
                'services'     => $services,
                'teamMembers'  => $teamMembers,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StorePhotoStudioAppointmentRequest $request)
    {
        if (Auth::user()->can('create-photo-studio-appointments')) {
            $validated = $request->validated();

            $appointment                     = new PhotoStudioAppointment();
            $appointment->name               = $validated['name'];
            $appointment->email              = $validated['email'];
            $appointment->mobile_no          = $validated['mobile_no'];
            $appointment->team_member_ids    = [];
            $appointment->booking_start_date = $validated['booking_start_date'];
            $appointment->booking_end_date   = $validated['booking_end_date'];
            $appointment->service_id         = $validated['service_id'];
            $appointment->price              = $validated['price'];
            $appointment->status             = 'pending';
            $appointment->payment_status     = 'pending';
            $appointment->creator_id         = Auth::id();
            $appointment->created_by         = creatorId();
            $appointment->save();

            CreatePhotoStudioAppointment::dispatch($request, $appointment);

            return redirect()->back()->with('success', __('The appointment has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdatePhotoStudioAppointmentRequest $request, PhotoStudioAppointment $appointment)
    {
        if (Auth::user()->can('edit-photo-studio-appointments')) {
            $validated = $request->validated();

            $appointment->name               = $validated['name'];
            $appointment->email              = $validated['email'];
            $appointment->mobile_no          = $validated['mobile_no'];
            $appointment->team_member_ids    = $appointment->team_member_ids ?? [];
            $appointment->booking_start_date = $validated['booking_start_date'];
            $appointment->booking_end_date   = $validated['booking_end_date'];
            $appointment->service_id         = $validated['service_id'];
            $appointment->price              = $validated['price'];
            $appointment->save();

            UpdatePhotoStudioAppointment::dispatch($request, $appointment);

            return redirect()->back()->with('success', __('The appointment has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function assignTeamMembers(AssignPhotoStudioTeamMembersRequest $request, PhotoStudioAppointment $appointment)
    {
        if (Auth::user()->can('edit-photo-studio-team-members')) {
            $appointment->team_member_ids = $request->validated()['team_member_ids'] ?? [];
            $appointment->save();
            UpdatePhotoStudioAppointment::dispatch($request, $appointment);

            return redirect()->back()->with('success', __('The team members has been assigned successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateStatus(UpdatePhotoStudioAppointmentStatusRequest $request, PhotoStudioAppointment $appointment)
    {
        if (Auth::user()->can('edit-photo-studio-appointments')) {
            $appointment->status = $request->validated()['status'];
            $appointment->save();
            UpdatePhotoStudioAppointment::dispatch($request, $appointment);

            return redirect()->back()->with('success', __('The status has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(PhotoStudioAppointment $appointment)
    {
        if (Auth::user()->can('delete-photo-studio-appointments')) {
            DestroyPhotoStudioAppointment::dispatch($appointment);
            $appointment->delete();

            return redirect()->back()->with('success', __('The appointment has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
