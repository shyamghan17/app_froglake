<?php

namespace Workdo\SuggestionBox\Http\Controllers;

use Workdo\SuggestionBox\Models\SuggestionStatusHistory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SuggestionBox\Events\DestroySuggestionStatusHistory;
use Workdo\SuggestionBox\Models\Suggestion;

class SuggestionStatusHistoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-suggestion-status-histories')) {
            $suggestionstatushistories = SuggestionStatusHistory::query()
                ->with(['suggestion:id,title', 'user:id,name'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-suggestion-status-histories')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-suggestion-status-histories')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('comment'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('comment', 'like', '%' . request('comment') . '%')
                              ->orWhereHas('suggestion', function ($suggestionQuery) {
                                  $suggestionQuery->where('title', 'like', '%' . request('comment') . '%');
                              });
                    });
                })
                ->when(request('suggestion_id') && request('suggestion_id') !== 'all', fn($q) => $q->where('suggestion_id', request('suggestion_id')))
                ->when(request('changed_by') && request('changed_by') !== 'all', fn($q) => $q->where('changed_by', request('changed_by')))
                ->when(request('old_status') && request('old_status') !== 'all', fn($q) => $q->where('old_status', request('old_status')))
                ->when(request('new_status') && request('new_status') !== 'all', fn($q) => $q->where('new_status', request('new_status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('SuggestionBox/SuggestionStatusHistories/Index', [
                'suggestionstatushistories' => $suggestionstatushistories,
                'suggestionboxsuggestions'  => Suggestion::where('created_by', creatorId())->select('id', 'title')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(SuggestionStatusHistory $suggestionstatushistory)
    {
        if (Auth::user()->can('delete-suggestion-status-histories')) {
            DestroySuggestionStatusHistory::dispatch($suggestionstatushistory);
            $suggestionstatushistory->delete();

            return redirect()->back()->with('success', __('The statushistory has been deleted.'));
        } else {
            return redirect()->route('status-histories.index')->with('error', __('Permission denied'));
        }
    }
}
