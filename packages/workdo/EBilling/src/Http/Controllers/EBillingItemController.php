<?php

namespace Workdo\EBilling\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\EBilling\Models\EBillingItem;
use Workdo\EBilling\Http\Requests\StoreEBillingItemRequest;
use Workdo\EBilling\Http\Requests\UpdateEBillingItemRequest;

class EBillingItemController extends Controller
{
    private function canAccessItem(EBillingItem $item): bool
    {
        if ($item->created_by !== creatorId()) {
            return false;
        }

        if (Auth::user()->can('manage-any-ebilling')) {
            return true;
        }

        if (Auth::user()->can('manage-own-ebilling')) {
            return (int) $item->creator_id === (int) Auth::id();
        }

        return false;
    }

    public function index()
    {
        if(Auth::user()->can('manage-ebilling')){
            $items = EBillingItem::select('id', 'name', 'description', 'is_active', 'created_at')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-ebilling')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-ebilling')) {
                        $q->where('created_by', creatorId())->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), fn($q) => $q->where('name', 'like', '%' . request('name') . '%'))
                ->when(request('is_active') !== null, fn($q) => $q->where('is_active', request('is_active')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('EBilling/Items/Index', [
                'items' => $items,
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }

    public function create()
    {
        if (!Auth::user()->can('create-ebilling')) {
            return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
        }

        return Inertia::render('EBilling/Items/Create');
    }

    public function store(StoreEBillingItemRequest $request)
    {
        if(Auth::user()->can('create-ebilling')){
            $validated = $request->validated();

            $validated['is_active'] = $request->boolean('is_active', true);

            $item = new EBillingItem();
            $item->name = $validated['name'];
            $item->description = $validated['description'];
            $item->is_active = $validated['is_active'];
            $item->creator_id = Auth::id();
            $item->created_by = creatorId();
            $item->save();

            return redirect()->route('ebilling.items.index')->with('success', __('Item created successfully.'));
        }
        return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
    }

    public function edit(EBillingItem $item)
    {
        if (!Auth::user()->can('edit-ebilling')) {
            return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
        }

        if (!$this->canAccessItem($item)) {
            return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
        }

        return Inertia::render('EBilling/Items/Edit', [
            'item' => $item->only(['id', 'name', 'description', 'is_active']),
        ]);
    }

    public function update(UpdateEBillingItemRequest $request, EBillingItem $item)
    {
        if(Auth::user()->can('edit-ebilling')){
            if (!$this->canAccessItem($item)) {
                return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            $validated['is_active'] = $request->boolean('is_active', true);

            $item->name = $validated['name'];
            $item->description = $validated['description'];
            $item->is_active = $validated['is_active'];
            $item->save();

            return back()->with('success', __('Item updated successfully.'));
        }
        return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
    }

    public function destroy(EBillingItem $item)
    {
        if(Auth::user()->can('delete-ebilling')){
            if (!$this->canAccessItem($item)) {
                return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
            }

            $item->delete();

            return back()->with('success', __('Item deleted successfully.'));
        }
        return redirect()->route('ebilling.items.index')->with('error', __('Permission denied'));
    }
}
