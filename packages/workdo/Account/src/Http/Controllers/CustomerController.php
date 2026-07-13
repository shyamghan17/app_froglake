<?php

namespace Workdo\Account\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Account\Models\Customer;
use Workdo\Account\Http\Requests\StoreCustomerRequest;
use Workdo\Account\Http\Requests\UpdateCustomerRequest;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Account\Events\UpdateCustomer;
use Workdo\Account\Events\DestroyCustomer;
use Illuminate\Support\Facades\DB;
use Workdo\Account\Services\AccountPartyUserProvisioningService;
use Workdo\Account\Services\UserAccountPartySyncService;

class CustomerController extends Controller
{
    private function sanitizedSort(): array
    {
        $allowedSorts = ['customer_code', 'company_name', 'contact_person_name', 'created_at'];
        $sort = request('sort');
        $direction = request('direction', 'asc');

        return [
            'sort' => in_array($sort, $allowedSorts, true) ? $sort : null,
            'direction' => in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc',
        ];
    }

    private function canAccessCustomer(Customer $customer): bool
    {
        if ((int) $customer->created_by !== (int) creatorId()) {
            return false;
        }

        if (Auth::user()->can('manage-any-customers')) {
            return true;
        }

        return Auth::user()->can('manage-own-customers') && (int) $customer->creator_id === (int) Auth::id();
    }

    public function index()
    {
        if(Auth::user()->can('manage-customers')){
            app(UserAccountPartySyncService::class)->syncCustomersForTenant(creatorId());
            $sort = $this->sanitizedSort();

            $customers = Customer::query()
                ->with('user:id,name,avatar,is_disable')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-customers')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-customers')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('company_name'), fn($q) => $q->where('company_name', 'like', '%' . request('company_name') . '%'))
                ->when(request('customer_code'), fn($q) => $q->where('customer_code', 'like', '%' . request('customer_code') . '%'))
                ->when(request('tax_number'), fn($q) => $q->where('tax_number', 'like', '%' . request('tax_number') . '%'))
                ->when($sort['sort'], fn($q) => $q->orderBy($sort['sort'], $sort['direction']), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Account/Customers/Index', [
                'customers' => $customers,
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }

    public function store(StoreCustomerRequest $request)
    {
        if(Auth::user()->can('create-customers')){
            $validated = $request->validated();

            $customer = DB::transaction(function () use ($validated) {
                if (empty($validated['user_id'])) {
                    $user = app(AccountPartyUserProvisioningService::class)->createLoginDisabledUser(
                        'client',
                        $validated['company_name'],
                        $validated['contact_person_email'] ?? null,
                        $validated['contact_person_mobile'] ?? null
                    );

                    $validated['user_id'] = $user->id;

                    if (empty($validated['contact_person_email'])) {
                        $validated['contact_person_email'] = $user->email;
                    }
                }

                $customer = new Customer();
                $customer->user_id = $validated['user_id'] ?? null;
                $customer->company_name = $validated['company_name'];
                $customer->contact_person_name = $validated['contact_person_name'];
                $customer->contact_person_email = $validated['contact_person_email'] ?? null;
                $customer->contact_person_mobile = $validated['contact_person_mobile'] ?? null;
                $customer->tax_number = $validated['tax_number'] ?? null;
                $customer->payment_terms = $validated['payment_terms'] ?? null;
                $customer->billing_address = $validated['billing_address'];
                $customer->shipping_address = $validated['same_as_billing'] ? $validated['billing_address'] : $validated['shipping_address'];
                $customer->same_as_billing = $validated['same_as_billing'] ?? false;
                $customer->notes = $validated['notes'] ?? null;
                $customer->creator_id = Auth::id();
                $customer->created_by = creatorId();
                $customer->save();

                return $customer;
            });

            CreateCustomer::dispatch($request, $customer);

            return redirect()->route('account.customers.index')->with('success', __('The customer has been created successfully.'));
        }
        return redirect()->route('account.customers.index')->with('error', __('Permission denied'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        if(Auth::user()->can('edit-customers')){
            if (!$this->canAccessCustomer($customer)) {
                return back()->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            $customer->company_name = $validated['company_name'];
            $customer->contact_person_name = $validated['contact_person_name'];
            $customer->contact_person_email = $validated['contact_person_email'] ?? null;
            $customer->contact_person_mobile = $validated['contact_person_mobile'] ?? null;
            $customer->tax_number = $validated['tax_number'] ?? null;
            $customer->payment_terms = $validated['payment_terms'] ?? null;
            $customer->billing_address = $validated['billing_address'];
            $customer->shipping_address = $validated['same_as_billing'] ? $validated['billing_address'] : ($validated['shipping_address'] ?? null);
            $customer->same_as_billing = $validated['same_as_billing'] ?? false;
            $customer->notes = $validated['notes'] ?? null;
            $customer->save();

            UpdateCustomer::dispatch($request, $customer);

            return back()->with('success', __('The customer details are updated successfully.'));
        }
        return back()->with('error', __('Permission denied'));
    }

    public function destroy(Customer $customer)
    {
        if(Auth::user()->can('delete-customers')){
            if (!$this->canAccessCustomer($customer)) {
                return back()->with('error', __('Permission denied'));
            }

            DestroyCustomer::dispatch($customer);
            $customer->delete();
            return back()->with('success', __('The customer has been deleted.'));
        }
        return back()->with('error', __('Permission denied'));
    }
}
