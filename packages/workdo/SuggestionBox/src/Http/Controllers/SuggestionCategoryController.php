<?php

namespace Workdo\SuggestionBox\Http\Controllers;

use Workdo\SuggestionBox\Models\SuggestionCategory;
use Workdo\SuggestionBox\Http\Requests\StoreSuggestionCategoryRequest;
use Workdo\SuggestionBox\Http\Requests\UpdateSuggestionCategoryRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SuggestionBox\Events\CreateSuggestionCategory;
use Workdo\SuggestionBox\Events\DestroySuggestionCategory;
use Workdo\SuggestionBox\Events\UpdateSuggestionCategory;

class SuggestionCategoryController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-suggestion-categories')){
            $suggestioncategories = SuggestionCategory::query()

                ->where(function($q) {
                    if(Auth::user()->can('manage-any-suggestion-categories')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-suggestion-categories')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function($q) {
                    $q->where(function($query) {
                    $query->where('name', 'like', '%' . request('name') . '%');
                    $query->orWhere('description', 'like', '%' . request('name') . '%');
                    });
                })
                ->when(request('is_active') !== null && request('is_active') !== '', fn($q) => $q->where('is_active', request('is_active') === '1'))

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('SuggestionBox/SuggestionCategories/Index', [
                'suggestioncategories' => $suggestioncategories,

            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreSuggestionCategoryRequest $request)
    {
        if(Auth::user()->can('create-suggestion-categories')){
            $validated              = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', false);

            $suggestioncategory                = new SuggestionCategory();
            $suggestioncategory->name          = $validated['name'];
            $suggestioncategory->color         = $validated['color'];
            $suggestioncategory->description   = $validated['description'];
            $suggestioncategory->is_active     = $validated['is_active'];
            $suggestioncategory->display_order = $validated['display_order'];
            $suggestioncategory->creator_id    = Auth::id();
            $suggestioncategory->created_by    = creatorId();
            $suggestioncategory->save();

            CreateSuggestionCategory::dispatch($request, $suggestioncategory);

            return redirect()->route('suggestion-categories.index')->with('success', __('The category has been created successfully.'));
        }
        else{
            return redirect()->route('suggestion-categories.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateSuggestionCategoryRequest $request, SuggestionCategory $suggestioncategory)
    {
        if(Auth::user()->can('edit-suggestion-categories')){
            $validated              = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', false);

            $suggestioncategory->name          = $validated['name'];
            $suggestioncategory->color         = $validated['color'];
            $suggestioncategory->description   = $validated['description'];
            $suggestioncategory->is_active     = $validated['is_active'];
            $suggestioncategory->display_order = $validated['display_order'];
            $suggestioncategory->save();

            UpdateSuggestionCategory::dispatch($request, $suggestioncategory);

            return redirect()->back()->with('success', __('The category details are updated successfully.'));
        }
        else{
            return redirect()->route('suggestion-categories.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(SuggestionCategory $suggestioncategory)
    {
        if(Auth::user()->can('delete-suggestion-categories')){
            DestroySuggestionCategory::dispatch($suggestioncategory);
            $suggestioncategory->delete();

            return redirect()->back()->with('success', __('The category has been deleted.'));
        }
        else{
            return redirect()->route('suggestion-categories.index')->with('error', __('Permission denied'));
        }
    }




}