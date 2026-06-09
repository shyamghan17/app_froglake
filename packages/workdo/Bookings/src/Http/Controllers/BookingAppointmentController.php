<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingAppointment;
use Workdo\Bookings\Models\BookingCustomer;
use Workdo\Bookings\Models\BookingPackage;
use Workdo\Bookings\Models\BookingPayment;
use Workdo\ProductService\Models\ProductServiceItem;
use App\Models\User;
use Workdo\Bookings\Http\Requests\StoreBookingAppointmentRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingAppointmentRequest;
use Workdo\Bookings\Models\BookingBusinessHours;
use Workdo\Bookings\Models\BookingStaff;
use Illuminate\Support\Carbon as SupportCarbon;
use Workdo\Bookings\Entities\BookingsPackage;
use Carbon\Carbon;
use Workdo\Bookings\Events\CreateBookingAppointment;
use Workdo\Bookings\Events\UpdateBookingAppointment;
use Workdo\Bookings\Events\DestroyBookingAppointment;

class BookingAppointmentController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-appointments')) {
            $appointments = BookingAppointment::with(['item:id,name', 'package:id,name,item_id', 'staff:id,name', 'customer:id,first_name,last_name,email'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-appointments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-appointments')) {
                        // For all other users, show projects they are involved in
                        $q->where(function ($subQ) {
                            // Show own created appointments
                            $subQ->where('creator_id', Auth::id());
                            // OR show assigned projects (for staff)
                            if (Auth::user()->type === 'staff') {
                                $subQ->orWhere('staff_id', Auth::id());
                            }
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), fn($q) => $q->where(function ($query) {
                    $search = request('search');
                    $query->where('appointment_number', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', fn($q) => $q->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%'))
                        ->orWhereHas('staff', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
                }))
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('payment_status'), fn($q) => $q->where('payment_status', request('payment_status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $items = ProductServiceItem::where('type', 'bookings')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-items')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-items')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get(['id', 'name']);

            $packages = BookingPackage::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-packages')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-packages')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->get(['id', 'name', 'item_id', 'price']);

            $staff_ids = BookingStaff::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-staff')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-staff')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->pluck('staff_id')->toArray();
            $users = User::whereIn('id', $staff_ids)->get(['id', 'name', 'email']);

            $customers = BookingCustomer::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-customers')) {
                    $q->where('created_by', creatorId())->orWhereNull('created_by');
                } elseif (Auth::user()->can('manage-own-booking-customers')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->get(['id', 'first_name', 'last_name', 'email']);

            return Inertia::render('Bookings/Appointments/Index', [
                'appointments' => $appointments,
                'items' => $items,
                'packages' => $packages,
                'users' => $users,
                'customers' => $customers,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingAppointmentRequest $request)
    {
        if (Auth::user()->can('create-booking-appointments')) {
            $validated = $request->validated();

            $package = BookingPackage::find($validated['package_id']);

            $currentYear = date('Y');
            $userId = creatorId();
            $lastAppointment = BookingAppointment::where('created_by', $userId)
                ->where('appointment_number', 'like', 'APT-' . $currentYear . '-' . $userId . '-%')
                ->orderBy('appointment_number', 'desc')
                ->first();

            if ($lastAppointment) {
                $lastNumber = (int) substr($lastAppointment->appointment_number, -4);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $appointmentNumber = 'APT-' . $currentYear . '-' . $userId . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $appointment                     = new BookingAppointment();
            $appointment->appointment_number = $appointmentNumber;
            $appointment->date               = $validated['date'];
            $appointment->item_id            = $validated['item_id'];
            $appointment->package_id         = $validated['package_id'];
            $appointment->customer_id        = $validated['customer_id'];
            $appointment->start_time         = $validated['start_time'];
            $appointment->end_time           = $validated['end_time'];
            $appointment->status             = 'pending';
            $appointment->payment            = 'Manually';
            $appointment->payment_status     = $validated['payment_status'] ?? 'pending';
            $appointment->amount             = $package ? $package->price : 0;
            $appointment->creator_id         = Auth::id();
            $appointment->created_by         = creatorId();
            $appointment->save();

            CreateBookingAppointment::dispatch($request, $appointment);
            return redirect()->route('bookings.appointments.index')
                ->with('success', __('The appointment has been created successfully.'));
        } else {
            return redirect()->route('bookings.appointments.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingAppointmentRequest $request, BookingAppointment $appointment)
    {
        if (Auth::user()->can('edit-booking-appointments') && $appointment->created_by == creatorId()) {
            $validated = $request->validated();

            $package = BookingPackage::find($validated['package_id']);

            $appointment->date        = $validated['date'];
            $appointment->item_id     = $validated['item_id'];
            $appointment->package_id  = $validated['package_id'];
            $appointment->customer_id = $validated['customer_id'];
            $appointment->start_time  = $validated['start_time'];
            $appointment->end_time    = $validated['end_time'];
            $appointment->amount         = $package ? $package->amount : 0;
            $appointment->payment_status = $validated['payment_status'] ?? $appointment->payment_status;
            $appointment->save();

            UpdateBookingAppointment::dispatch($request, $appointment);
            return redirect()->back()->with('success', __('The appointment details are updated successfully.'));
        } else {
            return redirect()->route('bookings.appointments.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingAppointment $appointment)
    {
        if (Auth::user()->can('delete-booking-appointments') && $appointment->created_by == creatorId()) {
            DestroyBookingAppointment::dispatch($appointment);
            $appointment->delete();
            return back()->with('success', __('The appointment has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function calendar()
    {
        if (Auth::user()->can('manage-booking-appointments')) {
            $appointments = BookingAppointment::with(['item:id,name', 'package:id,name,item_id', 'staff:id,name', 'customer:id,first_name,last_name,email'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-appointments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-appointments')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get();

            $items = ProductServiceItem::where('type', 'bookings')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-items')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-items')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get(['id', 'name']);

            $packages = BookingPackage::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-packages')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-packages')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->get(['id', 'name', 'item_id', 'price']);

            $staff_ids = BookingStaff::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-staff')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-staff')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->pluck('staff_id')->toArray();
            $users = User::whereIn('id', $staff_ids)->get(['id', 'name', 'email']);

            $customers = BookingCustomer::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-customers')) {
                    $q->where('created_by', creatorId())->orWhereNull('created_by');
                } elseif (Auth::user()->can('manage-own-booking-customers')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->get(['id', 'first_name', 'last_name', 'email']);

            return Inertia::render('Bookings/Appointments/Calendar', [
                'appointments' => $appointments,
                'items' => $items,
                'packages' => $packages,
                'users' => $users,
                'customers' => $customers,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function kanban()
    {
        if (Auth::user()->can('manage-booking-appointments')) {
            $appointments = BookingAppointment::with(['item:id,name', 'package:id,name,item_id', 'staff:id,name,avatar', 'customer:id,first_name,last_name,email'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-appointments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-appointments')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get();

            $items = ProductServiceItem::where('type', 'bookings')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-items')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-items')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get(['id', 'name']);

            $packages = BookingPackage::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-packages')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-packages')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->get(['id', 'name', 'item_id', 'price']);

            $staff_ids = BookingStaff::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-staff')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-staff')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->pluck('staff_id')->toArray();

            $users = User::whereIn('id', $staff_ids)->get(['id', 'name', 'email']);

            $customers = BookingCustomer::where(function ($q) {
                if (Auth::user()->can('manage-any-booking-customers')) {
                    $q->where('created_by', creatorId())->orWhereNull('created_by');
                } elseif (Auth::user()->can('manage-own-booking-customers')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->get(['id', 'first_name', 'last_name', 'email']);

            return Inertia::render('Bookings/Appointments/Kanban', [
                'appointments' => $appointments,
                'items' => $items,
                'packages' => $packages,
                'users' => $users,
                'customers' => $customers,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function complete(BookingAppointment $appointment)
    {
        if (Auth::user()->can('edit-booking-appointments') && $appointment->created_by == creatorId()) {
            if ($appointment->payment_status === 'paid') {
                $appointment->update(['status' => 'completed']);
                return back()->with('success', __('The appointment has been marked as completed.'));
            }
            return back()->with('error', __('Only paid appointments can be marked as completed.'));
        }
        return back()->with('error', __('Permission denied'));
    }

    public function updateStatus(Request $request, BookingAppointment $appointment)
    {
        if (Auth::user()->can('edit-booking-appointments') && $appointment->created_by == creatorId()) {
            $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled'
            ]);

            $appointment->update(['status' => $request->status]);

            return back();
        }

        return back()->with('error', __('Permission denied'));
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $formattedSelectedDate = $request->date;
        $itemId = $request->item_id;
        $packageId = $request->package_id;

        $package = BookingPackage::where('item_id', $itemId)->where('id', $packageId)->first();
        if (!$package) {
            return response()->json(['slots' => [], 'message' => __('Invalid service or package selected.')]);
        }
        $dayName = strtolower(SupportCarbon::parse($formattedSelectedDate)->format('l'));
        $businessHour = BookingBusinessHours::where('created_by', $package->created_by)->where('day_of_week', $dayName)->first();

        if ($businessHour && ($businessHour->is_closed == '1' || $businessHour->is_closed === 1 || $businessHour->is_closed === true)) {
            return response()->json(['slots' => [], 'message' => __('The selected day is marked as closed. Please choose another date.')]);
        }

        $appointment = null;
        if ($request->appointment_id) {
            $appointment = BookingAppointment::find($request->appointment_id);
        }
        $timeSlots = BookingAppointment::timeSlot($package->id, $request->date, $appointment);

        $availableSlots = [];
        foreach ($timeSlots as $slot) {
            $isSelected = false;
            if ($appointment && $appointment->start_time == $slot['start'] && $appointment->end_time == $slot['end']) {
                $isSelected = true;
            }

            $availableSlots[] = [
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
                'selected' => $isSelected
            ];
        }

        return response()->json(['slots' => $availableSlots, 'message' => __('success')]);
    }

    public function storePayment(Request $request)
    {
        if (Auth::user()->can('edit-booking-appointments')) {
            $validated = $request->validate([
                'appointment_id' => 'required|exists:booking_appointments,id',
                'reference_number' => 'nullable|string|max:255',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'bank_account_id' => 'nullable|exists:bank_accounts,id',
                'notes' => 'nullable|string'
            ]);

            $payment = new BookingPayment();
            $payment->appointment_id = $validated['appointment_id'];
            $payment->reference_number = $validated['reference_number'];
            $payment->payment_date = $validated['payment_date'];
            $payment->amount = $validated['amount'];
            $payment->bank_account_id = $validated['bank_account_id'] ?? null;
            $payment->notes = $validated['notes'];
            $payment->payment_status = 'pending';
            $payment->creator_id = Auth::id();
            $payment->created_by = creatorId();
            $payment->save();

            $appointment = BookingAppointment::find($validated['appointment_id']);
            if ($appointment) {
                $appointment->payment_status = 'paid';
                $appointment->save();
            }

            return redirect()->route('bookings.appointments.index')->with('success', __('The payment has been created successfully.'));
        } else {
            return redirect()->route('bookings.appointments.index')->with('error', __('Permission denied'));
        }
    }

    public function convertToMinutes($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return ($hours * 60) + $minutes;
    }
}
