<?php

namespace Workdo\Portfolio\Http\Controllers;

use Workdo\Portfolio\Models\PortfolioCategory;
use Workdo\Portfolio\Http\Requests\StorePortfolioCategoryRequest;
use Workdo\Portfolio\Http\Requests\UpdatePortfolioCategoryRequest;

use Workdo\Portfolio\Events\CreatePortfolioCategory;
use Workdo\Portfolio\Events\UpdatePortfolioCategory;
use Workdo\Portfolio\Events\DestroyPortfolioCategory;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PortfolioCategoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-portfolio-categories')) {
            $portfoliocategories = PortfolioCategory::query()
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-portfolio-categories')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-portfolio-categories')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'like', '%' . request('name') . '%');
                    });
                })
                ->when(request('is_active') !== null && request('is_active') !== '', fn($q) => $q->where('is_active', request('is_active') === '1' ? 1 : 0))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Portfolio/PortfolioCategories/Index', [
                'portfoliocategories' => $portfoliocategories,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StorePortfolioCategoryRequest $request)
    {
        if (Auth::user()->can('create-portfolio-categories')) {
            $validated              = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', true);

            $portfoliocategory              = new PortfolioCategory();
            $portfoliocategory->name        = $validated['name'];
            $portfoliocategory->description = $validated['description'];
            $portfoliocategory->is_active   = $validated['is_active'];
            $portfoliocategory->creator_id  = Auth::id();
            $portfoliocategory->created_by  = creatorId();
            $portfoliocategory->save();

            CreatePortfolioCategory::dispatch($request, $portfoliocategory);

            return redirect()->route('portfolio.categories.index')->with('success', __('The category has been created successfully.'));
        } else {
            return redirect()->route('portfolio.categories.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdatePortfolioCategoryRequest $request, PortfolioCategory $portfoliocategory)
    {
        if (Auth::user()->can('edit-portfolio-categories')) {
            $validated              = $request->validated();
            $validated['is_active'] = $request->boolean('is_active', true);

            $portfoliocategory->name        = $validated['name'];
            $portfoliocategory->description = $validated['description'];
            $portfoliocategory->is_active   = $validated['is_active'];
            $portfoliocategory->save();

            UpdatePortfolioCategory::dispatch($request, $portfoliocategory);

            return redirect()->back()->with('success', __('The category details are updated successfully.'));
        } else {
            return redirect()->route('portfolio.categories.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(PortfolioCategory $portfoliocategory)
    {
        if (Auth::user()->can('delete-portfolio-categories')) {
            DestroyPortfolioCategory::dispatch($portfoliocategory);

            $portfoliocategory->delete();

            return redirect()->back()->with('success', __('The category has been deleted.'));
        } else {
            return redirect()->route('portfolio.categories.index')->with('error', __('Permission denied'));
        }
    }
}
