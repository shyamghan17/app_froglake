<?php

namespace Workdo\PettyCashManagement\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Workdo\PettyCashManagement\Models\PettyCashCategory;
use Workdo\PettyCashManagement\Models\PettyCashExpense;

class PettyCashReportController extends Controller
{
    private function parseDate(?string $date): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function reportFilters(): array
    {
        $validated = request()->validate([
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('petty_cash_categories', 'id')->where(fn ($q) => $q->where('created_by', creatorId())),
            ],
        ]);

        $type = request('type');
        $type = in_array($type, ['pettycash', 'reimbursement'], true) ? $type : null;

        $status = request('status');
        $status = in_array((string) $status, ['0', '1', '2'], true) ? (string) $status : null;

        $allowedSort = ['type', 'amount', 'approved_at'];
        $sort = request('sort');
        $sort = in_array($sort, $allowedSort, true) ? $sort : null;

        $direction = request('direction', 'asc');
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc';

        return [
            'start_date' => $this->parseDate(request('start_date')),
            'end_date' => $this->parseDate(request('end_date')),
            'user_id' => $validated['user_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'type' => $type,
            'status' => $status,
            'sort' => $sort,
            'direction' => $direction,
        ];
    }

    private function filteredExpensesQuery(array $filters)
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $userId = $filters['user_id'] ?? null;
        $categoryId = $filters['category_id'] ?? null;
        $type = $filters['type'] ?? null;
        $status = $filters['status'] ?? null;

        return PettyCashExpense::query()
            ->with(['request.user', 'request.category', 'reimbursement.user', 'reimbursement.category', 'approver', 'pettyCash'])
            ->where(function ($q) {
                if (Auth::user()->can('manage-any-petty-cash-expenses')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-petty-cash-expenses')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereHas('pettyCash', fn ($pettyCashQuery) => $pettyCashQuery->whereDate('date', '>=', $startDate));
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereHas('pettyCash', fn ($pettyCashQuery) => $pettyCashQuery->whereDate('date', '<=', $endDate));
            })
            ->when($type !== null && $type !== '', fn ($q) => $q->where('type', $type))
            ->when($status !== null && $status !== '', fn ($q) => $q->where('status', $status))
            ->when($userId, function ($q) use ($userId) {
                $q->where(function ($subQ) use ($userId) {
                    $subQ->whereHas('request', fn ($requestQuery) => $requestQuery->where('user_id', $userId))
                        ->orWhereHas('reimbursement', fn ($reimbursementQuery) => $reimbursementQuery->where('user_id', $userId));
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where(function ($subQ) use ($categoryId) {
                    $subQ->whereHas('request', fn ($requestQuery) => $requestQuery->where('categorie_id', $categoryId))
                        ->orWhereHas('reimbursement', fn ($reimbursementQuery) => $reimbursementQuery->where('category_id', $categoryId));
                });
            });
    }

    private function applySorting($query, array $filters)
    {
        $sort = $filters['sort'] ?? null;
        $direction = $filters['direction'] ?? 'asc';

        if ($sort) {
            return $query->orderBy($sort, $direction);
        }

        return $query->latest();
    }

    public function index()
    {
        if (!Auth::user()->can('manage-petty-cash-expenses')) {
            return back()->with('error', __('Permission denied'));
        }

        $filters = $this->reportFilters();
        $expensesQuery = $this->filteredExpensesQuery($filters);

        $totalAmount = (clone $expensesQuery)->sum('amount');
        $totalPettyCashAmount = (clone $expensesQuery)->where('type', 'pettycash')->sum('amount');
        $totalReimbursementAmount = (clone $expensesQuery)->where('type', 'reimbursement')->sum('amount');
        $totalCount = (clone $expensesQuery)->count();

        $expenses = $this->applySorting(clone $expensesQuery, $filters)
            ->paginate(request('per_page', 10))
            ->withQueryString();

        if (Auth::user()->can('manage-any-petty-cash-expenses')) {
            $users = User::where('created_by', creatorId())->emp()->select('id', 'name')->get();
        } elseif (Auth::user()->can('manage-own-petty-cash-expenses')) {
            $users = User::where('id', Auth::id())->select('id', 'name')->get();
        } else {
            $users = collect();
        }

        $categories = PettyCashCategory::where('created_by', creatorId())->select('id', 'name')->get();

        return Inertia::render('PettyCashManagement/Reports/PettyCashReport', [
            'expenses' => $expenses,
            'totals' => [
                'count' => $totalCount,
                'total_amount' => (string) $totalAmount,
                'pettycash_amount' => (string) $totalPettyCashAmount,
                'reimbursement_amount' => (string) $totalReimbursementAmount,
            ],
            'users' => $users,
            'categories' => $categories,
            'filters' => [
                'start_date' => $filters['start_date'],
                'end_date' => $filters['end_date'],
                'user_id' => $filters['user_id'] ?? '',
                'category_id' => $filters['category_id'] ?? '',
                'type' => $filters['type'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        if (!Auth::user()->can('manage-petty-cash-expenses')) {
            abort(403, __('Permission denied'));
        }

        $filters = $this->reportFilters();
        $expensesQuery = $this->applySorting($this->filteredExpensesQuery($filters), $filters);

        $filename = 'petty-cash-report-' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($expensesQuery) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Petty Cash Date',
                'Petty Cash Number',
                'Request/Reimbursement Number',
                'User',
                'Category',
                'Type',
                'Amount',
                'Approved At',
                'Approved By',
            ]);

            foreach ($expensesQuery->lazy() as $expense) {
                $referenceNumber = $expense->request?->request_number ?: ($expense->reimbursement?->reimbursement_number ?: '');
                $userName = $expense->request?->user?->name ?: ($expense->reimbursement?->user?->name ?: '');
                $categoryName = $expense->request?->category?->name ?: ($expense->reimbursement?->category?->name ?: '');
                $pettyCashDate = $expense->pettyCash?->date ?: '';
                $pettyCashNumber = $expense->pettyCash?->pettycash_number ?: '';

                fputcsv($handle, [
                    $pettyCashDate,
                    $pettyCashNumber,
                    $referenceNumber,
                    $userName,
                    $categoryName,
                    (string) $expense->type,
                    (string) $expense->amount,
                    $expense->approved_at ? $expense->approved_at->toDateTimeString() : '',
                    $expense->approver?->name ?: '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function print()
    {
        if (!Auth::user()->can('manage-petty-cash-expenses')) {
            abort(403, __('Permission denied'));
        }

        $filters = $this->reportFilters();
        $expensesQuery = $this->filteredExpensesQuery($filters);

        $totalAmount = (clone $expensesQuery)->sum('amount');
        $totalPettyCashAmount = (clone $expensesQuery)->where('type', 'pettycash')->sum('amount');
        $totalReimbursementAmount = (clone $expensesQuery)->where('type', 'reimbursement')->sum('amount');
        $totalCount = (clone $expensesQuery)->count();

        $expenses = $this->applySorting(clone $expensesQuery, $filters)->get();

        return Inertia::render('PettyCashManagement/Reports/PettyCashReportPrint', [
            'expenses' => $expenses,
            'totals' => [
                'count' => $totalCount,
                'total_amount' => (string) $totalAmount,
                'pettycash_amount' => (string) $totalPettyCashAmount,
                'reimbursement_amount' => (string) $totalReimbursementAmount,
            ],
            'filters' => [
                'start_date' => $filters['start_date'],
                'end_date' => $filters['end_date'],
                'user_id' => $filters['user_id'] ?? '',
                'category_id' => $filters['category_id'] ?? '',
                'type' => $filters['type'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }
}
