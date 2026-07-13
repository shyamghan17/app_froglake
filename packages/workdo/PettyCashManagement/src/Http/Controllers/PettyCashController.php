<?php

namespace Workdo\PettyCashManagement\Http\Controllers;

use Workdo\PettyCashManagement\Models\PettyCash;
use Workdo\PettyCashManagement\Http\Requests\StorePettyCashRequest;
use Workdo\PettyCashManagement\Http\Requests\UpdatePettyCashRequest;
use Workdo\PettyCashManagement\Events\CreatePettyCash;
use Workdo\PettyCashManagement\Events\UpdatePettyCash;
use Workdo\PettyCashManagement\Events\DestroyPettyCash;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PettyCashManagement\Services\PettyCashApprovalService;

class PettyCashController extends Controller
{
    private function sanitizedSort(): array
    {
        $allowedSorts = ['pettycash_number', 'date', 'opening_balance', 'added_amount', 'total_balance', 'total_expense', 'closing_balance', 'status', 'created_at'];
        $sort = request('sort');
        $direction = request('direction', 'asc');

        return [
            'sort' => in_array($sort, $allowedSorts, true) ? $sort : null,
            'direction' => in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc',
        ];
    }

    private function latestTenantFundId(): ?int
    {
        return PettyCash::query()
            ->where('created_by', creatorId())
            ->latest('id')
            ->value('id');
    }

    public function index()
    {
        if(Auth::user()->can('manage-petty-cashes')){
            $sort = $this->sanitizedSort();
            $pettycashes = PettyCash::query()
                ->with(['expenses.request.user', 'expenses.request.category', 'expenses.reimbursement.user', 'expenses.reimbursement.category', 'expenses.approver'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-petty-cashes')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-petty-cashes')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('pettycash_number'), function($q) {
                    $q->where('pettycash_number', 'like', '%' . request('pettycash_number') . '%');
                })
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))

                ->when($sort['sort'], fn($q) => $q->orderBy($sort['sort'], $sort['direction']), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $latestEntryId = $this->latestTenantFundId();

            return Inertia::render('PettyCashManagement/PettyCashes/Index', [
                'pettycashes' => $pettycashes,
                'latestEntryId' => $latestEntryId,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StorePettyCashRequest $request)
    {
        if(Auth::user()->can('create-petty-cashes')){
            $validated    = $request->validated();
            $existingFund = PettyCash::where('date', $validated['date'])
                ->where('created_by', creatorId())
                ->first();

            if ($existingFund) {
                return back()->withErrors(['date' => __('Petty cash fund already exists for this date.')]);
            }

            $unapprovedEntries = PettyCash::where('created_by', creatorId())
                ->where('status', 0)
                ->exists();

            if ($unapprovedEntries) {
                return redirect()->route('petty-cash-management.petty-cashes.index')->with('error', __('Please approve all previous petty cash entries before creating a new one.'));
            }

            $previousEntry  = PettyCash::query()->where('created_by', creatorId())->latest('id')->first();
            $addedAmount    = $validated['added_amount'] ?? 0;

            $openingBalance = $previousEntry ? $previousEntry->closing_balance : 0;
            $closingBalance = $openingBalance + $addedAmount;
            $totalBalance   = $closingBalance;

            $pettycash                  = new PettyCash();
            $pettycash->date            = $validated['date'];
            $pettycash->opening_balance = $openingBalance;
            $pettycash->added_amount    = $addedAmount;
            $pettycash->total_balance   = $totalBalance;
            $pettycash->total_expense   = 0;
            $pettycash->closing_balance = $closingBalance;
            $pettycash->remarks         = $validated['remarks'];
            $pettycash->bank_account_id = $validated['bank_account_id'] ?? null;
            $pettycash->status          = 0;
            $pettycash->creator_id      = Auth::id();
            $pettycash->created_by      = creatorId();
            $pettycash->save();

            CreatePettyCash::dispatch($request, $pettycash);

            return redirect()->route('petty-cash-management.petty-cashes.index')->with('success', __('Petty cash fund created successfully.'));
        }
        return redirect()->route('petty-cash-management.petty-cashes.index')->with('error', __('Permission denied'));
    }

    public function update(UpdatePettyCashRequest $request, PettyCash $pettycash)
    {
        if(Auth::user()->can('edit-petty-cashes')){
            if ((int) $pettycash->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            if ((string) $pettycash->status === '1') {
                return redirect()->back()->with('error', __('Approved petty cash funds cannot be edited.'));
            }

            if ((int) $pettycash->id !== (int) $this->latestTenantFundId()) {
                return redirect()->back()->with('error', __('Only the latest petty cash fund can be edited.'));
            }

            $validated = $request->validated();

            if ($validated['date'] !== $pettycash->date) {
                $existingFund = PettyCash::where('date', $validated['date'])
                    ->where('created_by', creatorId())
                    ->where('id', '!=', $pettycash->id)
                    ->first();

                if ($existingFund) {
                    return back()->withErrors(['date' => __('Petty cash fund already exists for this date.')]);
                }
            }

            $addedAmount    = $validated['added_amount'] ?? 0;
            $openingBalance = $pettycash->opening_balance;
            $totalBalance   = $openingBalance + $addedAmount;
            $closingBalance = $totalBalance - $pettycash->total_expense;

            $pettycash->date            = $validated['date'];
            $pettycash->opening_balance = $openingBalance;
            $pettycash->added_amount    = $addedAmount;
            $pettycash->total_balance   = $totalBalance;
            $pettycash->closing_balance = $closingBalance;
            $pettycash->remarks         = $validated['remarks'];
            $pettycash->bank_account_id = $validated['bank_account_id'] ?? $pettycash->bank_account_id;
            $pettycash->save();

            UpdatePettyCash::dispatch($request, $pettycash);

            return redirect()->back()->with('success', __('Petty cash fund updated successfully.'));
        }
        return redirect()->back()->with('error', __('Permission denied'));
    }

    public function destroy(PettyCash $pettycash)
    {
        if(Auth::user()->can('delete-petty-cashes')){
            if ((int) $pettycash->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            if ((string) $pettycash->status === '1') {
                return redirect()->back()->with('error', __('Approved petty cash funds cannot be deleted.'));
            }

            if ((int) $pettycash->id !== (int) $this->latestTenantFundId()) {
                return redirect()->back()->with('error', __('Only the latest petty cash fund can be deleted.'));
            }

            DestroyPettyCash::dispatch($pettycash);

            $pettycash->delete();

            return redirect()->back()->with('success', __('The petty cash has been deleted.'));
        }
        else{
            return redirect()->route('petty-cash-management.petty-cashes.index')->with('error', __('Permission denied'));
        }
    }

    public function approve(PettyCash $pettycash)
    {
        if(Auth::user()->can('approve-petty-cashes')){
            if ($pettycash->created_by != creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            if ((int) $pettycash->id !== (int) $this->latestTenantFundId()) {
                return redirect()->back()->with('error', __('Only the latest petty cash fund can be approved.'));
            }

            try {
                $changed = app(PettyCashApprovalService::class)->approvePettyCash($pettycash->id, creatorId(), Auth::id());
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('approve_petty_cash', $e, [
                    'pettycash_id' => $pettycash->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return back()->with('error', __('Something went wrong. Please try again.'));
            }

            if (!$changed) {
                return redirect()->back()->with('success', __('Petty cash approved successfully.'));
            }

            return redirect()->back()->with('success', __('Petty cash approved successfully.'));
        }
        return redirect()->back()->with('error', __('Permission denied'));
    }
}
