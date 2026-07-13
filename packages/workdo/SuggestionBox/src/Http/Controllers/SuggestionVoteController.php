<?php

namespace Workdo\SuggestionBox\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\SuggestionBox\Models\Suggestion;
use Workdo\SuggestionBox\Models\SuggestionVote;

class SuggestionVoteController extends Controller
{
    public function vote(Request $request, $id)
    {
        if (Auth::user()->can('vote-suggestions')) {
            try {
                $suggestion = Suggestion::findOrFail($id);

            
                if ($suggestion->user_id === Auth::id()) {
                    return redirect()->back()->with('error', __('You cannot vote on your own suggestion'));
                }

                // Check if already voted
                $existingVote = SuggestionVote::where('suggestion_id', $id)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($existingVote) {
                    $existingVote->delete();

                    $currentCount            = $suggestion->votes_count ?? 0;
                    $suggestion->votes_count = max(0, $currentCount - 1);
                    $suggestion->save();

                    return redirect()->back()->with('success', __('Vote removed successfully'));
                } else {
                    $suggestionvote                = new SuggestionVote();
                    $suggestionvote->suggestion_id = $id;
                    $suggestionvote->user_id       = Auth::id();
                    $suggestionvote->save();

                    // Update votes_count
                    $currentCount            = $suggestion->votes_count ?? 0;
                    $suggestion->votes_count = $currentCount + 1;
                    $suggestion->save();

                    return redirect()->back()->with('success', __('Vote added successfully'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}
