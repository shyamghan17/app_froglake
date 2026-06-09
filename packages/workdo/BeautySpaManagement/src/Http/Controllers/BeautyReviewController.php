<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\BeautyReview;
use Workdo\BeautySpaManagement\Models\BeautyService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\DestroyBeautyReview;

class BeautyReviewController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-beauty-reviews')){
            $beautyreviews = BeautyReview::query()
                ->where('created_by', creatorId())
                ->with('beautyService:id,name')
                ->when(request('name'), function($q) {
                    $q->where(function($query) {
                        $query->where('name', 'like', '%' . request('name') . '%')
                              ->orWhere('email', 'like', '%' . request('name') . '%');
                    });
                })
                ->when(request('rating'), fn($q) => $q->where('rating', request('rating')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Reviews/Index', [
                'beautyreviews'  => $beautyreviews,
                'beautyservices' => BeautyService::where('created_by', creatorId())->select('id', 'name')->get(),
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyReview $beautyreview)
    {
        if(Auth::user()->can('delete-beauty-reviews')){
            DestroyBeautyReview::dispatch($beautyreview);

            $beautyreview->delete();
            return redirect()->back()->with('success', __('The review has been deleted.'));
        }
        else{
            return redirect()->route('beauty-spa-management.beauty-reviews.index')->with('error', __('Permission denied'));
        }
    }
}