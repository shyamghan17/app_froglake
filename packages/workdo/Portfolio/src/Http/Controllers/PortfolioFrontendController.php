<?php

namespace Workdo\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Workdo\Portfolio\Models\Portfolio;

class PortfolioFrontendController extends Controller
{
    public function show($slug)
    {
        $portfolio = Portfolio::with([
            'custom_sections' => function ($query) {
                $query->orderBy('sort_order');
            },
            'portfolio_category:id,name'
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Add category_name to the portfolio object
        $portfolio->category_name = $portfolio->portfolio_category?->name;

        $companyAllSetting = getCompanyAllSetting($portfolio->created_by);

        return Inertia::render('Portfolio/Frontend/PortfolioShow', [
            'portfolio'         => $portfolio,
            'companyAllSetting' => $companyAllSetting
        ]);
    }
}
