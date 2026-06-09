<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\Shift;
use Workdo\Rotas\Http\Requests\StoreRotasShiftRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasShiftRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Workdo\Rotas\Events\CreateShift;
use Workdo\Rotas\Events\UpdateShift;
use Workdo\Rotas\Events\DestroyShift;

class RotasShiftController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-shifts')) {
            $shifts = Shift::query()
                ->with(['creator'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-rotas-shifts')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-rotas-shifts')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Rotas/SystemSetup/Shifts/Index', [
                'shifts' => $shifts,
                'users' => User::where('created_by', creatorId())->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasShiftRequest $request)
    {
        if (Auth::user()->can('create-rotas-shifts')) {
            $validated = $request->validated();
            $shift = new Shift();
            $shift->shift_name = $validated['shift_name'];
            $shift->start_time = $validated['start_time'];
            $shift->end_time = $validated['end_time'];
            $shift->break_start_time = $validated['break_start_time'];
            $shift->break_end_time = $validated['break_end_time'];
            $shift->is_night_shift = $validated['is_night_shift'];
            $shift->creator_id = Auth::id();
            $shift->created_by = creatorId();
            $shift->save();

            CreateShift::dispatch($request, $shift);

            return back()->with('success', __('The shift has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasShiftRequest $request, Shift $shift)
    {
        if (Auth::user()->can('edit-rotas-shifts')) {
            $validated = $request->validated();
            $shift->shift_name = $validated['shift_name'];
            $shift->start_time = $validated['start_time'];
            $shift->end_time = $validated['end_time'];
            $shift->break_start_time = $validated['break_start_time'];
            $shift->break_end_time = $validated['break_end_time'];
            $shift->is_night_shift = $validated['is_night_shift'];
            $shift->save();

            UpdateShift::dispatch($request, $shift);

            return back()->with('success', __('The shift details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Shift $shift)
    {
        if (Auth::user()->can('delete-rotas-shifts')) {
            DestroyShift::dispatch($shift);
            $shift->delete();

            return back()->with('success', __('The shift has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
