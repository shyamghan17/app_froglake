<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Models\GiftCard;
use Workdo\BeautySpaManagement\Http\Requests\StoreGiftCardRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateGiftCardRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyGiftCard;
use Workdo\BeautySpaManagement\Events\DestroyBeautyGiftCard;
use Workdo\BeautySpaManagement\Events\UpdateBeautyGiftCard;
use Workdo\BeautySpaManagement\Models\BeautyGiftCard;

class BeautyGiftCardController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-beauty-gift-cards')) {
            $giftcards = BeautyGiftCard::query()

                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-beauty-gift-cards')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-beauty-gift-cards')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('card_code'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('card_code', 'like', '%' . request('card_code') . '%');
                    });
                })
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status') === '1' ? 1 : 0))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/GiftCards/Index', [
                'giftcards' => $giftcards,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreGiftCardRequest $request)
    {
        if (Auth::user()->can('create-beauty-gift-cards')) {
            $validated = $request->validated();



            $giftcard              = new BeautyGiftCard();
            $giftcard->card_code   = $validated['card_code'];
            $giftcard->customer    = $validated['customer'];
            $giftcard->balance     = $validated['balance'];
            $giftcard->expiry_date = $validated['expiry_date'];
            $giftcard->status      = $validated['status'];

            $giftcard->creator_id = Auth::id();
            $giftcard->created_by = creatorId();
            $giftcard->save();

            CreateBeautyGiftCard::dispatch($request, $giftcard);

            return redirect()->route('beauty-spa-management.gift-cards.index')->with('success', __('The gift card has been created successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.gift-cards.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateGiftCardRequest $request, BeautyGiftCard $giftcard)
    {
        if (Auth::user()->can('edit-beauty-gift-cards')) {
            $validated = $request->validated();

            $giftcard->card_code   = $validated['card_code'];
            $giftcard->customer    = $validated['customer'];
            $giftcard->balance     = $validated['balance'];
            $giftcard->expiry_date = $validated['expiry_date'];
            $giftcard->status      = $validated['status'];

            $giftcard->save();
            UpdateBeautyGiftCard::dispatch($request, $giftcard);

            return redirect()->back()->with('success', __('The gift card details are updated successfully.'));
        } else {
            return redirect()->route('beauty-spa-management.gift-cards.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyGiftCard $giftcard)
    {
        if (Auth::user()->can('delete-beauty-gift-cards')) {
            DestroyBeautyGiftCard::dispatch($giftcard);
            $giftcard->delete();

            return redirect()->back()->with('success', __('The gift card has been deleted.'));
        } else {
            return redirect()->route('beauty-spa-management.gift-cards.index')->with('error', __('Permission denied'));
        }
    }
}
