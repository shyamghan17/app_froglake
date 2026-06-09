<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Models\BeautyWorking;
use Workdo\BeautySpaManagement\Events\UpdateWorkingHours;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Http\Requests\UpdateWorkingHoursRequest;

class BeautyWorkingController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-working')) {
            $work      = BeautyWorking::where('created_by', creatorId())->first();
            $week_days = BeautyWorking::$week_days;
            
              // Generate business_hours array from existing data
            $business_hours = [];
            if ($work && $work->day_of_week) {
                $workingDays = explode(',', $work->day_of_week);
                foreach ($week_days as $day) {
                    $business_hours[] = [
                        'day'     => $day,
                        'is_open' => in_array($day, $workingDays)
                    ];
                }
            } else {
                  // Default business hours
                foreach ($week_days as $day) {
                    $business_hours[] = [
                        'day'     => $day,
                        'is_open' => !in_array($day, ['Saturday', 'Sunday'])
                    ];
                }
            }
            
            return Inertia::render('BeautySpaManagement/SystemSetup/WorkingHours/Index', [
                'work'           => $work,
                'week_days'      => $week_days,
                'business_hours' => $business_hours,
                'isHrmActive'    => module_is_active('Hrm'),
            ]);
        } else {
            return back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(UpdateWorkingHoursRequest $request)
    {
        if (Auth::user()->can('edit-beauty-working')) {
            $validated = $request->validated();
            
            $workingHours = BeautyWorking::where('created_by', creatorId())->first();
            
            if (!$workingHours) {
                $workingHours             = new BeautyWorking();
                $workingHours->creator_id = Auth::id();
                $workingHours->created_by = creatorId();
            }
            
            $workingHours->opening_time    = $validated['opening_time'];
            $workingHours->closing_time    = $validated['closing_time'];
            $workingHours->holiday_setting = $validated['holiday_setting'] ?? 'off';
            
              // Extract working days from business_hours
            $workingDays = [];
            foreach ($validated['business_hours'] as $businessHour) {
                if ($businessHour['is_open']) {
                    $workingDays[] = $businessHour['day'];
                }
            }
            $workingHours->day_of_week = implode(',', $workingDays);
            $workingHours->save();
            
            UpdateWorkingHours::dispatch($request, $workingHours);

            return back()->with('success', __('The working hours has been updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.working-hours.index')->with('error', __('Permission denied'));
        }
    }
}