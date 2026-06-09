<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Workdo\PhotoStudioManagement\Models\PhotoStudioSubscriber;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioSubscriber;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PhotoStudioSubscriberController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-subscribers')) {
            $subscribers = PhotoStudioSubscriber::query()
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-photo-studio-subscribers')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-photo-studio-subscribers')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('email'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('email', 'like', '%' . request('email') . '%');
                    });
                })
                ->when(request('date_range'), function ($q) {
                    $dateRange = request('date_range');
                    if (strpos($dateRange, ' - ') !== false) {
                        [$startDate, $endDate] = explode(' - ', $dateRange);
                        $q->whereBetween('created_at', [trim($startDate), trim($endDate)]);
                    }
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('PhotoStudioManagement/Subscribers/Index', [
                'subscribers' => $subscribers,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(PhotoStudioSubscriber $subscriber)
    {
        if (Auth::user()->can('delete-photo-studio-subscribers')) {
            DestroyPhotoStudioSubscriber::dispatch($subscriber);

            $subscriber->delete();

            return redirect()->back()->with('success', __('The subscriber has been deleted.'));
        } else {
            return redirect()->route('photo-studio-management.subscribers.index')->with('error', __('Permission denied'));
        }
    }
}
