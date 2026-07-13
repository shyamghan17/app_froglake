<?php

namespace Workdo\SuggestionBox\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\SuggestionBox\Models\Suggestion;
use Workdo\SuggestionBox\Models\SuggestionVote;
use App\Models\User;

class DemoSuggestionVoteSeeder extends Seeder
{
    public function run($userId): void
    {
        if (SuggestionVote::whereHas('suggestion', function ($query) use ($userId) {
            $query->where('created_by', $userId);
        })->exists()) {
            return;
        }

        if (!empty($userId)) {
            $suggestions = Suggestion::where('created_by', $userId)->get();
            $users       = User::where('created_by', $userId)->emp()->get();

            if ($suggestions->isEmpty() || $users->isEmpty()) {
                return;
            }

            foreach ($suggestions as $suggestion) {
                $votesNeeded = $suggestion->votes_count;
                
                if ($votesNeeded > 0) {
                    // Get users excluding the suggestion creator (users can't vote on their own suggestions)
                    $eligibleUsers = $users->where('id', '!=', $suggestion->user_id);
                    
                    if ($eligibleUsers->count() < $votesNeeded) {
                        // If not enough eligible users, adjust the votes_count in suggestion
                        $actualVotesCount = $eligibleUsers->count();
                        $suggestion->update(['votes_count' => $actualVotesCount]);
                        $votesNeeded = $actualVotesCount;
                    }
                    
                    // Select exact number of users needed for votes
                    if ($votesNeeded > 0) {
                        $voters = $eligibleUsers->random($votesNeeded);
                        
                        foreach ($voters as $voter) {
                            SuggestionVote::create([
                                'suggestion_id' => $suggestion->id,
                                'user_id'       => $voter->id,
                                'created_at'    => now()->subDays(rand(1, 30)),
                                'updated_at'    => now()->subDays(rand(1, 30)),
                            ]);
                        }
                    }
                }
            }
        }
    }
}