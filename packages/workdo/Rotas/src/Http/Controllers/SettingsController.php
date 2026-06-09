<?php

namespace Workdo\Rotas\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        if (Auth::user()->can('edit-rotas-settings')) {

            $validator = Validator::make($request->all(),[
                    'settings.rotas_show_employee_price' => 'required|boolean',
                    'settings.rotas_show_employee_avatars' => 'required|boolean',
                    'settings.rotas_hide_employee_hours' => 'required|boolean',
                    'settings.rotas_include_unpublished_shifts' => 'required|boolean',
                    'settings.rotas_employees_see_only_themselves' => 'required|boolean',
                    'settings.rotas_week_starts' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                    'settings.rotas_break_type' => 'required|string|in:paid,unpaid',
                ],
                [
                    'settings.rotas_show_employee_price.required' => __('Employee price display setting is required.'),
                    'settings.rotas_show_employee_price.boolean' => __('Employee price display must be true or false.'),
                    'settings.rotas_show_employee_avatars.required' => __('Employee avatars display setting is required.'),
                    'settings.rotas_show_employee_avatars.boolean' => __('Employee avatars display must be true or false.'),
                    'settings.rotas_hide_employee_hours.required' => __('Hide employee hours setting is required.'),
                    'settings.rotas_hide_employee_hours.boolean' => __('Hide employee hours must be true or false.'),
                    'settings.rotas_include_unpublished_shifts.required' => __('Include unpublished shifts setting is required.'),
                    'settings.rotas_include_unpublished_shifts.boolean' => __('Include unpublished shifts must be true or false.'),
                    'settings.rotas_employees_see_only_themselves.required' => __('Employees see only themselves setting is required.'),
                    'settings.rotas_employees_see_only_themselves.boolean' => __('Employees see only themselves must be true or false.'),
                    'settings.rotas_week_starts.required' => __('Week start day is required.'),
                    'settings.rotas_week_starts.in' => __('Week start day must be a valid day of the week.'),
                    'settings.rotas_break_type.required' => __('Break type is required.'),
                    'settings.rotas_break_type.in' => __('Break type must be paid or unpaid.'),
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', __('Validation failed'));
            }

            $settings = $request->input('settings', []);

            try {
                foreach ($settings as $key => $value) {
                    setSetting($key, $value, creatorId());
                }

                return redirect()->back()->with('success', __('The rotas settings has been saved successfully.'));
            } catch (\Exception $e) {
                return back()->with('error', __('Failed to update rotas settings: ') . $e->getMessage());
            }
        }
        else
        {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function updateWorkSchedule(Request $request)
    {
        if (Auth::user()->can('edit-work-schedule-settings')) {

            $validator = Validator::make($request->all(),[
                    'settings.rotas_work_schedule_monday' => 'required|boolean',
                    'settings.rotas_work_schedule_tuesday' => 'required|boolean',
                    'settings.rotas_work_schedule_wednesday' => 'required|boolean',
                    'settings.rotas_work_schedule_thursday' => 'required|boolean',
                    'settings.rotas_work_schedule_friday' => 'required|boolean',
                    'settings.rotas_work_schedule_saturday' => 'required|boolean',
                    'settings.rotas_work_schedule_sunday' => 'required|boolean',
                ],
                [
                    'settings.rotas_work_schedule_monday.required' => __('Monday work schedule is required.'),
                    'settings.rotas_work_schedule_monday.boolean' => __('Monday work schedule must be true or false.'),
                    'settings.rotas_work_schedule_tuesday.required' => __('Tuesday work schedule is required.'),
                    'settings.rotas_work_schedule_tuesday.boolean' => __('Tuesday work schedule must be true or false.'),
                    'settings.rotas_work_schedule_wednesday.required' => __('Wednesday work schedule is required.'),
                    'settings.rotas_work_schedule_wednesday.boolean' => __('Wednesday work schedule must be true or false.'),
                    'settings.rotas_work_schedule_thursday.required' => __('Thursday work schedule is required.'),
                    'settings.rotas_work_schedule_thursday.boolean' => __('Thursday work schedule must be true or false.'),
                    'settings.rotas_work_schedule_friday.required' => __('Friday work schedule is required.'),
                    'settings.rotas_work_schedule_friday.boolean' => __('Friday work schedule must be true or false.'),
                    'settings.rotas_work_schedule_saturday.required' => __('Saturday work schedule is required.'),
                    'settings.rotas_work_schedule_saturday.boolean' => __('Saturday work schedule must be true or false.'),
                    'settings.rotas_work_schedule_sunday.required' => __('Sunday work schedule is required.'),
                    'settings.rotas_work_schedule_sunday.boolean' => __('Sunday work schedule must be true or false.'),
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', __('Validation failed'));
            }

            $settings = $request->input('settings', []);

            try {
                foreach ($settings as $key => $value) {
                    setSetting($key, $value, creatorId());
                }

                return redirect()->back()->with('success', __('The work schedule settings has been saved successfully.'));
            } catch (\Exception $e) {
                return back()->with('error', __('Failed to update work schedule settings: ') . $e->getMessage());
            }
        }
        else
        {
            return back()->with('error', __('Permission denied'));
        }
    }
}