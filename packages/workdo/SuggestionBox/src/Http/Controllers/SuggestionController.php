<?php

namespace Workdo\SuggestionBox\Http\Controllers;

use Workdo\SuggestionBox\Models\Suggestion;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SuggestionBox\Models\SuggestionCategory;
use Workdo\SuggestionBox\Models\SuggestionView;
use App\Models\User;
use Workdo\SuggestionBox\Events\CreateSuggestion;
use Workdo\SuggestionBox\Events\DestroySuggestion;
use Workdo\SuggestionBox\Http\Requests\StoreSuggestionRequest;
use Workdo\SuggestionBox\Http\Requests\UpdateSuggestionRequest;
use Workdo\SuggestionBox\Events\UpdateSuggestion;

class SuggestionController extends Controller
{
    private function checkSuggestionAccess(Suggestion $suggestion)
    {
        if ($suggestion->created_by !== creatorId()) {
            return false;
        }

        if (Auth::user()->can('manage-any-suggestions')) {
            return true;
        } elseif (Auth::user()->can('manage-own-suggestions')) {
            if ($suggestion->creator_id == Auth::id()) {
                return true;
            }
            // Can view others' suggestions only if not new or rejected
            if (!in_array($suggestion->status, ['new', 'rejected'])) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function index()
    {
        if (Auth::user()->can('manage-suggestions')) {
            $suggestions = Suggestion::query()
                ->with(['category:id,name,color', 'user', 'respondedBy'])
                ->withExists(['votes as has_voted' => function ($query) {
                    $query->where('user_id', Auth::id());
                }])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-suggestions')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-suggestions')) {
                        $q->where(function ($query) {
                            $query->where('creator_id', Auth::id())
                                ->orWhere(function ($subQuery) {
                                    $subQuery->where('created_by', creatorId())
                                        ->whereNotIn('status', ['new', 'rejected']);
                                });
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('title', 'like', '%' . request('name') . '%');
                        $query->orWhere('description', 'like', '%' . request('name') . '%');
                    });
                })
                ->when(request('status') !== null && request('status') !== '' && request('status') !== 'all', fn($q) => $q->where('status', request('status')))
                ->when(request('category_id') !== null && request('category_id') !== '' && request('category_id') !== 'all', fn($q) => $q->where('category_id', request('category_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $categories = SuggestionCategory::where('created_by', creatorId())->where('is_active', true)->orderBy('display_order')->select('id', 'name', 'color')->get();

            return Inertia::render('SuggestionBox/Suggestions/Index', [
                'suggestions' => $suggestions,
                'categories'  => $categories,
                'users'       => User::where('created_by', creatorId())->emp()->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreSuggestionRequest $request)
    {
        if (Auth::user()->can('create-suggestions')) {
            $validated = $request->validated();

            $exists = Suggestion::where('user_id', Auth::id())
                ->where('title', $validated['title'])
                ->exists();

            if ($exists) {
                return redirect()->route('suggestions.index')->with('error', __('You have already submitted a suggestion with this title.'));
            }

            $suggestion               = new Suggestion();
            $suggestion->title        = $validated['title'];
            $suggestion->category_id  = $validated['category_id'];
            $suggestion->description  = $validated['description'];
            $suggestion->is_anonymous = $validated['is_anonymous'];
            $suggestion->user_id      = Auth::id();
            $suggestion->creator_id   = Auth::id();
            $suggestion->created_by   = creatorId();
            $suggestion->save();

            CreateSuggestion::dispatch($request, $suggestion);

            return redirect()->route('suggestions.index')->with('success', __('The suggestion has been created successfully.'));
        } else {
            return redirect()->route('suggestions.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateSuggestionRequest $request, Suggestion $suggestion)
    {
        if (Auth::user()->can('edit-suggestions') && $suggestion->status === 'new' && $suggestion->user_id === Auth::id()) {
            $validated = $request->validated();

            $suggestion->update([
                'title'        => $validated['title'],
                'category_id'  => $validated['category_id'],
                'description'  => $validated['description'],
                'is_anonymous' => $validated['is_anonymous'] ?? false,
            ]);

            UpdateSuggestion::dispatch($request, $suggestion);

            return redirect()->route('suggestions.my-suggestions')->with('success', __('The suggestion has been updated successfully.'));
        } else {
            return redirect()->route('suggestions.index')->with('error', __('Permission denied'));
        }
    }

    public function show(Suggestion $suggestion)
    {
        if (Auth::user()->can('view-suggestions')) {
            if (!$this->checkSuggestionAccess($suggestion)) {
                return redirect()->route('suggestions.index')->with('error', __('Permission denied'));
            }

            $hasViewed = SuggestionView::where('suggestion_id', $suggestion->id)
                ->where('user_id', Auth::id())
                ->exists();

            if (!$hasViewed) {
                SuggestionView::create([
                    'suggestion_id' => $suggestion->id,
                    'user_id' => Auth::id(),
                ]);

                $suggestion->increment('views_count');
            }

            $suggestion->load(['category', 'user', 'respondedBy', 'votes.user:id,name', 'views.user:id,name']);

            $voters = $suggestion->votes->pluck('user.name')->filter()->values();
            $suggestion->voters = $voters;

            $viewers = $suggestion->views->pluck('user.name')->filter()->values();
            $suggestion->viewers = $viewers;

            return Inertia::render('SuggestionBox/Suggestions/View', [
                'suggestion' => $suggestion,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Suggestion $suggestion)
    {
        if (Auth::user()->can('delete-suggestions')) {
            DestroySuggestion::dispatch($suggestion);
            $suggestion->delete();

            return redirect()->back()->with('success', __('The suggestion has been deleted.'));
        } else {
            return redirect()->route('suggestions.index')->with('error', __('Permission denied'));
        }
    }

    public function mySuggestions()
    {
        if (Auth::user()->can('manage-own-suggestions')) {
            $suggestions = Suggestion::query()
                ->with(['category:id,name,color', 'user:id,name', 'respondedBy:id,name'])
                ->where('user_id', Auth::id())
                ->where('created_by', creatorId())
                ->when(request('search'), function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    });
                })
                ->when(request('category_id') && request('category_id') !== 'all', fn($q) => $q->where('category_id', request('category_id')))
                ->when(request('status') && request('status') !== 'all', fn($q) => $q->where('status', request('status')))
                ->orderBy('created_at', 'desc')
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $categories = SuggestionCategory::where('created_by', creatorId())
                ->where('is_active', true)
                ->orderBy('display_order')
                ->select('id', 'name', 'color')
                ->get();

            return Inertia::render('SuggestionBox/Suggestions/MySuggestions', [
                'suggestions' => $suggestions,
                'categories'  => $categories,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
