<?php

namespace Workdo\FindGoogleLeads\Http\Controllers;

use App\Http\Controllers\Controller;
use Workdo\FindGoogleLeads\Http\Requests\UpdateFindGoogleLeadsSettingsRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Workdo\Lead\Models\LeadStage;
use Workdo\Lead\Models\Pipeline;

class FindGoogleLeadsSettingsController extends Controller
{
    public function update(UpdateFindGoogleLeadsSettingsRequest $request)
    {
        if (Auth::user()->can('edit-findgoogleleads-settings')) {
            $validated = $request->validated();

            $settings = $validated['settings'];
            try {
                foreach ($settings as $key => $value) {
                    setSetting($key, $value, creatorId(),false);
                }

                return redirect()->back()->with('success', __('Find Google Leads settings save successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Failed to update find google leads settings: ') . $e->getMessage());
            }
        }
        return back()->with('error', __('Permission denied'));
    }

    public function getStages(Request $request)
    {
        if (Module_is_active('Lead')) {
            $pipelineId = $request->input('id');
            $stages = LeadStage::where('pipeline_id', $pipelineId)->pluck('name', 'id')->toArray();

            return response()->json([
                'status' => 1,
                'data' => $stages
            ]);
        }else{
            return response()->json([
                    'status' => 0,
                    'message' => __('CRM Add-On is not active.')
                ]);
        }
    }

    public function getPipelinesAndStages()
    {
        if (Module_is_active('Lead')) {
            $pipelines = Pipeline::where('created_by', creatorId())->get(['id', 'name']);
            $leadStages = LeadStage::whereIn('pipeline_id', $pipelines->pluck('id'))->get(['id', 'name', 'pipeline_id']);

            return response()->json([
                'pipelines' => $pipelines,
                'leadStages' => $leadStages
            ]);
        }else{
            return response()->json([
                   'status' => 0,
                   'message' => __('CRM Add-On is not active.')
               ]);
        }
    }
}
