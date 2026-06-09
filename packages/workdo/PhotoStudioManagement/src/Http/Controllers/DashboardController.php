<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointmentPayment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;
use Workdo\PhotoStudioManagement\Models\PhotoStudioTeamMember;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSetup;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('staff') && $user->can('manage-photo-studio-management-dashboard')) {
            return $this->teamMemberDashboard();
        }

        if ($user->can('manage-photo-studio-management-dashboard')) {
            return $this->companyDashboard();
        }

        return back()->with('error', __('Permission denied.'));
    }

    private function companyDashboard()
    {
        $creatorId = creatorId();

        $appointments = PhotoStudioAppointment::where('created_by', $creatorId);
        $payments     = PhotoStudioAppointmentPayment::where('created_by', $creatorId);

        $totalAppointments   = (clone $appointments)->count();
        $totalTeamMembers    = PhotoStudioTeamMember::where('created_by', $creatorId)->count();
        $totalServices       = PhotoStudioService::where('created_by', $creatorId)->count();
        $totalRevenue        = (clone $payments)->where('payment_status', 'cleared')->sum('amount');
        $pendingAppointments = (clone $appointments)->where('status', 'pending')->count();

        $recentAppointments = (clone $appointments)->with('service')->latest()->take(8)->get();

        $recentTeamMembers = PhotoStudioTeamMember::where('created_by', $creatorId)
            ->with('user:id,name,email')
            ->latest()
            ->take(8)
            ->get();

        $appointmentStatusChart = (clone $appointments)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'name'  => ucfirst($item->status),
                'value' => $item->count,
            ])->values()->toArray();

        $paymentStatusChart = (clone $payments)
            ->selectRaw('payment_status, count(*) as count')
            ->groupBy('payment_status')
            ->get()
            ->map(fn($item) => [
                'name'  => ucfirst($item->payment_status),
                'value' => $item->count,
            ])->values()->toArray();

        $userSlug = User::find($creatorId)?->slug ?? 'demo';

        return Inertia::render('PhotoStudioManagement/Dashboard/CompanyDashboard', [
            'stats' => [
                'total_appointments'   => $totalAppointments,
                'total_team_members'   => $totalTeamMembers,
                'total_services'       => $totalServices,
                'total_revenue'        => $totalRevenue,
                'pending_appointments' => $pendingAppointments,
            ],
            'recentAppointments'     => $recentAppointments,
            'recentTeamMembers'      => $recentTeamMembers,
            'appointmentStatusChart' => $appointmentStatusChart,
            'paymentStatusChart'     => $paymentStatusChart,
            'welcomeCard'            => $this->getWelcomeCardSettings($creatorId),
            'userSlug'               => $userSlug,
            'message'                => __('Photo Studio Management Dashboard'),
        ]);
    }

    private function teamMemberDashboard()
    {
        $user       = Auth::user();
        $teamMember = PhotoStudioTeamMember::where('user_id', $user->id)->first();
        $creatorId  = $teamMember ? $teamMember->created_by : creatorId();

        $myAppointments = PhotoStudioAppointment::where('created_by', $creatorId)
            ->whereJsonContains('team_member_ids', (string) ($teamMember?->id))
            ->with('service')
            ->latest()
            ->get();

        $totalAssigned  = $myAppointments->count();
        $pendingCount   = $myAppointments->where('status', 'pending')->count();
        $scheduledCount = $myAppointments->where('status', 'scheduled')->count();
        $completedCount = $myAppointments->where('status', 'completed')->count();
        $cancelledCount = $myAppointments->where('status', 'cancelled')->count();

        $appointmentIds = $myAppointments->pluck('id')->toArray();

        $appointmentStatusChart = [
            ['name' => 'Pending',   'value' => $pendingCount],
            ['name' => 'Scheduled', 'value' => $scheduledCount],
            ['name' => 'Completed', 'value' => $completedCount],
            ['name' => 'Cancelled', 'value' => $cancelledCount],
        ];

        $paymentStatusChart = PhotoStudioAppointmentPayment::whereIn('appointment_id', $appointmentIds)
            ->selectRaw('payment_status, count(*) as count')
            ->groupBy('payment_status')
            ->get()
            ->map(fn($item) => [
                'name'  => ucfirst($item->payment_status),
                'value' => $item->count,
            ])->values()->toArray();

        $userSlug = User::find($creatorId)?->slug ?? 'demo';

        return Inertia::render('PhotoStudioManagement/Dashboard/TeamMemberDashboard', [
            'stats' => [
                'total_assigned' => $totalAssigned,
                'pending'        => $pendingCount,
                'scheduled'      => $scheduledCount,
                'completed'      => $completedCount,
                'cancelled'      => $cancelledCount,
                'designation'    => $teamMember?->designation ?? '',
                'rate_per_hour'  => $teamMember?->rate_per_hour ?? 0,
            ],
            'recentAppointments'     => $myAppointments->take(8)->values(),
            'appointmentStatusChart' => $appointmentStatusChart,
            'paymentStatusChart'     => $paymentStatusChart,
            'welcomeCard'            => $this->getWelcomeCardSettings($creatorId),
            'userSlug'               => $userSlug,
            'message'                => __('Team Member Dashboard'),
        ]);
    }

    private function getWelcomeCardSettings(int $creatorId): array
    {
        $settings = PhotoStudioSetup::where('created_by', $creatorId)
            ->whereIn('key', ['copy_link_card_title', 'copy_link_card_description', 'copy_link_button_text', 'copy_link_button_icon'])
            ->pluck('value', 'key')
            ->toArray();

        return [
            'title'       => $settings['copy_link_card_title'] ?? null,
            'description' => $settings['copy_link_card_description'] ?? null,
            'buttonText'  => $settings['copy_link_button_text'] ?? null,
            'buttonIcon'  => $settings['copy_link_button_icon'] ?? null,
        ];
    }
}
