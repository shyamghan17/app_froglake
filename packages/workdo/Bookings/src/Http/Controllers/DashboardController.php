<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingAppointment;
use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Models\BookingStaff;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('manage-bookings-dashboard')) {            
            try {
                $userId = (int) creatorId();
                $currentUser = Auth::user();
                $dashboardType = $this->getDashboardType($currentUser);

                return Inertia::render('Bookings/Index', [
                    'stats' => $this->getStats($userId, $currentUser, $dashboardType),
                    'recentAppointments' => $this->getRecentAppointments($userId, $currentUser, $dashboardType),
                    'chartData' => $this->getChartData($userId, $currentUser, $dashboardType),
                    'calendarAppointments' => $this->getCalendarAppointments($userId, $currentUser, $dashboardType),
                    'bookingUrl' => $this->getBookingUrl($userId),
                    'dashboardType' => $dashboardType,
                    'currentUser' => [
                        'id' => $currentUser->id,
                        'name' => $currentUser->name,
                        'type' => $currentUser->type
                    ],
                    'message' => $dashboardType === 'staff' 
                        ? __('Staff Dashboard - Manage your assigned appointments.') 
                        : __('Bookings Dashboard - Manage your appointments efficiently.')
                ]);
            } catch (\Exception $e) {
                return back()->with('error', __('Unable to load dashboard data'));
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    private function getStats(int $userId, $currentUser, string $dashboardType): array
    {
        $query = BookingAppointment::where('created_by', $userId);
        
        if ($dashboardType === 'staff') {
            $query->where('staff_id', $currentUser->id);
        }
        
        $stats = $query->selectRaw('
                COUNT(*) as total_appointments,
                COUNT(CASE WHEN DATE(date) = CURDATE() THEN 1 END) as today_appointments,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_appointments
            ')
            ->first();

        $customerQuery = BookingCustomer::where('created_by', $userId);
        if ($dashboardType === 'staff') {
            $appointmentCustomerIds = BookingAppointment::where('created_by', $userId)
                ->where('staff_id', $currentUser->id)
                ->pluck('customer_id')
                ->unique();
            $customerQuery->whereIn('id', $appointmentCustomerIds);
        }
        $totalCustomers = $customerQuery->count();

        return [
            'totalAppointments' => $stats->total_appointments ?? 0,
            'todayAppointments' => $stats->today_appointments ?? 0,
            'totalCustomers' => $totalCustomers,
            'pendingAppointments' => $stats->pending_appointments ?? 0
        ];
    }

    private function getRecentAppointments(int $userId, $currentUser, string $dashboardType): array
    {
        $query = BookingAppointment::where('created_by', $userId);
        
        if ($dashboardType === 'staff') {
            $query->where('staff_id', $currentUser->id);
        }
        
        return $query->with(['customer:id,first_name,last_name', 'item:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'customer_name' => $appointment->customer 
                        ? $appointment->customer->first_name . ' ' . $appointment->customer->last_name 
                        : 'N/A',
                    'service_name' => $appointment->item?->name ?? 'N/A',
                    'date' => $appointment->date,
                    'time' => $appointment->start_time,
                    'status' => $appointment->status
                ];
            })->toArray();
    }

    private function getChartData(int $userId, $currentUser, string $dashboardType): array
    {
        $dates = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
        
        $query = BookingAppointment::where('created_by', $userId);
        
        if ($dashboardType === 'staff') {
            $query->where('staff_id', $currentUser->id);
        }
        
        $appointments = $query->whereIn(DB::raw('DATE(date)'), $dates)
            ->selectRaw('DATE(date) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('count', 'date');

        return $dates->map(function ($date) use ($appointments) {
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'appointments' => $appointments->get($date, 0)
            ];
        })->toArray();
    }

    private function getCalendarAppointments(int $userId, $currentUser, string $dashboardType): array
    {
        $query = BookingAppointment::where('created_by', $userId);
        
        if ($dashboardType === 'staff') {
            $query->where('staff_id', $currentUser->id);
        }
        
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->select([
                'id', 'appointment_number', 'date', 'start_time', 'end_time', 
                'status', 'payment_status', 'staff_id', 'customer_id', 
                'item_id', 'package_id'
            ])
            ->with([
                'customer:id,first_name,last_name,email',
                'staff:id,name,email', 
                'item:id,name',
                'package:id,name'
            ])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'title' => ($appointment->customer?->first_name ?? 'Customer') . ' - ' . ($appointment->item?->name ?? 'Service'),
                    'date' => $appointment->date,
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                    'status' => $appointment->status,
                    'payment_status' => $appointment->payment_status,
                    'staff_id' => $appointment->staff_id,
                    'customer_id' => $appointment->customer_id,
                    'item_id' => $appointment->item_id,
                    'package_id' => $appointment->package_id,
                    'customer' => $appointment->customer,
                    'staff' => $appointment->staff,
                    'item' => $appointment->item,
                    'package' => $appointment->package
                ];
            })->toArray();
    }

    private function getBookingUrl(int $userId): ?string
    {
        $user = User::select('slug')->find($userId);
        return $user?->slug ? route('booking.home', ['userSlug' => $user->slug]) : null;
    }

    private function getDashboardType($currentUser): string
    {
        // Check if user is a staff member assigned to booking services
        if ($currentUser->type === 'staff' || $currentUser->hasRole('staff')) {
            $bookingStaff = BookingStaff::where('staff_id', $currentUser->id)
                ->first();
            
            if ($bookingStaff) {
                return 'staff';
            }
        }
        
        return 'main';
    }
}