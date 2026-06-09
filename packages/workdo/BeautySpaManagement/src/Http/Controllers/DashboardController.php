<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyService;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('manage-beauty-spa-dashboard')) {
            $user     = Auth::user();
            $userType = $user->type;

            switch ($userType) {
                case 'company': 
                    return $this->companyDashboard();
                case 'staff': 
                     default: 
                    return $this->staffDashboard();
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    private function companyDashboard()
    {
        $creatorId = Auth::user()->id;

        return Inertia::render('BeautySpaManagement/Dashboard/CompanyDashboard', [
            'stats'                => $this->getStats($creatorId),
            'recentAppointments'   => $this->getRecentAppointments($creatorId),
            'chartData'            => $this->getChartData($creatorId),
            'calendarAppointments' => $this->getCalendarAppointments($creatorId),
            'bookingUrl'           => $this->getBookingUrl($creatorId),
            'message'              => __('Beauty Spa Dashboard - Manage your appointments efficiently.')
        ]);
    }

    private function staffDashboard()
    {
        $user      = Auth::user();
        $creatorId = $user->created_by ?? $user->id;

        return Inertia::render('BeautySpaManagement/Dashboard/StaffDashboard', [
            'stats'                => $this->getStaffStats($user->id, $creatorId),
            'recentAppointments'   => $this->getStaffRecentAppointments($user->id, $creatorId),
            'chartData'            => $this->getStaffChartData($user->id, $creatorId),
            'calendarAppointments' => $this->getStaffCalendarAppointments($user->id, $creatorId),
            'bookingUrl'           => $this->getBookingUrl($creatorId),
            'message'              => __('Staff Dashboard - Manage your appointments efficiently.')
        ]);
    }

    private function getStats(int $userId): array
    {
        $stats = BeautyBooking::where('created_by', $userId)
            ->selectRaw('
                COUNT(*) as total_appointments,
                COUNT(CASE WHEN stage_id = 2 THEN 1 END) as complete_appointments,
                COUNT(CASE WHEN stage_id = 0 THEN 1 END) as pending_appointments
            ')
            ->first();

        $totalCustomers = BeautyBooking::where('created_by', $userId)->distinct('email')->count();

        return [
            'totalAppointments'    => $stats->total_appointments ?? 0,
            'completeAppointments' => $stats->complete_appointments ?? 0,
            'totalCustomers'       => $totalCustomers,
            'pendingAppointments'  => $stats->pending_appointments ?? 0
        ];
    }

    private function getRecentAppointments(int $userId): array
    {
        return BeautyBooking::where('created_by', $userId)
            ->with('beautyService:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'                 => $appointment->id,
                    'appointment_number' => 'BSM-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT),
                    'customer_name'      => $appointment->name,
                    'service_name'       => $appointment->beautyService?->name ?? 'N/A',
                    'date'               => $appointment->date ? date('Y-m-d', strtotime($appointment->date)) : '-',
                    'time'               => $appointment->start_time && $appointment->end_time ? date('H:i', strtotime($appointment->start_time)) . ' - ' . date('H:i', strtotime($appointment->end_time)) : ($appointment->start_time ? date('H:i', strtotime($appointment->start_time)) : ($appointment->end_time ? date('H:i', strtotime($appointment->end_time)) : '-')),
                    'status'             => $appointment->stage_id == 0 ? 'Pending' : ($appointment->stage_id == 1 ? 'Confirmed' : ($appointment->stage_id == 2 ? 'Completed' : 'Cancelled')),
                    'stage_id'           => $appointment->stage_id
                ];
            })->toArray();
    }

    private function getChartData(int $userId): array
    {
        $dates = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
        
        $appointments = BeautyBooking::where('created_by', $userId)
            ->whereIn(DB::raw('DATE(date)'), $dates)
            ->selectRaw('DATE(date) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('count', 'date');

        return $dates->map(function ($date) use ($appointments) {
            return [
                'date'         => Carbon::parse($date)->format('M d'),
                'appointments' => $appointments->get($date, 0)
            ];
        })->toArray();
    }

    private function getCalendarAppointments(int $userId): array
    {
        return BeautyBooking::where('created_by', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->select(['id', 'name', 'date', 'start_time', 'end_time', 'stage_id', 'service', 'email', 'phone_number'])
            ->with('beautyService:id,name')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'                 => $appointment->id,
                    'appointment_number' => 'BSM-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT),
                    'title'              => $appointment->name . ' - ' . ($appointment->beautyService?->name ?? 'Service'),
                    'date'               => $appointment->date,
                    'start_time'         => $appointment->start_time,
                    'end_time'           => $appointment->end_time,
                    'status'             => $appointment->stage_id == 0 ? 'Pending' : ($appointment->stage_id == 1 ? 'Confirmed' : ($appointment->stage_id == 2 ? 'Completed' : 'Cancelled'))
                ];
            })->toArray();
    }

    private function getStaffStats(int $staffId, int $creatorId): array
    {
        $staffBookings = BeautyBooking::where('created_by', $creatorId)
            ->whereHas('beautyService', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            });

        $stats = $staffBookings
            ->selectRaw('
                COUNT(*) as total_appointments,
                COUNT(CASE WHEN stage_id = 2 THEN 1 END) as complete_appointments,
                COUNT(CASE WHEN stage_id = 0 THEN 1 END) as pending_appointments
            ')
            ->first();

        $todayAppointments = BeautyBooking::where('created_by', $creatorId)
            ->whereHas('beautyService', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->whereDate('date', today())
            ->count();

        $totalCustomers = $staffBookings->distinct('email')->count();

        return [
            'totalAppointments'   => $stats->total_appointments ?? 0,
            'todayAppointments'   => $todayAppointments,
            'totalCustomers'      => $totalCustomers,
            'pendingAppointments' => $stats->pending_appointments ?? 0
        ];
    }

    private function getStaffRecentAppointments(int $staffId, int $creatorId): array
    {
        return BeautyBooking::where('created_by', $creatorId)
            ->whereHas('beautyService', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->with('beautyService:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'                 => $appointment->id,
                    'appointment_number' => 'BSM-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT),
                    'customer_name'      => $appointment->name,
                    'service_name'       => $appointment->beautyService?->name ?? 'N/A',
                    'date'               => $appointment->date ? date('Y-m-d', strtotime($appointment->date)) : '-',
                    'time'               => $appointment->start_time && $appointment->end_time ? date('H:i', strtotime($appointment->start_time)) . ' - ' . date('H:i', strtotime($appointment->end_time)) : ($appointment->start_time ? date('H:i', strtotime($appointment->start_time)) : ($appointment->end_time ? date('H:i', strtotime($appointment->end_time)) : '-')),
                    'status'             => $appointment->stage_id == 0 ? 'Pending' : ($appointment->stage_id == 1 ? 'Confirmed' : ($appointment->stage_id == 2 ? 'Completed' : 'Cancelled')),
                    'stage_id'           => $appointment->stage_id
                ];
            })->toArray();
    }

    private function getStaffChartData(int $staffId, int $creatorId): array
    {
        $dates = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
        
        $appointments = BeautyBooking::where('created_by', $creatorId)
            ->whereHas('beautyService', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->whereIn(DB::raw('DATE(date)'), $dates)
            ->selectRaw('DATE(date) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('count', 'date');

        return $dates->map(function ($date) use ($appointments) {
            return [
                'date'         => Carbon::parse($date)->format('M d'),
                'appointments' => $appointments->get($date, 0)
            ];
        })->toArray();
    }

    private function getStaffCalendarAppointments(int $staffId, int $creatorId): array
    {
        return BeautyBooking::where('created_by', $creatorId)
            ->whereHas('beautyService', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->select(['id', 'name', 'date', 'start_time', 'end_time', 'stage_id', 'service', 'email', 'phone_number'])
            ->with('beautyService:id,name')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'                 => $appointment->id,
                    'appointment_number' => 'BSM-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT),
                    'title'              => $appointment->name . ' - ' . ($appointment->beautyService?->name ?? 'Service'),
                    'date'               => $appointment->date,
                    'start_time'         => $appointment->start_time,
                    'end_time'           => $appointment->end_time,
                    'status'             => $appointment->stage_id == 0 ? 'Pending' : ($appointment->stage_id == 1 ? 'Confirmed' : ($appointment->stage_id == 2 ? 'Completed' : 'Cancelled'))
                ];
            })->toArray();
    }

    private function getBookingUrl(int $userId): ?string
    {
        $user = User::select('slug')->find($userId);
        return $user?->slug ? route('beauty-spa.home', ['userSlug' => $user->slug]): null;
    }
}