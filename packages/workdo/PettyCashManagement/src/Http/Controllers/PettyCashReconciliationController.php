<?php

namespace Workdo\PettyCashManagement\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PettyCashManagement\Http\Requests\StorePettyCashReconciliationRequest;
use Workdo\PettyCashManagement\Models\PettyCash;
use Workdo\PettyCashManagement\Models\PettyCashExpense;
use Workdo\PettyCashManagement\Models\PettyCashReconciliation;
use Workdo\PettyCashManagement\Services\PettyCashAuditLogService;

class PettyCashReconciliationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage-petty-cash-reconciliations')) {
            return back()->with('error', __('Permission denied'));
        }

        $reconciliations = PettyCashReconciliation::query()
            ->where(function ($q) {
                if (Auth::user()->can('manage-any-petty-cash-reconciliations')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-petty-cash-reconciliations')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->when(request('period_start'), fn ($q) => $q->where('period_start', '>=', request('period_start')))
            ->when(request('period_end'), fn ($q) => $q->where('period_end', '<=', request('period_end')))
            ->when(request('locked') !== null && request('locked') !== '', fn ($q) => $q->where('locked', (int) request('locked') === 1))
            ->latest()
            ->paginate(request('per_page', 10))
            ->withQueryString();

        return Inertia::render('PettyCashManagement/Reconciliations/Index', [
            'reconciliations' => $reconciliations,
            'filters' => [
                'period_start' => request('period_start', ''),
                'period_end' => request('period_end', ''),
                'locked' => request('locked', ''),
            ],
        ]);
    }

    public function create()
    {
        if (!Auth::user()->can('create-petty-cash-reconciliations')) {
            return back()->with('error', __('Permission denied'));
        }

        return Inertia::render('PettyCashManagement/Reconciliations/Create');
    }

    public function store(StorePettyCashReconciliationRequest $request)
    {
        if (!Auth::user()->can('create-petty-cash-reconciliations')) {
            return back()->with('error', __('Permission denied'));
        }

        $validated = $request->validated();
        $tenantId = (int) creatorId();
        $actorId = (int) Auth::id();

        $periodStart = Carbon::parse($validated['period_start'])->startOfDay();
        $periodEnd = Carbon::parse($validated['period_end'])->endOfDay();

        $previousReconciliation = PettyCashReconciliation::query()
            ->where('created_by', $tenantId)
            ->where('period_end', '<', $periodStart->toDateString())
            ->orderByDesc('period_end')
            ->first();

        if ($previousReconciliation) {
            $openingBalance = round((float) $previousReconciliation->expected_closing, 2);
        } else {
            $previousFund = PettyCash::query()
                ->where('created_by', $tenantId)
                ->where('status', 1)
                ->whereNotNull('date')
                ->whereDate('date', '<', $periodStart->toDateString())
                ->orderByDesc('date')
                ->first();

            $openingBalance = $previousFund ? round((float) $previousFund->closing_balance, 2) : 0.0;
        }

        $additionsTotal = round((float) PettyCash::query()
            ->where('created_by', $tenantId)
            ->where('status', 1)
            ->whereNotNull('date')
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->sum('added_amount'), 2);

        $expensesTotal = round((float) PettyCashExpense::query()
            ->where('created_by', $tenantId)
            ->where('status', 1)
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$periodStart, $periodEnd])
            ->sum('amount'), 2);

        $expectedClosing = round($openingBalance + $additionsTotal - $expensesTotal, 2);
        $countedCash = round((float) $validated['counted_cash'], 2);
        $variance = round($countedCash - $expectedClosing, 2);

        $reconciliation = PettyCashReconciliation::create([
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'opening_balance' => $openingBalance,
            'additions_total' => $additionsTotal,
            'expenses_total' => $expensesTotal,
            'expected_closing' => $expectedClosing,
            'counted_cash' => $countedCash,
            'variance' => $variance,
            'locked' => (bool) ($validated['locked'] ?? false),
            'creator_id' => $actorId,
            'created_by' => $tenantId,
        ]);

        app(PettyCashAuditLogService::class)->write(
            $tenantId,
            $actorId,
            'petty_cash_reconciliation.created',
            'petty_cash_reconciliation',
            (int) $reconciliation->id,
            [
                'period_start' => $reconciliation->period_start?->toDateString(),
                'period_end' => $reconciliation->period_end?->toDateString(),
                'opening_balance' => (string) $openingBalance,
                'additions_total' => (string) $additionsTotal,
                'expenses_total' => (string) $expensesTotal,
                'expected_closing' => (string) $expectedClosing,
                'counted_cash' => (string) $countedCash,
                'variance' => (string) $variance,
                'locked' => (bool) $reconciliation->locked,
            ]
        );

        return redirect()
            ->route('petty-cash-management.reconciliations.show', ['reconciliation' => $reconciliation->id])
            ->with('success', __('Reconciliation created successfully.'));
    }

    public function show(PettyCashReconciliation $reconciliation)
    {
        if (!Auth::user()->can('manage-petty-cash-reconciliations')) {
            return back()->with('error', __('Permission denied'));
        }

        if ((int) $reconciliation->created_by !== (int) creatorId()) {
            abort(404);
        }

        if (Auth::user()->can('manage-own-petty-cash-reconciliations') && !Auth::user()->can('manage-any-petty-cash-reconciliations')) {
            if ((int) $reconciliation->creator_id !== (int) Auth::id()) {
                abort(404);
            }
        }

        return Inertia::render('PettyCashManagement/Reconciliations/Show', [
            'reconciliation' => $reconciliation,
        ]);
    }
}
