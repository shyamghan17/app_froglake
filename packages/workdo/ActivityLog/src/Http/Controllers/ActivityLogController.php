<?php

namespace Workdo\ActivityLog\Http\Controllers;

use App\Http\Controllers\Controller;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\User;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-activity-log')){
            $creatorId = creatorId();
            
            $modules = AllActivityLog::select('module')
                ->where('created_by', $creatorId)
                ->groupBy('module')
                ->get()
                ->pluck('module')
                ->toArray();
            
            // Filter modules based on user's purchased add-ons
            $userAvailableModules = Plan::getUserSubscriptionModules();
            $modules = array_values(array_intersect($modules, $userAvailableModules));
            
            $staffs = User::where('created_by', $creatorId)
                ->orWhere('id', $creatorId)
                ->get();

            $activityLogs = AllActivityLog::with(['user'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-activity-log')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-activity-log')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('module'), fn($q) => $q->where('module', request('module')))
                ->when(request('search'), function($q) {
                    $search = request('search');
                    $q->where(function($query) use ($search) {
                        $query->where('module', 'like', '%' . $search . '%')
                              ->orWhere('sub_module', 'like', '%' . $search . '%')
                              ->orWhere('description', 'like', '%' . $search . '%');
                    });
                })
                ->when(request('user_id'), fn($q) => $q->where('creator_id', request('user_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('ActivityLog/Index', [
                'activityLogs' => $activityLogs,
                'modules' => $modules,
                'staffs' => $staffs,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(AllActivityLog $activityLog)
    {
        if(Auth::user()->can('delete-activity-log')){
            $activityLog->delete();

            return back()->with('success', __('The activity log has been deleted.'));
        }
        else{
            return redirect()->route('activity-logs.index')->with('error', __('Permission denied'));
        }
    }
}