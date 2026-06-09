<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySubscriber;
use Workdo\BeautySpaManagement\Events\DestroyBeautySubscriber;

class BeautySubscriberController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-beauty-subscribers')){
            $beautysubscribers = BeautySubscriber::query()
                ->where('created_by', creatorId())
                ->when(request('email'), function($q) {
                    $q->where('email', 'like', '%' . request('email') . '%');
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Subscribers/Index', [
                'beautysubscribers' => $beautysubscribers,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautySubscriber $beautysubscriber)
    {
        if(Auth::user()->can('delete-beauty-subscribers')){
            DestroyBeautySubscriber::dispatch($beautysubscriber);
            
            $beautysubscriber->delete();

            return redirect()->back()->with('success', __('The subscriber has been deleted.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}