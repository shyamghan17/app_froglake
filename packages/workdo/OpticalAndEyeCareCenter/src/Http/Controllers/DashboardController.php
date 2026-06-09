<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;
use Workdo\OpticalAndEyeCareCenter\Models\EyeCareAppoinment;
use Workdo\OpticalAndEyeCareCenter\Models\EyeTestPrescription;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-optical-dashboard'))
        {
            $user = Auth::user();

            if ($user->hasRole('doctor')) {
                return $this->doctorDashboard();
            }

            return $this->companyDashboard();
        }else{
            return redirect()->route('dashboard')->with('error', __('Permission denied'));
        }
    }

    private function companyDashboard()
    {
        $creatorId = creatorId();

        $stats = [
            'total_patients' => EyePatient::where('created_by', $creatorId)->count(),
            'total_doctors' => OpticalDoctor::where('created_by', $creatorId)->count(),
            'total_orders' => EyewearOrder::where('created_by', $creatorId)->count(),
            'total_appointments' => EyeCareAppoinment::where('created_by', $creatorId)->count(),
            'total_prescriptions' => EyeTestPrescription::where('created_by', $creatorId)->count(),
            'draft_orders' => EyewearOrder::where('created_by', $creatorId)->where('payment_status', 'draft')->count(),
            'paid_orders' => EyewearOrder::where('created_by', $creatorId)->where('payment_status', 'paid')->count(),
            'pending_appointments' => EyeCareAppoinment::where('created_by', $creatorId)->whereIn('status', ['0', '1'])->count(),
            'completed_appointments' => EyeCareAppoinment::where('created_by', $creatorId)->where('status', '2')->count(),
            'cancelled_appointments' => EyeCareAppoinment::where('created_by', $creatorId)->where('status', '3')->count(),
            'total_revenue' => EyewearOrder::where('created_by', $creatorId)->where('payment_status', 'paid')->sum('total_amount'),
            'monthly_revenue' => EyewearOrder::where('created_by', $creatorId)->where('payment_status', 'paid')->whereMonth('created_at', now()->month)->sum('total_amount'),
        ];

        $appointmentStatus = [
            ['name' => 'Scheduled', 'value' => EyeCareAppoinment::where('created_by', $creatorId)->where('status', '0')->count(), 'color' => '#3b82f6'],
            ['name' => 'Confirmed', 'value' => EyeCareAppoinment::where('created_by', $creatorId)->where('status', '1')->count(), 'color' => '#10b981'],
            ['name' => 'Completed', 'value' => $stats['completed_appointments'], 'color' => '#8b5cf6'],
            ['name' => 'Cancelled', 'value' => $stats['cancelled_appointments'], 'color' => '#ef4444'],
        ];

        $orderStatus = [
            ['name' => 'Draft', 'value' => $stats['draft_orders'], 'color' => '#eab308'],
            ['name' => 'Paid', 'value' => $stats['paid_orders'], 'color' => '#10b981'],
        ];

        $recentAppointments = EyeCareAppoinment::where('created_by', $creatorId)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->patient_name ?? 'N/A',
                    'doctor_name' => $appointment->doctor_name ?? 'N/A',
                    'appointment_date' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('Y-m-d') : 'N/A',
                    'appointment_time' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('H:i') : 'N/A',
                    'status' => $appointment->status,
                ];
            });

        $recentOrders = EyewearOrder::where('created_by', $creatorId)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'patient_name' => $order->patient->patient_name ?? 'N/A',
                    'order_number' => $order->order_number ?? '#' . $order->id,
                    'total_amount' => $order->total_amount ?? 0,
                    'status' => $order->payment_status,
                    'created_at' => $order->created_at->format('Y-m-d'),
                ];
            });

        $topDoctors = OpticalDoctor::where('created_by', $creatorId)
            ->with('user')
            ->get()
            ->map(function($doctor) use ($creatorId) {
                $totalAppointments = EyeCareAppoinment::where('created_by', $creatorId)
                    ->where('doctor_name', $doctor->user_id)
                    ->count();
                $completedAppointments = EyeCareAppoinment::where('created_by', $creatorId)
                    ->where('doctor_name', $doctor->user_id)
                    ->where('status', '2')
                    ->count();
                return [
                    'name' => $doctor->user->name ?? 'N/A',
                    'total_appointments' => $totalAppointments,
                    'completed_appointments' => $completedAppointments,
                    'specialization' => 'Optometrist',
                ];
            })
            ->sortByDesc('total_appointments')
            ->take(5)
            ->values();

        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'appointments' => EyeCareAppoinment::where('created_by', $creatorId)->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count(),
                'orders' => EyewearOrder::where('created_by', $creatorId)->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count(),
                'revenue' => EyewearOrder::where('created_by', $creatorId)->where('payment_status', 'paid')->whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->sum('total_amount'),
            ];
        }

        return Inertia::render('OpticalAndEyeCareCenter/Dashboard/CompanyDashboard', [
            'stats' => $stats,
            'appointmentStatus' => $appointmentStatus,
            'orderStatus' => $orderStatus,
            'recentAppointments' => $recentAppointments,
            'recentOrders' => $recentOrders,
            'topDoctors' => $topDoctors,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function doctorDashboard()
    {
        $userId = Auth::id();

        $stats = [
            'my_patients' => EyePatient::where('preferred_doctor', $userId)->count(),
            'my_appointments' => EyeCareAppoinment::where('doctor_name', $userId)->count(),
            'my_prescriptions' => EyeTestPrescription::where('doctor_name', $userId)->count(),
            'pending_appointments' => EyeCareAppoinment::where('doctor_name', $userId)->whereIn('status', ['0', '1'])->count(),
            'today_appointments' => EyeCareAppoinment::where('doctor_name', $userId)
                ->whereDate('appointment_datetime', today())
                ->count(),
        ];

        $todayAppointments = EyeCareAppoinment::where('doctor_name', $userId)
            ->whereDate('appointment_datetime', today())
            ->with('patient')
            ->orderBy('appointment_datetime')
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->patient_name ?? 'N/A',
                    'appointment_date' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('Y-m-d') : 'N/A',
                    'appointment_time' => $appointment->appointment_datetime ? $appointment->appointment_datetime->format('h:i A') : 'N/A',
                    'status' => $appointment->status,
                ];
            });

        $recentPrescriptions = EyeTestPrescription::where('doctor_name', $userId)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($prescription) {
                return [
                    'id' => $prescription->id,
                    'patient_name' => $prescription->patient->patient_name ?? 'N/A',
                    'prescription_date' => $prescription->created_at->format('Y-m-d'),
                    'diagnosis' => $prescription->test_results ? substr($prescription->test_results, 0, 50) . '...' : 'N/A',
                ];
            });

        return Inertia::render('OpticalAndEyeCareCenter/Dashboard/DoctorDashboard', [
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
            'recentPrescriptions' => $recentPrescriptions,
        ]);
    }
}
