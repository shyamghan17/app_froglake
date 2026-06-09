<?php

namespace Workdo\Portfolio\Http\Controllers;

use Workdo\Portfolio\Models\Portfolio;
use Workdo\Portfolio\Models\PortfolioCategory;
use Workdo\Portfolio\Models\PortfolioCustomSection;

use Workdo\Portfolio\Http\Requests\StorePortfolioRequest;
use Workdo\Portfolio\Http\Requests\UpdatePortfolioRequest;

use Workdo\Portfolio\Events\CreatePortfolio;
use Workdo\Portfolio\Events\UpdatePortfolio;
use Workdo\Portfolio\Events\DestroyPortfolio;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-portfolios')) {
            $portfolios = Portfolio::query()
                ->with(['portfolio_category'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-portfolios')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-portfolios')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('title'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('title', 'like', '%' . request('title') . '%')
                            ->orWhere('name', 'like', '%' . request('title') . '%')
                            ->orWhere('role', 'like', '%' . request('title') . '%')
                            ->orWhere('experience_years', 'like', '%' . request('title') . '%')
                            ->orWhere('client', 'like', '%' . request('title') . '%');
                    });
                })
                ->when(request('category_id') && request('category_id') !== 'all', fn($q) => $q->where('category_id', request('category_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Portfolio/Portfolios/Index', [
                'portfolios'          => $portfolios,
                'portfoliocategories' => PortfolioCategory::where('created_by', creatorId())->where('is_active', 1)->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create-portfolios')) {
            return Inertia::render('Portfolio/Portfolios/Create', [
                'portfoliocategories' => PortfolioCategory::where('created_by', creatorId())->where('is_active', 1)->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StorePortfolioRequest $request)
    {
        if (Auth::user()->can('create-portfolios')) {
            $validated = $request->validated();

            if (isset($validated['photo']) && $validated['photo']) {
                $validated['photo'] = basename($validated['photo']);
            }

            if (isset($validated['images']) && is_array($validated['images'])) {
                $validated['images'] = array_map('basename', $validated['images']);
            }

            $portfolio = new Portfolio();

            // Personal Information fields
            $portfolio->name             = $validated['name'] ?? null;
            $portfolio->email            = $validated['email'] ?? null;
            $portfolio->role             = $validated['role'] ?? null;
            $portfolio->experience_years = $validated['experience_years'] ?? null;
            $portfolio->photo            = $validated['photo'] ?? null;
            $portfolio->education        = $validated['education'] ?? null;

            // Work Details fields
            $portfolio->title          = $validated['title'];
            $portfolio->description    = $validated['description'] ?? null;
            $portfolio->category_id    = $validated['category_id'] ?? null;
            $portfolio->live_url       = $validated['live_url'] ?? null;
            $portfolio->repository_url = $validated['repository_url'] ?? null;
            $portfolio->skills         = $validated['skills'] ?? null;
            $portfolio->client         = $validated['client'] ?? null;
            $portfolio->duration       = $validated['duration'] ?? null;
            $portfolio->team_size      = $validated['team_size'] ?? null;
            $portfolio->start_date     = $validated['start_date'] ?? null;
            $portfolio->end_date       = $validated['end_date'] ?? null;
            $portfolio->budget         = $validated['budget'] ?? null;
            $portfolio->industry       = $validated['industry'] ?? null;

            // Overview fields
            $portfolio->show_overview = $request->boolean('show_overview', true);
            $portfolio->overview      = $validated['overview'] ?? null;

            // Gallery fields
            $portfolio->show_gallery = $request->boolean('show_gallery', true);
            $portfolio->images       = $validated['images'] ?? [];
            $portfolio->video_link   = $validated['video_link'] ?? null;

            // Contact fields
            $portfolio->show_contact    = $request->boolean('show_contact', true);
            $portfolio->contact_heading = $validated['contact_heading'] ?? null;
            $portfolio->contact_message = $validated['contact_message'] ?? null;

            // System fields
            $portfolio->creator_id = Auth::id();
            $portfolio->created_by = creatorId();
            $portfolio->save();

            CreatePortfolio::dispatch($request, $portfolio);

            // Handle custom sections
            $customSections = $request->input('custom_sections', []);

            if (!empty($customSections) && is_array($customSections)) {
                foreach ($customSections as $index => $section) {
                    if (
                        isset($section['title']) && isset($section['content']) &&
                        !empty(trim($section['title'])) && !empty(trim($section['content']))
                    ) {
                        PortfolioCustomSection::create([
                            'portfolio_id' => $portfolio->id,
                            'title'        => trim($section['title']),
                            'content'      => trim($section['content']),
                            'sort_order'   => $index
                        ]);
                    }
                }
            }

            return redirect()->route('portfolio.portfolios.index')->with('success', __('The portfolio has been created successfully.'));
        } else {
            return redirect()->route('portfolio.portfolios.index')->with('error', __('Permission denied'));
        }
    }

    public function edit(Portfolio $portfolio)
    {
        if (Auth::user()->can('edit-portfolios')) {

            $portfolioQuery = Portfolio::where('id', $portfolio->id)
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-portfolios')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-portfolios')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                });

            if (!$portfolioQuery->exists()) {
                return back()->with('error', __('Permission denied'));
            }

            $portfolio->load([
                'custom_sections' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ]);

            return Inertia::render('Portfolio/Portfolios/Edit', [
                'portfolio'           => $portfolio,
                'portfoliocategories' => PortfolioCategory::where('created_by', creatorId())->where('is_active', 1)->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio)
    {
        if (Auth::user()->can('edit-portfolios')) {
            $validated = $request->validated();

            if (isset($validated['photo']) && $validated['photo']) {
                $validated['photo'] = basename($validated['photo']);
            }

            if (isset($validated['images']) && is_array($validated['images'])) {
                $validated['images'] = array_map('basename', $validated['images']);
            }

            // Personal Information fields
            $portfolio->photo            = $validated['photo'] ?? null;
            $portfolio->name             = $validated['name'] ?? null;
            $portfolio->role             = $validated['role'] ?? null;
            $portfolio->experience_years = $validated['experience_years'] ?? null;
            $portfolio->email            = $validated['email'] ?? null;
            $portfolio->education        = $validated['education'] ?? null;

            // Work Details fields
            $portfolio->title          = $validated['title'];
            $portfolio->description    = $validated['description'] ?? null;
            $portfolio->category_id    = $validated['category_id'] ?? null;
            $portfolio->live_url       = $validated['live_url'] ?? null;
            $portfolio->repository_url = $validated['repository_url'] ?? null;
            $portfolio->skills         = $validated['skills'] ?? null;
            $portfolio->client         = $validated['client'] ?? null;
            $portfolio->duration       = $validated['duration'] ?? null;
            $portfolio->team_size      = $validated['team_size'] ?? null;
            $portfolio->start_date     = $validated['start_date'] ?? null;
            $portfolio->end_date       = $validated['end_date'] ?? null;
            $portfolio->budget         = $validated['budget'] ?? null;
            $portfolio->industry       = $validated['industry'] ?? null;

            // Overview fields
            $portfolio->show_overview = $request->boolean('show_overview', true);
            $portfolio->overview      = $validated['overview'] ?? null;

            // Gallery fields
            $portfolio->images       = $validated['images'] ?? [];
            $portfolio->video_link   = $validated['video_link'] ?? null;
            $portfolio->show_gallery = $request->boolean('show_gallery', true);

            // Contact fields
            $portfolio->contact_heading = $validated['contact_heading'] ?? null;
            $portfolio->contact_message = $validated['contact_message'] ?? null;
            $portfolio->show_contact    = $request->boolean('show_contact', true);

            $portfolio->save();

            UpdatePortfolio::dispatch($request, $portfolio);

            // Handle custom sections
            $customSections = $request->input('custom_sections', []);
            // Delete existing sections first
            $portfolio->custom_sections()->delete();

            if (!empty($customSections) && is_array($customSections)) {
                foreach ($customSections as $index => $section) {
                    if (
                        isset($section['title']) && isset($section['content']) &&
                        !empty(trim($section['title'])) && !empty(trim($section['content']))
                    ) {
                        PortfolioCustomSection::create([
                            'portfolio_id' => $portfolio->id,
                            'title'        => trim($section['title']),
                            'content'      => trim($section['content']),
                            'sort_order'   => $index
                        ]);
                    }
                }
            }

            return redirect()->route('portfolio.portfolios.index')->with('success', __('The portfolio details are updated successfully.'));
        } else {
            return redirect()->route('portfolio.portfolios.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(Portfolio $portfolio)
    {
        if (Auth::user()->can('delete-portfolios')) {
            DestroyPortfolio::dispatch($portfolio);

            $portfolio->custom_sections()->delete();

            $portfolio->delete();

            return redirect()->back()->with('success', __('The portfolio has been deleted.'));
        } else {
            return redirect()->route('portfolio.portfolios.index')->with('error', __('Permission denied'));
        }
    }
}
