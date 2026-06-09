<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\DestroyBeautyContact;
use Workdo\BeautySpaManagement\Models\BeautyContact;

class BeautyContactController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-beauty-contacts')){
            $beautycontacts = BeautyContact::query()
                ->where('created_by', creatorId())
                ->when(request('name'), function($q) {
                    $q->where('name', 'like', '%' . request('name') . '%');
                })
                ->when(request('email'), function($q) {
                    $q->where('email', 'like', '%' . request('email') . '%');
                })
                ->when(request('subject'), function($q) {
                    $q->where('subject', 'like', '%' . request('subject') . '%');
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Contacts/Index', [
                'beautycontacts' => $beautycontacts,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyContact $beautycontact)
    {
        if(Auth::user()->can('delete-beauty-contacts')){
            DestroyBeautyContact::dispatch($beautycontact);
           
            $beautycontact->delete();

            return redirect()->back()->with('success', __('The contact has been deleted.'));
        }
        else{
            return redirect()->route('beauty-spa-management.beauty-contacts.index')->with('error', __('Permission denied'));
        }
    }
}