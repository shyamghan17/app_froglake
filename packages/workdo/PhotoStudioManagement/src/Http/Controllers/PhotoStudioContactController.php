<?php

namespace Workdo\PhotoStudioManagement\Http\Controllers;

use Workdo\PhotoStudioManagement\Models\PhotoStudioContact;
use Workdo\PhotoStudioManagement\Events\DestroyPhotoStudioContact;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PhotoStudioContactController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-photo-studio-contacts')) {
            $contacts = PhotoStudioContact::query()
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-photo-studio-contacts')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-photo-studio-contacts')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('first_name', 'like', '%' . request('search') . '%')
                            ->orWhere('last_name', 'like', '%' . request('search') . '%')
                            ->orWhere('email', 'like', '%' . request('search') . '%')
                            ->orWhere('phone_number', 'like', '%' . request('search') . '%');
                    });
                })
                ->when(request('date_range'), function ($q) {
                    $dateRange = request('date_range');
                    if (strpos($dateRange, ' - ') !== false) {
                        [$startDate, $endDate] = explode(' - ', $dateRange);
                        $q->whereBetween('received_date', [trim($startDate), trim($endDate)]);
                    }
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('PhotoStudioManagement/Contacts/Index', [
                'contacts' => $contacts,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(PhotoStudioContact $contact)
    {
        if (Auth::user()->can('delete-photo-studio-contacts')) {
            DestroyPhotoStudioContact::dispatch($contact);

            $contact->delete();
            return redirect()->back()->with('success', __('The contact has been deleted.'));
        } else {
            return redirect()->route('photo-studio-management.contacts.index')->with('error', __('Permission denied'));
        }
    }
}
