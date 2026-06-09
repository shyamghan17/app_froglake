<?php

namespace Workdo\Rotas\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Workdo\Hrm\Models\Holiday;
use Workdo\Rotas\Events\CreateRota;
use Workdo\Rotas\Events\DestroyRota;
use Workdo\Rotas\Events\UpdateRota;
use Workdo\Rotas\Models\Branch;
use Workdo\Rotas\Models\Department;
use Workdo\Rotas\Models\Designation;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Models\LeaveApplication;
use Workdo\Rotas\Models\Rota;
use Workdo\Rotas\Models\RotasAvailability;
use Workdo\Rotas\Models\Shift;

class RotaController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('manage-rotas')) {
            $settings = getCompanyAllSetting();

            // Get shifts
            $shifts = Shift::where('created_by', creatorId())->get();

            // Calculate week start setting
            $weekStartSetting = $settings['rotas_week_starts'] ?? 'monday';
            $weekStartMap = [
                'sunday' => 0,
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6,
            ];

            // Use provided dates or calculate current week
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
            } else {
                // Calculate current week dates
                $weekStart = $weekStartMap[$weekStartSetting] ?? 1;

                $today = now();
                $currentDay = $today->dayOfWeek;
                $diff = $currentDay - $weekStart;
                if ($diff < 0) {
                    $diff += 7;
                }

                $startDate = $today->copy()->subDays($diff);
                $endDate = $startDate->copy()->addDays(6);
            }

            // Get employees with filtering
            $employees = Employee::with(['user', 'shifts', 'availability' => function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                });
            }])->where(function ($q) use ($settings) {
                if (Auth::user()->can('manage-any-employees')) {
                    $q->where('created_by', creatorId());
                } elseif ($settings['rotas_employees_see_only_themselves'] === '1' && Auth::user()->not_emp_type) {
                    $q->where('user_id', Auth::id());
                } elseif (Auth::user()->can('manage-own-employees')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->when($request->get('branch_id') && $request->get('branch_id') !== 'all', function ($q) use ($request) {
                    $q->where('branch_id', $request->get('branch_id'));
                })
                ->when($request->get('department_id') && $request->get('department_id') !== 'all', function ($q) use ($request) {
                    $q->where('department_id', $request->get('department_id'));
                })
                ->when($request->get('designation_id') && $request->get('designation_id') !== 'all', function ($q) use ($request) {
                    $q->where('designation_id', $request->get('designation_id'));
                })
                ->orderBy('id', 'desc')->get();

            // Get rotas for current week
            $rotas = Rota::where(function ($q) use ($settings) {
                if (Auth::user()->can('manage-any-rotas')) {
                    $q->where('created_by', creatorId());
                } elseif ($settings['rotas_employees_see_only_themselves'] === '1' && Auth::user()->not_emp_type) {
                    $q->whereHas('employee', function ($eq) {
                        $eq->where('user_id', Auth::id());
                    });
                } elseif (Auth::user()->can('manage-own-rotas')) {
                    $q->where('issued_by', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
                ->whereBetween('rotas_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            // Check if week is published
            $weekPublished = $rotas->where('is_published', true)->count() > 0;
            $allRotasPublished = $rotas->count() > 0 && $rotas->where('is_published', false)->count() === 0;

            // Build employee data array with week schedule
            $employeesData = [];
            foreach ($employees as $employee) {
                $weekSchedule = [];
                $currentDate = $startDate->copy();
                
                for ($i = 0; $i < 7; $i++) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayOfWeek = strtolower($currentDate->format('l'));
                    
                    $dayRotas = $rotas->where('user_id', $employee->user_id)
                        ->where('rotas_date', $currentDate)
                        ->values()
                        ->map(function ($rota) {
                            return [
                                'id' => $rota->id,
                                'shiftId' => $rota->shift_id,
                                'startTime' => $rota->start_time->format('H:i'),
                                'endTime' => $rota->end_time->format('H:i'),
                                'breakTime' => $rota->break_time,
                                'type' => $rota->type,
                                'notes' => $rota->notes,
                                'isPublished' => $rota->is_published,
                            ];
                        })->toArray();
                    
                    $isWorkingDay = false;
                    if ($employee->work_schedule && is_array($employee->work_schedule)) {
                        $daySchedule = collect($employee->work_schedule)->firstWhere('day', $dayOfWeek);
                        $isWorkingDay = $daySchedule ? ($daySchedule['is_working'] ?? false) : false;
                    } else {
                        $workScheduleKey = 'rotas_work_schedule_'.$dayOfWeek;
                        $isWorkingDay = $settings[$workScheduleKey] ?? false;
                    }
                    
                    $availability = null;
                    if ($employee->availability->isNotEmpty()) {
                        $matchingAvailability = $employee->availability->first(function ($avail) use ($currentDate) {
                            return $avail->start_date <= $currentDate && $avail->end_date >= $currentDate;
                        });
                        
                        if ($matchingAvailability && isset($matchingAvailability->availability)) {
                            $dayAvailability = collect($matchingAvailability->availability)
                                ->where('day', $dayOfWeek)
                                ->values()
                                ->toArray();
                            $availability = $dayAvailability;
                        }
                    }
                    
                    $weekSchedule[] = [
                        'date' => $dateStr,
                        'day' => $dayOfWeek,
                        'isWorkingDay' => $isWorkingDay,
                        'availability' => $availability,
                        'shifts' => $dayRotas,
                    ];
                    
                    $currentDate->addDay();
                }
                
                $employeesData[] = [
                    'id' => $employee->id,
                    'employeeId' => $employee->employee_id,
                    'userId' => $employee->user_id,
                    'name' => $employee->user->name ?? '',
                    'email' => $employee->user->email ?? '',
                    'avatar' => $employee->user->avatar ?? null,
                    'branchId' => $employee->branch_id,
                    'departmentId' => $employee->department_id,
                    'designationId' => $employee->designation_id,
                    'shiftId' => $employee->shift,
                    'rate_per_hour' => $employee->rate_per_hour,
                    'user' => [
                        'name' => $employee->user->name ?? '',
                        'email' => $employee->user->email ?? '',
                        'avatar' => $employee->user->avatar ?? null,
                    ],
                    'weekSchedule' => $weekSchedule,
                ];
            }

            $branches = Branch::where('created_by', creatorId())->get();
            $departments = Department::where('created_by', creatorId())->get();
            $designations = Designation::where('created_by', creatorId())->get();

            // Get holidays for the week
            $holidays = [];
            if (Module_is_active('Hrm')) {
                $hrmHolidays = Holiday::where('created_by', creatorId())
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($q) use ($startDate, $endDate) {
                                $q->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    })
                    ->with('holidayType')
                    ->get();

                foreach ($hrmHolidays as $holiday) {
                    $holidays[] = [
                        'id' => $holiday->id,
                        'name' => $holiday->name,
                        'start_date' => $holiday->start_date->format('Y-m-d'),
                        'end_date' => $holiday->end_date->format('Y-m-d'),
                        'holiday_type' => $holiday->holidayType->name ?? 'Holiday',
                        'description' => $holiday->description,
                        'is_paid' => $holiday->is_paid,
                    ];
                }
            }

            // Get approved leave applications for the week
            $leaveApplications = [];
            $leaves = LeaveApplication::where('status', 'approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->with(['leave_type', 'employee'])
                ->get();

            foreach ($leaves as $leave) {
                $rotasEmployee = $employees->firstWhere('user_id', $leave->employee_id);
                if ($rotasEmployee) {
                    if (! isset($leaveApplications[$rotasEmployee->id])) {
                        $leaveApplications[$rotasEmployee->id] = [];
                    }
                    $leaveApplications[$rotasEmployee->id][] = [
                        'id' => $leave->id,
                        'start_date' => $leave->start_date->format('Y-m-d'),
                        'end_date' => $leave->end_date->format('Y-m-d'),
                        'leave_type' => $leave->leave_type->name ?? 'Leave',
                        'reason' => $leave->reason,
                        'status' => $leave->status,
                    ];
                }
            }

            return Inertia::render('Rotas/Rotas/Index', [
                'branches' => $branches,
                'departments' => $departments,
                'designations' => $designations,
                'employees' => $employeesData,
                'shifts' => $shifts,
                'weekDates' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ],
                'leaveApplications' => $leaveApplications,
                'holidays' => $holidays,
                'weekPublished' => $weekPublished,
                'allRotasPublished' => $allRotasPublished,
                'settings' => [
                    'rotas_week_starts' => $weekStartMap[$weekStartSetting] ?? 1,
                    'rotas_week_starts_name' => $weekStartSetting,
                ],
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function copyNextWeek(Rota $rota)
    {
        if (Auth::user()->can('create-rotas')) {
            $newRota = $rota->replicate();
            $newRota->title = $rota->title.' (Next Week)';
            $newRota->start_date = $rota->start_date->addWeek();
            $newRota->end_date = $rota->end_date->addWeek();
            $newRota->is_published = false;
            $newRota->creator_id = Auth::id();
            $newRota->created_by = creatorId();
            $newRota->save();

            return back()->with('success', __('The rota has been copied to next week successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create-rotas')) {
            $userId = $request->get('user_id');
            $employeeId = $request->get('employee_id');
            $shiftData = $request->get('shift_data');

            $employee = $employeeId 
                ? Employee::find($employeeId) 
                : Employee::where('user_id', $userId)->first();
                
            if (! $employee) {
                return back()->with('error', __('Employee not found'));
            }

            $shiftDate = Carbon::parse($shiftData['date']);
            $dayOfWeek = strtolower($shiftDate->format('l'));

            // Parse times and create full datetime
            $startTimeStr = $shiftData['startTime'];
            $endTimeStr = $shiftData['endTime'];

            $newStartTime = Carbon::parse($shiftDate->format('Y-m-d').' '.$startTimeStr);
            $newEndTime = Carbon::parse($shiftDate->format('Y-m-d').' '.$endTimeStr);

            // Handle overnight shifts - if end time is before start time, add one day
            if ($newEndTime->lessThanOrEqualTo($newStartTime)) {
                $newEndTime->addDay();
            }

            $canWork = false;
            if ($employee->work_schedule && is_array($employee->work_schedule)) {
                $daySchedule = collect($employee->work_schedule)->firstWhere('day', $dayOfWeek);
                $canWork = $daySchedule ? $daySchedule['is_working'] : false;
            } else {
                $settings = getCompanyAllSetting();
                $workScheduleKey = 'rotas_work_schedule_'.$dayOfWeek;
                $canWork = $settings[$workScheduleKey] ?? false;
            }

            if (! $canWork) {
                return back()->with('error', __('Employee is not scheduled to work on :day', ['day' => ucfirst($dayOfWeek)]));
            }

            $availability = RotasAvailability::where('employee_id', $employee->id)
                ->where('start_date', '<=', $shiftDate)
                ->where('end_date', '>=', $shiftDate)
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $availability) {
                return back()->with('error', __('Employee is not available on :day', ['day' => ucfirst($dayOfWeek)]));
            }

            $availableSlots = collect($availability->availability)
                ->filter(function ($slot) use ($dayOfWeek) {
                    return isset($slot['day'], $slot['type'], $slot['start_time'], $slot['end_time']) &&
                        $slot['day'] === $dayOfWeek &&
                        $slot['type'] === 'available';
                })->sortBy('start_time')->values();

            if ($availableSlots->isEmpty()) {
                return back()->with('error', __('Employee is not available on :day', ['day' => ucfirst($dayOfWeek)]));
            }

            $availabilityBlocks = [];

            foreach ($availableSlots as $slot) {
                $start = Carbon::createFromFormat('H:i', $slot['start_time']);
                $end = Carbon::createFromFormat('H:i', $slot['end_time']);

                $isOvernightSlot = $end->lessThan($start);

                if ($isOvernightSlot) {
                    $availabilityBlocks[] = ['start' => $start, 'end' => Carbon::createFromFormat('H:i', '23:59')];
                    $availabilityBlocks[] = ['start' => Carbon::createFromFormat('H:i', '00:00'), 'end' => $end];
                } else {
                    if (empty($availabilityBlocks)) {
                        $availabilityBlocks[] = ['start' => $start, 'end' => $end];

                        continue;
                    }

                    $lastIndex = count($availabilityBlocks) - 1;
                    $last = &$availabilityBlocks[$lastIndex];

                    if ($start->equalTo($last['end'])) {
                        $last['end'] = $end;
                    } else {
                        $availabilityBlocks[] = ['start' => $start, 'end' => $end];
                    }
                }
            }

            $existingShifts = Rota::where('user_id', $employee->user_id)
                ->where('rotas_date', $shiftDate->format('Y-m-d'))
                ->get(['start_time', 'end_time']);

            $remainingAvailability = [];

            foreach ($availabilityBlocks as $block) {
                $segments = [$block];

                foreach ($existingShifts as $shift) {
                    $shiftStart = Carbon::parse($shift->start_time);
                    $shiftEnd = Carbon::parse($shift->end_time);
                    $newSegments = [];
                    foreach ($segments as $seg) {
                        if ($shiftEnd <= $seg['start'] || $shiftStart >= $seg['end']) {
                            $newSegments[] = $seg;

                            continue;
                        }

                        if ($shiftStart > $seg['start']) {
                            $newSegments[] = ['start' => $seg['start'], 'end' => min($shiftStart, $seg['end'])];
                        }

                        if ($shiftEnd < $seg['end']) {
                            $newSegments[] = ['start' => max($shiftEnd, $seg['start']), 'end' => $seg['end']];
                        }
                    }
                    $segments = $newSegments;
                }

                foreach ($segments as $seg) {
                    if ($seg['start'] < $seg['end']) {
                        $remainingAvailability[] = $seg;
                    }
                }
            }

            $shiftAllowed = false;

            $newStartTimeOnly = Carbon::createFromFormat('H:i', $newStartTime->format('H:i'));
            $newEndTimeOnly = Carbon::createFromFormat('H:i', $newEndTime->format('H:i'));
            $isOvernightShift = $newEndTimeOnly->lessThanOrEqualTo($newStartTimeOnly);

            if ($isOvernightShift) {
                $part1Fits = false; // Evening part (start to midnight)
                $part2Fits = false; // Morning part (midnight to end)

                foreach ($remainingAvailability as $range) {
                    if ($newStartTimeOnly->greaterThanOrEqualTo($range['start']) &&
                        Carbon::createFromFormat('H:i', '23:59')->lessThanOrEqualTo($range['end'])) {
                        $part1Fits = true;
                    }
                    if (Carbon::createFromFormat('H:i', '00:00')->greaterThanOrEqualTo($range['start']) &&
                        $newEndTimeOnly->lessThanOrEqualTo($range['end'])) {
                        $part2Fits = true;
                    }
                }

                $shiftAllowed = $part1Fits && $part2Fits;
            } else {
                foreach ($remainingAvailability as $range) {
                    if ($newStartTimeOnly->greaterThanOrEqualTo($range['start']) && $newEndTimeOnly->lessThanOrEqualTo($range['end'])) {
                        $shiftAllowed = true;
                        break;
                    }
                }
            }

            if (! $shiftAllowed) {
                return back()->with('error', __('Employee is not available on This time range.'));
            }
            $rota = Rota::create([
                'user_id' => $employee->user_id,
                'rotas_date' => $shiftData['date'],
                'start_time' => $newStartTime->format('Y-m-d H:i:s'),
                'end_time' => $newEndTime->format('Y-m-d H:i:s'),
                'break_time' => $shiftData['breakTime'] ?? 0,
                'time_diff_in_minutes' => $newEndTime->diffInMinutes($newStartTime),
                'branch_id' => $employee->branch_id,
                'department_id' => $employee->department_id,
                'designation_id' => $employee->designation_id,
                'shift_id' => $employee->shift ?? null,
                'type' => $shiftData['type'] ?? 'shift',
                'is_published' => false,
                'notes' => $shiftData['notes'] ?? null,
                'issued_by' => Auth::id(),
                'created_by' => creatorId(),
            ]);

            CreateRota::dispatch($request, $rota);

            return back()->with('success', __('The shift has been saved successfully'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(Request $request, Rota $rota)
    {
        if (Auth::user()->can('edit-rotas')) {
            $shiftData = $request->get('shift_data');
            $userId = $request->get('user_id');
            $employeeId = $request->get('employee_id');

            $employee = $employeeId 
                ? Employee::find($employeeId) 
                : Employee::where('user_id', $userId)->first();
                
            if (! $employee) {
                return back()->with('error', __('Employee not found'));
            }

            $newStartTime = Carbon::parse($shiftData['startTime']);
            $newEndTime = Carbon::parse($shiftData['endTime']);
            $shiftDate = Carbon::parse($shiftData['date']);
            $dayOfWeek = strtolower($shiftDate->format('l'));

            $startTimeStr = $shiftData['startTime'];
            $endTimeStr = $shiftData['endTime'];
            $newStartTime = Carbon::parse($shiftDate->format('Y-m-d').' '.$startTimeStr);
            $newEndTime = Carbon::parse($shiftDate->format('Y-m-d').' '.$endTimeStr);

            if ($newEndTime->lessThanOrEqualTo($newStartTime)) {
                $newEndTime->addDay();
            }

            $canWork = false;
            if ($employee->work_schedule && is_array($employee->work_schedule)) {
                $daySchedule = collect($employee->work_schedule)->firstWhere('day', $dayOfWeek);
                $canWork = $daySchedule ? $daySchedule['is_working'] : false;
            } else {
                $settings = getCompanyAllSetting();
                $workScheduleKey = 'rotas_work_schedule_'.$dayOfWeek;
                $canWork = $settings[$workScheduleKey] ?? false;
            }

            if (! $canWork) {
                return back()->with('error', __('Employee is not scheduled to work on :day', ['day' => ucfirst($dayOfWeek)]));
            }

            $availability = RotasAvailability::where('employee_id', $employee->id)
                ->where('start_date', '<=', $shiftDate)
                ->where('end_date', '>=', $shiftDate)
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $availability) {
                return back()->with('error', __('Employee is not available on :day', ['day' => ucfirst($dayOfWeek)]));
            }

            $availableSlots = collect($availability->availability)
                ->filter(function ($slot) use ($dayOfWeek) {
                    return isset($slot['day'], $slot['type'], $slot['start_time'], $slot['end_time']) &&
                        $slot['day'] === $dayOfWeek &&
                        $slot['type'] === 'available';
                })
                ->sortBy('start_time')
                ->values();

            if ($availableSlots->isEmpty()) {
                return back()->with('error', __('Employee is not available on :day', ['day' => ucfirst($dayOfWeek)]));
            }

            $availabilityBlocks = [];

            foreach ($availableSlots as $slot) {
                $start = Carbon::createFromFormat('H:i', $slot['start_time']);
                $end = Carbon::createFromFormat('H:i', $slot['end_time']);

                $isOvernightSlot = $end->lessThan($start);

                if ($isOvernightSlot) {
                    $availabilityBlocks[] = ['start' => $start, 'end' => Carbon::createFromFormat('H:i', '23:59')];
                    $availabilityBlocks[] = ['start' => Carbon::createFromFormat('H:i', '00:00'), 'end' => $end];
                } else {
                    if (empty($availabilityBlocks)) {
                        $availabilityBlocks[] = ['start' => $start, 'end' => $end];

                        continue;
                    }

                    $lastIndex = count($availabilityBlocks) - 1;
                    $last = &$availabilityBlocks[$lastIndex];

                    if ($start->equalTo($last['end'])) {
                        $last['end'] = $end;
                    } else {
                        $availabilityBlocks[] = ['start' => $start, 'end' => $end];
                    }
                }
            }

            $existingShifts = Rota::where('user_id', $employee->user_id)
                ->where('rotas_date', $shiftDate->format('Y-m-d'))
                ->where('id', '!=', $rota->id)
                ->get(['start_time', 'end_time']);

            $remainingAvailability = [];

            foreach ($availabilityBlocks as $block) {
                $segments = [$block];

                foreach ($existingShifts as $shift) {
                    $shiftStart = Carbon::parse($shift->start_time);
                    $shiftEnd = Carbon::parse($shift->end_time);

                    $newSegments = [];

                    foreach ($segments as $seg) {
                        if ($shiftEnd <= $seg['start'] || $shiftStart >= $seg['end']) {
                            $newSegments[] = $seg;

                            continue;
                        }

                        if ($shiftStart > $seg['start']) {
                            $newSegments[] = ['start' => $seg['start'], 'end' => min($shiftStart, $seg['end'])];
                        }

                        if ($shiftEnd < $seg['end']) {
                            $newSegments[] = ['start' => max($shiftEnd, $seg['start']), 'end' => $seg['end']];
                        }
                    }

                    $segments = $newSegments;
                }

                foreach ($segments as $seg) {
                    if ($seg['start'] < $seg['end']) {
                        $remainingAvailability[] = $seg;
                    }
                }
            }

            $shiftAllowed = false;

            $newStartTimeOnly = Carbon::createFromFormat('H:i', $newStartTime->format('H:i'));
            $newEndTimeOnly = Carbon::createFromFormat('H:i', $newEndTime->format('H:i'));
            $isOvernightShift = $newEndTimeOnly->lessThanOrEqualTo($newStartTimeOnly);

            if ($isOvernightShift) {
                $part1Fits = false; // Evening part (start to midnight)
                $part2Fits = false; // Morning part (midnight to end)

                foreach ($remainingAvailability as $range) {
                    if ($newStartTimeOnly->greaterThanOrEqualTo($range['start']) &&
                        Carbon::createFromFormat('H:i', '23:59')->lessThanOrEqualTo($range['end'])) {
                        $part1Fits = true;
                    }
                    if (Carbon::createFromFormat('H:i', '00:00')->greaterThanOrEqualTo($range['start']) &&
                        $newEndTimeOnly->lessThanOrEqualTo($range['end'])) {
                        $part2Fits = true;
                    }
                }

                $shiftAllowed = $part1Fits && $part2Fits;
            } else {
                foreach ($remainingAvailability as $range) {
                    if ($newStartTimeOnly->greaterThanOrEqualTo($range['start']) && $newEndTimeOnly->lessThanOrEqualTo($range['end'])) {
                        $shiftAllowed = true;
                        break;
                    }
                }
            }

            if (! $shiftAllowed) {
                return back()->with('error', __('Employee is not available on This time range.'));
            }

            $rota->update([
                'rotas_date' => $shiftData['date'],
                'start_time' => $newStartTime->format('Y-m-d H:i:s'),
                'end_time' => $newEndTime->format('Y-m-d H:i:s'),
                'break_time' => $shiftData['breakTime'] ?? 0,
                'time_diff_in_minutes' => $newEndTime->diffInMinutes($newStartTime),
                'type' => $shiftData['type'] ?? 'shift',
                'notes' => $shiftData['notes'] ?? null,
            ]);

            UpdateRota::dispatch($request, $rota);

            return back()->with('success', __('The shift has been updated successfully'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Rota $rota)
    {
        if (Auth::user()->can('delete-rotas')) {
            DestroyRota::dispatch($rota);
            $rota->delete();

            return back()->with('success', __('The shift has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function createShareLink(Request $request)
    {
        if (Auth::user()->can('share-rotas')) {
            try {
                $validator = Validator::make($request->all(), [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'description' => 'nullable|string|max:500',
                    'has_expiry' => 'boolean',
                    'expiry_date' => 'nullable|date|after:now',
                    'has_password' => 'boolean',
                    'password' => 'nullable|string|min:6',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->errors()->first(),
                    ], 422);
                }
                $validated = $validator->validated();
                // Check if there are any published rotas for the selected date range
                $publishedRotasCount = Rota::where('created_by', creatorId())
                    ->where('is_published', true)
                    ->whereBetween('rotas_date', [$validated['start_date'], $validated['end_date']])
                    ->count();

                if ($publishedRotasCount === 0) {
                    return response()->json([
                        'error' => __('Cannot create share link. No published rotas found for the selected date range. Please publish the week rotas first.'),
                    ], 422);
                }

                if (Auth::user()->type == 'company') {
                    $userSlug = Auth::user()->slug;
                } else {
                    $userSlug = Auth::user()->createdBy->slug;
                }
                // Create encrypted query parameters
                $shareData = [
                    'company_id' => creatorId(),
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'description' => $validated['description'],
                    'created_at' => now()->toISOString(),
                ];

                if ($validated['has_expiry'] && $validated['expiry_date']) {
                    $shareData['expiry_date'] = $validated['expiry_date'];
                }

                if ($validated['has_password'] && $validated['password']) {
                    $shareData['password_hash'] = bcrypt($validated['password']);
                }

                // Encrypt the data
                $encryptedData = encrypt($shareData);
                $shareToken = base64_encode($encryptedData);

                // Generate share URL
                $shareUrl = url($userSlug.'/rotas/shared/'.urlencode($shareToken));

                return response()->json([
                    'success' => true,
                    'share_url' => $shareUrl,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 422);
            }
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function viewSharedSchedule(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $token = $request->route('token');
        try {
            // Decrypt the token
            $decryptedData = decrypt(base64_decode(urldecode($token)));
            $shareData = $decryptedData;

            // Check if expired
            if (isset($shareData['expiry_date']) && Carbon::parse($shareData['expiry_date'])->isPast()) {
                return Inertia::render('Rotas/Shared/Expired', [
                    'userSlug' => $userSlug,
                    'expiry_date' => $shareData['expiry_date'],
                ]);
            }

            // Check if password protected
            if (isset($shareData['password_hash'])) {
                if (! session('shared_schedule_authenticated_'.$token)) {
                    return Inertia::render('Rotas/Shared/Password', [
                        'userSlug' => $userSlug,
                        'token' => $token,
                    ]);
                }
            }

            // Get schedule data
            $startDate = Carbon::parse($shareData['start_date']);
            $endDate = Carbon::parse($shareData['end_date']);

            $employees = Employee::with(['user', 'shifts'])
                ->where('created_by', $shareData['company_id'])
                ->get();

            $shifts = Shift::where('created_by', $shareData['company_id'])->get();

            $rotas = Rota::where('created_by', $shareData['company_id'])
                ->where('is_published', true)
                ->whereBetween('rotas_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            $scheduleData = [];
            foreach ($rotas as $rota) {
                // Find employee to get employee_id
                $employee = $employees->firstWhere('user_id', $rota->user_id);
                if ($employee) {
                    if (! isset($scheduleData[$employee->id])) {
                        $scheduleData[$employee->id] = [];
                    }
                    $scheduleData[$employee->id][] = [
                        'id' => $rota->id,
                        'userId' => $rota->user_id,
                        'employeeId' => $employee->id,
                        'shiftId' => $rota->shift_id,
                        'date' => $rota->rotas_date->format('Y-m-d'),
                        'startTime' => $rota->start_time->format('H:i'),
                        'endTime' => $rota->end_time->format('H:i'),
                        'breakTime' => $rota->break_time,
                        'type' => $rota->type,
                        'notes' => $rota->notes,
                    ];
                }
            }

            // Get approved leave applications for the week
            $leaveApplications = [];
            $leaves = LeaveApplication::where('status', 'approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->with(['leave_type', 'employee'])
                ->get();

            foreach ($leaves as $leave) {
                // Find the rotas employee by user_id since leave.employee_id is actually user_id
                $rotasEmployee = $employees->firstWhere('user_id', $leave->employee_id);
                if ($rotasEmployee) {
                    if (! isset($leaveApplications[$rotasEmployee->id])) {
                        $leaveApplications[$rotasEmployee->id] = [];
                    }
                    $leaveApplications[$rotasEmployee->id][] = [
                        'id' => $leave->id,
                        'start_date' => $leave->start_date->format('Y-m-d'),
                        'end_date' => $leave->end_date->format('Y-m-d'),
                        'leave_type' => $leave->leave_type->name ?? 'Leave',
                        'reason' => $leave->reason,
                        'status' => $leave->status,
                    ];
                }
            }

            return Inertia::render('Rotas/Shared/Schedule', [
                'userSlug' => $userSlug,
                'employees' => $employees,
                'shifts' => $shifts,
                'scheduleData' => $scheduleData,
                'shareData' => $shareData,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
                'leaveApplications' => $leaveApplications,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Rotas/Shared/Invalid', [
                'userSlug' => $userSlug,
                'token' => $token,
            ]);
        }
    }

    public function authenticateSharedSchedule(Request $request)
    {
        try {
            $userSlug = $request->route('userSlug');
            $token = $request->route('token');
            $decryptedData = decrypt(base64_decode(urldecode($token)));
            $shareData = $decryptedData;

            if (isset($shareData['password_hash'])) {
                $password = $request->input('password');

                if (\Hash::check($password, $shareData['password_hash'])) {
                    session(['shared_schedule_authenticated_'.$token => true]);

                    return redirect($userSlug.'/rotas/shared/'.$token);
                } else {
                    return back()->withErrors(['password' => __('Invalid password')]);
                }
            }

            return redirect($userSlug.'/rotas/shared/'.$token);
        } catch (\Exception $e) {
            return Inertia::render('Rotas/Shared/Invalid');
        }
    }

    public function publishWeek(Request $request)
    {
        if (Auth::user()->can('publish-rotas')) {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $updated = Rota::where('created_by', creatorId())
                ->whereBetween('rotas_date', [$validated['start_date'], $validated['end_date']])
                ->update(['is_published' => true]);

            return back()->with('success', __("Published {$updated} rotas for the week successfully."));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function copyWeek(Request $request)
    {
        if (Auth::user()->can('create-rotas')) {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $rotas = Rota::where('created_by', creatorId())
                ->whereBetween('rotas_date', [$validated['start_date'], $validated['end_date']])
                ->get();

            $copied = 0;
            $updated = 0;
            $skipped = 0;
            $settings = getCompanyAllSetting();

            foreach ($rotas as $rota) {
                $nextWeekDate = Carbon::parse($rota->rotas_date)->addWeek();
                $dayOfWeek = strtolower($nextWeekDate->format('l'));
                
                $employee = Employee::where('user_id', $rota->user_id)->first();
                if (!$employee) {
                    $skipped++;
                    continue;
                }

                // Check work schedule
                $canWork = false;
                if ($employee->work_schedule && is_array($employee->work_schedule)) {
                    $daySchedule = collect($employee->work_schedule)->firstWhere('day', $dayOfWeek);
                    $canWork = $daySchedule ? ($daySchedule['is_working'] ?? false) : false;
                } else {
                    $workScheduleKey = 'rotas_work_schedule_'.$dayOfWeek;
                    $canWork = $settings[$workScheduleKey] ?? false;
                }

                if (!$canWork) {
                    $skipped++;
                    continue;
                }

                // Check availability
                $availability = RotasAvailability::where('employee_id', $employee->id)
                    ->where('start_date', '<=', $nextWeekDate)
                    ->where('end_date', '>=', $nextWeekDate)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$availability) {
                    $skipped++;
                    continue;
                }

                $availableSlots = collect($availability->availability)
                    ->filter(function ($slot) use ($dayOfWeek) {
                        return isset($slot['day'], $slot['type'], $slot['start_time'], $slot['end_time']) &&
                            $slot['day'] === $dayOfWeek &&
                            $slot['type'] === 'available';
                    })->values();

                if ($availableSlots->isEmpty()) {
                    $skipped++;
                    continue;
                }

                // Check if rota already exists for next week
                $existingRota = Rota::where('created_by', creatorId())
                    ->where('user_id', $rota->user_id)
                    ->where('rotas_date', $nextWeekDate->format('Y-m-d'))
                    ->where('start_time', $rota->start_time)
                    ->where('end_time', $rota->end_time)
                    ->first();

                if ($existingRota) {
                    $existingRota->update([
                        'break_time' => $rota->break_time,
                        'time_diff_in_minutes' => $rota->time_diff_in_minutes,
                        'type' => $rota->type,
                        'notes' => $rota->notes,
                        'is_published' => false,
                        'issued_by' => Auth::id(),
                    ]);
                    $updated++;
                } else {
                    $newRota = $rota->replicate();
                    $newRota->rotas_date = $nextWeekDate;
                    $newRota->is_published = false;
                    $newRota->issued_by = Auth::id();
                    $newRota->creator_id = Auth::id();
                    $newRota->created_by = creatorId();
                    $newRota->save();
                    $copied++;
                }
            }

            $message = [];
            if ($copied > 0) {
                $message[] = "Created {$copied} new rotas";
            }
            if ($updated > 0) {
                $message[] = "Updated {$updated} existing rotas";
            }
            if ($skipped > 0) {
                $message[] = "Skipped {$skipped} rotas, employee not scheduled or unavailable";
            }

            if (empty($message)) {
                return back()->with('error', __('No rotas could be copied. All employees are either not scheduled or unavailable for next week.'));
            }

            return back()->with('success', __(implode(', ', $message).' for next week.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function sendMail(Request $request)
    {
        if (Auth::user()->can('send-mail-rotas')) {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $employees = Employee::with(['user', 'rotas' => function ($query) use ($validated) {
                $query->whereBetween('rotas_date', [$validated['start_date'], $validated['end_date']])
                    ->with('shift');
            }])
                ->where('created_by', creatorId())
                ->whereHas('rotas', function ($query) use ($validated) {
                    $query->whereBetween('rotas_date', [$validated['start_date'], $validated['end_date']]);
                })
                ->get();

            $sent = 0;

            SetConfigEmail(creatorId());

            foreach ($employees as $employee) {
                if ($employee->user && $employee->user->email && $employee->rotas->count() > 0) {
                    try {
                        Mail::send('rotas::emails.schedule', [
                            'employee' => $employee,
                            'rotas' => $employee->rotas,
                            'start_date' => $validated['start_date'],
                            'end_date' => $validated['end_date'],
                            'company_name' => getCompanyAllSetting()['titleText'] ?? 'Company',
                        ], function ($message) use ($employee) {
                            $message->to($employee->user->email, $employee->user->name)
                                ->subject(__('Regarding to Rotas Detail'));
                        });
                        $sent++;
                    } catch (\Exception $e) {
                        $error = $e->getMessage();
                    }
                }
            }

            $response = back()->with('success', __("Schedule emails sent to {$sent} employees successfully."));

            if (! empty($error)) {
                $response->with('error', $error);
            }

            return $response;
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
