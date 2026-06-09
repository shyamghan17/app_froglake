<?php

namespace Workdo\Rotas\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Models\Rota;
use Workdo\Rotas\Models\RotasAvailability;
use Carbon\Carbon;

class DemoRotasDataSeeder extends Seeder
{
    public function run($userId): void
    {
        // Get existing employees
        $employees = Employee::where('created_by', $userId)->get();

        if ($employees->isEmpty()) {
            return; // No employees to create rotas for
        }

        $months = [
            now()->startOfMonth(),
            now()->addMonth()->startOfMonth()
        ];

        foreach ($months as $monthStart) {
            $monthEnd = $monthStart->copy()->endOfMonth();

            // Create availability records for each employee for this month
            foreach ($employees as $employee) {
                $shift = $employee->shifts;
                $startTime = $shift ? $shift->start_time->format('H:i') : '09:00';
                $endTime = $shift ? $shift->end_time->format('H:i') : '17:00';

                // Randomly make some days "unavailable"
                $availabilityData = [
                    ['day' => 'monday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'available'],
                    ['day' => 'tuesday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'available'],
                    ['day' => 'wednesday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'available'],
                    ['day' => 'thursday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'available'],
                    ['day' => 'friday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'available'],
                    ['day' => 'saturday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'unavailable'],
                    ['day' => 'sunday', 'start_time' => $startTime, 'end_time' => $endTime, 'type' => 'unavailable'],
                ];

                RotasAvailability::updateOrCreate([
                    'employee_id' => $employee->id,
                    'start_date' => $monthStart->format('Y-m-d'),
                    'end_date' => $monthEnd->format('Y-m-d'),
                ], [
                    'name' => $monthStart->format('F Y') . ' Availability',
                    'availability' => $availabilityData,
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]);
            }

            // Create rotas for each week of the month
            $currentWeek = $monthStart->copy()->startOfWeek();
            while ($currentWeek->lte($monthEnd)) {
                $this->createWeeklyRotas($employees, $userId, $currentWeek);
                $currentWeek->addWeek();
            }
        }
    }

    private function createWeeklyRotas($employees, $userId, $weekStart)
    {
        $patterns = [
            ['monday' => 'shift:09:00-17:00', 'tuesday' => 'shift:09:00-17:00', 'wednesday' => 'shift:09:00-17:00', 'thursday' => 'shift:09:00-17:00', 'friday' => 'shift:09:00-17:00', 'saturday' => 'dayoff', 'sunday' => 'dayoff'],
            ['monday' => 'shift:08:00-16:00', 'tuesday' => 'shift:08:00-16:00', 'wednesday' => 'leave', 'thursday' => 'shift:08:00-16:00', 'friday' => 'shift:08:00-16:00', 'saturday' => 'shift:10:00-14:00', 'sunday' => 'dayoff'],
            ['monday' => 'dayoff', 'tuesday' => 'shift:14:00-22:00', 'wednesday' => 'shift:14:00-22:00', 'thursday' => 'shift:14:00-22:00', 'friday' => 'shift:14:00-22:00', 'saturday' => 'shift:14:00-22:00', 'sunday' => 'dayoff'],
        ];

        $notes = [
            'shift' => ['Team meeting at 10 AM', 'Training session at 2 PM', 'Cover reception during lunch', 'Client presentation', 'Inventory check', null, null, null],
            'leave' => ['Annual leave', 'Sick leave', 'Personal leave', 'Medical appointment'],
            'dayoff' => ['Scheduled day off', 'Weekend off', null]
        ];

        foreach ($employees as $index => $employee) {
            $pattern = $patterns[$index % count($patterns)];

            foreach ($pattern as $day => $entry) {
                $dayIndex = ['monday' => 0, 'tuesday' => 1, 'wednesday' => 2, 'thursday' => 3, 'friday' => 4, 'saturday' => 5, 'sunday' => 6][$day];
                $rotaDate = $weekStart->copy()->addDays($dayIndex);

                // Check Work Schedule
                $schedule = $employee->work_schedule;
                if (isset($schedule[$day]) && empty($schedule[$day])) {
                    continue; // Employee not scheduled to work today
                }

                // Check Availabilities
                $availability = RotasAvailability::where('employee_id', $employee->id)
                    ->where('start_date', '<=', $rotaDate->format('Y-m-d'))
                    ->where('end_date', '>=', $rotaDate->format('Y-m-d'))
                    ->first();

                if ($availability) {
                    $dayAvail = collect($availability->availability)->firstWhere('day', $day);
                    if ($dayAvail && $dayAvail['type'] === 'unavailable') {
                        continue; // Employee is unavailable today
                    }
                }

                if (str_starts_with($entry, 'shift:')) {
                    $timeSlot = substr($entry, 6);
                    [$startTime, $endTime] = explode('-', $timeSlot);
                    $start = Carbon::createFromFormat('H:i', $startTime);
                    $end = Carbon::createFromFormat('H:i', $endTime);
                    $totalMinutes = abs($end->diffInMinutes($start));
                    $breakTime = $totalMinutes > 360 ? 60 : 30;

                    Rota::updateOrCreate([
                        'user_id' => $employee->user_id,
                        'rotas_date' => $rotaDate->format('Y-m-d'),
                    ], [
                        'start_time' => $startTime . ':00',
                        'end_time' => $endTime . ':00',
                        'break_time' => $breakTime,
                        'time_diff_in_minutes' => $totalMinutes,
                        'branch_id' => $employee->branch_id,
                        'department_id' => $employee->department_id,
                        'designation_id' => $employee->designation_id,
                        'shift_id' => $employee->shift_id,
                        'type' => 'shift',
                        'is_published' => rand(0, 1),
                        'notes' => $notes['shift'][array_rand($notes['shift'])],
                        'issued_by' => $userId,
                        'creator_id' => $userId,
                        'created_by' => $userId,
                    ]);
                } else {
                    Rota::updateOrCreate([
                        'user_id' => $employee->user_id,
                        'rotas_date' => $rotaDate->format('Y-m-d'),
                    ], [
                        'start_time' => null,
                        'end_time' => null,
                        'break_time' => 0,
                        'time_diff_in_minutes' => 0,
                        'branch_id' => $employee->branch_id,
                        'department_id' => $employee->department_id,
                        'designation_id' => $employee->designation_id,
                        'shift_id' => null,
                        'type' => $entry,
                        'is_published' => true,
                        'notes' => $notes[$entry][array_rand($notes[$entry])],
                        'issued_by' => $userId,
                        'creator_id' => $userId,
                        'created_by' => $userId,
                    ]);
                }
            }
        }
    }
}
