<?php

namespace Workdo\SuggestionBox\Http\Controllers;

use Workdo\SuggestionBox\Models\Suggestion;
use Workdo\SuggestionBox\Models\SuggestionCategory;
use Workdo\SuggestionBox\Http\Requests\RespondSuggestionRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SuggestionBox\Events\CreateSuggestionStatusHistory;
use Workdo\SuggestionBox\Models\SuggestionStatusHistory;

class SuggestionAdminController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-suggestions')) {
            $suggestions = Suggestion::query()
                ->with([
                    'category:id,name,color', 
                    'user:id,name', 
                    'respondedBy:id,name',
                    'votes.user:id,name'
                ])
                ->where('created_by', creatorId())
                ->when(request('search'), function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
                })
                ->when(request('category_id') && request('category_id') !== 'all', fn($q) => $q->where('category_id', request('category_id')))
                ->when(request('status') && request('status') !== 'all', fn($q) => $q->where('status', request('status')))
                ->when(request('date_range'), function ($q, $dateRange) {
                    $dates = explode(' - ', $dateRange);
                    if (count($dates) === 2) {
                        $q->whereBetween('created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                    }
                })
                ->when(request('sort'), function ($q) {
                    $allowedSortFields = ['title', 'votes_count', 'views_count', 'created_at', 'status'];
                    $sortField = in_array(request('sort'), $allowedSortFields) ? request('sort') : 'created_at';
                    $direction = in_array(request('direction'), ['asc', 'desc']) ? request('direction') : 'desc';
                    $q->orderBy($sortField, $direction);
                }, function ($q) {
                    $q->orderBy('created_at', 'desc');
                })
                ->paginate(request('per_page', 10))
                ->withQueryString();


            $categories = SuggestionCategory::where('created_by', creatorId())
                ->where('is_active', true)
                ->orderBy('display_order')
                ->select('id', 'name', 'color')
                ->get();

            $stats = [
                'total'        => Suggestion::where('created_by', creatorId())->count(),
                'new'          => Suggestion::where('created_by', creatorId())->where('status', 'new')->count(),
                'under_review' => Suggestion::where('created_by', creatorId())->where('status', 'under_review')->count(),
                'accepted'     => Suggestion::where('created_by', creatorId())->where('status', 'accepted')->count(),
                'rejected'     => Suggestion::where('created_by', creatorId())->where('status', 'rejected')->count(),
                'complete'     => Suggestion::where('created_by', creatorId())->where('status', 'complete')->count(),
            ];

            return Inertia::render('SuggestionBox/Suggestions/AdminDashboard', [
                'suggestions' => $suggestions,
                'categories'  => $categories,
                'stats'       => $stats,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function respond(RespondSuggestionRequest $request, Suggestion $suggestion)
    {
        if (Auth::user()->can('respond-suggestions')) {
            $validated = $request->validated();

            $oldStatus = $suggestion->status;

            $suggestion->update([
                'status'         => $validated['status'],
                'admin_response' => $validated['admin_response'],
                'responded_by'   => Auth::id(),
                'responded_at'   => now(),
            ]);

            if ($oldStatus !== $validated['status']) {
                $statushistory                = new SuggestionStatusHistory();
                $statushistory->old_status    = $oldStatus;
                $statushistory->new_status    = $validated['status'];
                $statushistory->comment       = $validated['admin_response'];
                $statushistory->suggestion_id = $suggestion->id;
                $statushistory->changed_by    = $suggestion->responded_by;

                $statushistory->creator_id = Auth::id();
                $statushistory->created_by = creatorId();
                $statushistory->save();

                CreateSuggestionStatusHistory::dispatch($request, $statushistory);
            }

            return redirect()->back()->with('success', __('The response has been updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}
