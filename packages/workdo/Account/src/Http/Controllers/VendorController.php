<?php

namespace Workdo\Account\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Account\Models\Vendor;
use Workdo\Account\Http\Requests\StoreVendorRequest;
use Workdo\Account\Http\Requests\UpdateVendorRequest;
use Workdo\Account\Events\CreateVendor;
use Workdo\Account\Events\UpdateVendor;
use Workdo\Account\Events\DestroyVendor;
use Illuminate\Support\Facades\DB;
use Workdo\Account\Services\AccountPartyUserProvisioningService;
use Workdo\Account\Services\UserAccountPartySyncService;

class VendorController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-vendors')){
            app(UserAccountPartySyncService::class)->syncVendorsForTenant(creatorId());

            $vendors = Vendor::query()
                ->with('user:id,name,avatar,is_disable')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-vendors')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-vendors')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('company_name'), fn($q) => $q->where('company_name', 'like', '%' . request('company_name') . '%'))
                ->when(request('vendor_code'), fn($q) => $q->where('vendor_code', 'like', '%' . request('vendor_code') . '%'))
                ->when(request('tax_number'), fn($q) => $q->where('tax_number', 'like', '%' . request('tax_number') . '%'))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Account/Vendors/Index', [
                'vendors' => $vendors,
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }



    public function store(StoreVendorRequest $request)
    {
        if(Auth::user()->can('create-vendors')){
            $validated = $request->validated();

            $vendor = DB::transaction(function () use ($validated) {
                if (empty($validated['user_id'])) {
                    $user = app(AccountPartyUserProvisioningService::class)->createLoginDisabledUser(
                        'vendor',
                        $validated['company_name'],
                        $validated['contact_person_email'] ?? null,
                        $validated['contact_person_mobile'] ?? null
                    );

                    $validated['user_id'] = $user->id;

                    if (empty($validated['contact_person_email'])) {
                        $validated['contact_person_email'] = $user->email;
                    }
                }

                $vendor = new Vendor();
                $vendor->user_id = $validated['user_id'] ?? null;
                $vendor->company_name = $validated['company_name'];
                $vendor->contact_person_name = $validated['contact_person_name'];
                $vendor->contact_person_email = $validated['contact_person_email'] ?? null;
                $vendor->contact_person_mobile = $validated['contact_person_mobile'] ?? null;
                $vendor->tax_number = $validated['tax_number'] ?? null;
                $vendor->payment_terms = $validated['payment_terms'] ?? null;
                $vendor->billing_address = $validated['billing_address'];
                $vendor->shipping_address = $validated['same_as_billing'] ? $validated['billing_address'] : $validated['shipping_address'];
                $vendor->same_as_billing = $validated['same_as_billing'] ?? false;
                $vendor->notes = $validated['notes'] ?? null;
                $vendor->creator_id = Auth::id();
                $vendor->created_by = creatorId();
                $vendor->save();

                return $vendor;
            });

            CreateVendor::dispatch($request, $vendor);

            return redirect()->route('account.vendors.index')->with('success', __('The vendor has been created successfully.'));
        }
        return redirect()->route('account.vendors.index')->with('error', __('Permission denied'));
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        if(Auth::user()->can('edit-vendors')){
            $validated = $request->validated();

            $vendor->company_name = $validated['company_name'];
            $vendor->contact_person_name = $validated['contact_person_name'];
            $vendor->contact_person_email = $validated['contact_person_email'] ?? null;
            $vendor->contact_person_mobile = $validated['contact_person_mobile'] ?? null;
            $vendor->tax_number = $validated['tax_number'] ?? null;
            $vendor->payment_terms = $validated['payment_terms'] ?? null;
            $vendor->billing_address = $validated['billing_address'];
            $vendor->shipping_address = $validated['same_as_billing'] ? $validated['billing_address'] : $validated['shipping_address'];
            $vendor->same_as_billing = $validated['same_as_billing'] ?? false;
            $vendor->notes = $validated['notes'] ?? null;
            $vendor->save();

            UpdateVendor::dispatch($request, $vendor);

            return back()->with('success', __('The vendor details are updated successfully.'));
        }
        return back()->with('error', __('Permission denied'));
    }

    public function destroy(Vendor $vendor)
    {
        if(Auth::user()->can('delete-vendors')){
            DestroyVendor::dispatch($vendor);
            $vendor->delete();
            return back()->with('success', __('The vendor has been deleted.'));
        }
        return back()->with('error', __('Permission denied'));
    }
}
