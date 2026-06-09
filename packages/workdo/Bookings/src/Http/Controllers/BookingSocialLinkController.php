<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingSocialLink;
use Workdo\Bookings\Http\Requests\StoreBookingSocialLinkRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingSocialLinkRequest;
use Workdo\Bookings\Events\CreateBookingSocialLink;
use Workdo\Bookings\Events\UpdateBookingSocialLink;
use Workdo\Bookings\Events\DestroyBookingSocialLink;

class BookingSocialLinkController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-booking-social-links')) {
            $socialLinks = BookingSocialLink::where(function($q) {
                    if(Auth::user()->can('manage-any-booking-social-links')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-booking-social-links')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get()
                ->map(function($link) {
                    return [
                        'icon' => $link->icon ?? '',
                        'name' => $link->name,
                        'url' => $link->link
                    ];
                });
            
            return Inertia::render('Bookings/SystemSetup/SocialLinks/Index', [
                'socialLinks' => $socialLinks
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create-booking-social-links')) {
            $request->validate([
                'social_links'        => 'nullable|array',
                'social_links.*.url'  => 'required|url',
                'social_links.*.icon' => 'required|string'
            ]);

            BookingSocialLink::where('created_by', creatorId())->delete();

            foreach ($request->social_links as $link) {
                $sociallink = new BookingSocialLink();
                $sociallink->name = $link['name'] ?? '';
                $sociallink->icon = $link['icon'] ?? '';
                $sociallink->link = $link['url'] ?? '';
                $sociallink->creator_id = Auth::id();
                $sociallink->created_by = creatorId();
                $sociallink->save();
            }

            return back()->with('success', __('Social links saved successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingSocialLinkRequest $request, BookingSocialLink $socialLink)
    {
        if (Auth::user()->can('edit-booking-social-links') && ($socialLink->created_by == creatorId() || is_null($socialLink->created_by))) {
            $socialLink->update($request->validated());

            UpdateBookingSocialLink::dispatch($request, $socialLink);
            return back()->with('success', __('The social link details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BookingSocialLink $socialLink)
    {
        if (Auth::user()->can('delete-booking-social-links') && ($socialLink->created_by == creatorId() || is_null($socialLink->created_by))) {
            DestroyBookingSocialLink::dispatch($socialLink);
            
            $socialLink->delete();

            return back()->with('success', __('The social link has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}