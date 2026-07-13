<?php

namespace Workdo\SuggestionBox\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\SuggestionBox\Models\Suggestion;
use Workdo\SuggestionBox\Models\SuggestionView;
use App\Models\User;

class DemoSuggestionViewSeeder extends Seeder
{
    public function run($userId): void
    {
        if (SuggestionView::whereHas('suggestion', function ($query) use ($userId) {
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
                $viewsNeeded = $suggestion->views_count;
                
                if ($viewsNeeded > 0) {
                    // All users can view suggestions (including creator)
                    $eligibleUsers = $users;
                    
                    if ($eligibleUsers->count() < $viewsNeeded) {
                        // If not enough users, adjust the views_count in suggestion
                        $actualViewsCount = $eligibleUsers->count();
                        $suggestion->update(['views_count' => $actualViewsCount]);
                        $viewsNeeded = $actualViewsCount;
                    }
                    
                    // Select exact number of users needed for views
                    if ($viewsNeeded > 0) {
                        $viewers = $eligibleUsers->random($viewsNeeded);
                        
                        foreach ($viewers as $viewer) {
                            SuggestionView::create([
                                'suggestion_id' => $suggestion->id,
                                'user_id'       => $viewer->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}