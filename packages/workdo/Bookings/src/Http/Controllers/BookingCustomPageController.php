<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingCustomPage;
use Workdo\Bookings\Http\Requests\StoreBookingCustomPageRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingCustomPageRequest;
use Illuminate\Support\Str;
use Workdo\Bookings\Events\CreateBookingCustomPage;
use Workdo\Bookings\Events\UpdateBookingCustomPage;
use Workdo\Bookings\Events\DestroyBookingCustomPage;

class BookingCustomPageController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-custom-pages')) {
            $query = BookingCustomPage::where(function($q) {
                    if(Auth::user()->can('manage-any-booking-custom-pages')) {
                        $q->where('created_by', creatorId())->orWhereNull('created_by');
                    } elseif(Auth::user()->can('manage-own-booking-custom-pages')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function($q) {
                    $search = request('search');
                    $q->where(function($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                              ->orWhere('page_header', 'like', "%{$search}%")
                              ->orWhere('content', 'like', "%{$search}%");
                    });
                })
                ->when((request()->has('status') && request()->has('status') != ''), function($q) {
                    $q->where('is_active', request('status'));
                })
                ->when(request('sort'), function($q) {
                    $sortField = request('sort');
                    $sortDirection = in_array(request('direction'), ['asc', 'desc']) ? request('direction') : 'asc';
                    $q->orderBy($sortField, $sortDirection);
                }, function($q) {
                    $q->orderBy('created_at', 'desc');
                });
                
            $pages = $query->paginate(request('per_page', 10));
                
            return Inertia::render('Bookings/SystemSetup/CustomPages/Index', [
                'pages' => $pages
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingCustomPageRequest $request)
    {
        if (Auth::user()->can('create-booking-custom-pages')) {
            $data = $request->validated();
            $data['slug'] = $request->slug;
            $data['created_by'] = creatorId();
            $data['creator_id'] = Auth::id();
            
            $page = BookingCustomPage::create($data);
            
            CreateBookingCustomPage::dispatch($request, $page);
            return back()->with('success', __('The custom page has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function show(BookingCustomPage $page)
    {
        if (Auth::user()->can('manage-booking-custom-pages') && ($page->created_by == creatorId() || is_null($page->created_by))) {
            return response()->json($page);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function update(UpdateBookingCustomPageRequest $request, BookingCustomPage $page)
    {
        if (Auth::user()->can('edit-booking-custom-pages') && ($page->created_by == creatorId() || is_null($page->created_by))) {
            $data = $request->validated();
            $data['slug'] = $request->slug;
            
            $page->update($data);

            UpdateBookingCustomPage::dispatch($request, $page);
            return back()->with('success', __('The custom page has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingCustomPage $page)
    {
        if (Auth::user()->can('delete-booking-custom-pages') && ($page->created_by == creatorId() || is_null($page->created_by))) {
            DestroyBookingCustomPage::dispatch($page);

            $page->delete();

            return back()->with('success', __('The custom page has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function getActivePages(Request $request)
    {
        $userSlug = $request->route('userSlug');
        $userId = null;
        
        if ($userSlug) {
            $user = \App\Models\User::where('slug', $userSlug)->first();
            if ($user) {
                $userId = $user->id;
            }
        }
        
        $pages = BookingCustomPage::where('is_active', true)
            ->when($userId, function($q) use ($userId) {
                $q->where('created_by', $userId);
            })
            ->select('id', 'title', 'slug', 'is_active')
            ->orderBy('title')
            ->get();
            
        return response()->json($pages);
    }

    private function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (BookingCustomPage::where('slug', $slug)
            ->when($excludeId, function($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}