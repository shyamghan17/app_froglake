<?php

namespace Workdo\PettyCashManagement\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Workdo\PettyCashManagement\Events\ApprovePettyCash;
use Workdo\PettyCashManagement\Events\CreatePettyCashExpense;
use Workdo\PettyCashManagement\Events\DestroyPettyCashRequest;
use Workdo\PettyCashManagement\Events\DestroyReimbursement;
use Workdo\PettyCashManagement\Models\PettyCash;
use Workdo\PettyCashManagement\Models\PettyCashExpense;
use Workdo\PettyCashManagement\Models\PettyCashReimbursement;
use Workdo\PettyCashManagement\Models\PettyCashRequest;

class PettyCashApprovalService
{
    public function approvePettyCash(int $pettyCashId, int $tenantId, int $actorId): bool
    {
        return (bool) DB::transaction(function () use ($pettyCashId, $tenantId, $actorId) {
            $pettyCash = PettyCash::query()
                ->where('id', $pettyCashId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((string) $pettyCash->status === '1') {
                return false;
            }

            $pettyCash->status = 1;
            $pettyCash->save();

            app(PettyCashAuditLogService::class)->write(
                $tenantId,
                $actorId,
                'petty_cash.approved',
                'petty_cash',
                (int) $pettyCash->id,
                [
                    'previous_status' => '0',
                    'new_status' => '1',
                ]
            );

            ApprovePettyCash::dispatch($pettyCash);

            return true;
        }, 3);
    }

    public function updatePettyCashRequestStatus(int $requestId, int $tenantId, int $actorId, array $validated): array
    {
        return DB::transaction(function () use ($requestId, $tenantId, $actorId, $validated) {
            $request = PettyCashRequest::query()
                ->where('id', $requestId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            $previousStatus = (string) $request->status;
            $requestedStatus = (string) $validated['status'];

            if ($requestedStatus === '1') {
                if ((string) $request->status === '1') {
                    return ['ok' => true, 'changed' => false];
                }

                $approvedAmount = round((float) $validated['approved_amount'], 2);

                $existingExpense = PettyCashExpense::query()
                    ->where('created_by', $tenantId)
                    ->where('type', 'pettycash')
                    ->where('request_id', $request->id)
                    ->lockForUpdate()
                    ->first();

                if ($existingExpense) {
                    $request->status = 1;
                    $request->approved_at = now();
                    $request->approved_by = $actorId;
                    $request->approved_amount = round((float) $existingExpense->amount, 2);
                    $request->rejection_reason = null;
                    $request->save();

                    app(PettyCashAuditLogService::class)->write(
                        $tenantId,
                        $actorId,
                        'petty_cash_request.approved',
                        'petty_cash_request',
                        (int) $request->id,
                        [
                            'previous_status' => $previousStatus,
                            'new_status' => '1',
                            'approved_amount' => round((float) $existingExpense->amount, 2),
                        ]
                    );

                    return ['ok' => true, 'changed' => true];
                }

                $pettyCash = PettyCash::query()
                    ->where('created_by', $tenantId)
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if (!$pettyCash) {
                    return ['ok' => false, 'error' => __('No petty cash record found!')];
                }

                if ((string) $pettyCash->status !== '1') {
                    return ['ok' => false, 'error' => __('Please approve petty cash first before processing request.')];
                }

                $closingBalance = round((float) $pettyCash->closing_balance - $approvedAmount, 2);

                if ($closingBalance < 0) {
                    return [
                        'ok' => false,
                        'error' => __('Insufficient petty cash balance! Available balance: :balance, Requested amount: :amount', [
                            'balance' => number_format((float) $pettyCash->closing_balance, 2),
                            'amount' => number_format($approvedAmount, 2),
                        ]),
                    ];
                }

                $pettyCash->closing_balance = $closingBalance;
                $pettyCash->total_expense = round((float) $pettyCash->total_expense + $approvedAmount, 2);
                $pettyCash->save();

                $expense = new PettyCashExpense();
                $expense->request_id = $request->id;
                $expense->pettycash_id = $pettyCash->id;
                $expense->type = 'pettycash';
                $expense->amount = $approvedAmount;
                $expense->remarks = $request->remarks;
                $expense->status = 1;
                $expense->approved_at = now();
                $expense->approved_by = $actorId;
                $expense->creator_id = $actorId;
                $expense->created_by = $tenantId;
                $expense->save();

                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'petty_cash_expense.created',
                    'petty_cash_expense',
                    (int) $expense->id,
                    [
                        'type' => 'pettycash',
                        'amount' => $approvedAmount,
                        'pettycash_id' => (int) $expense->pettycash_id,
                        'request_id' => (int) $expense->request_id,
                    ]
                );

                CreatePettyCashExpense::dispatch($expense);

                $request->status = 1;
                $request->approved_at = now();
                $request->approved_by = $actorId;
                $request->approved_amount = $approvedAmount;
                $request->rejection_reason = null;
                $request->save();

                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'petty_cash_request.approved',
                    'petty_cash_request',
                    (int) $request->id,
                    [
                        'previous_status' => $previousStatus,
                        'new_status' => '1',
                        'approved_amount' => $approvedAmount,
                        'pettycash_id' => (int) $pettyCash->id,
                    ]
                );

                return ['ok' => true, 'changed' => true];
            }

            if ($requestedStatus === '2') {
                if ((string) $request->status === '2') {
                    return ['ok' => true, 'changed' => false];
                }

                if ((string) $request->status === '1') {
                    $this->reversePettyCashRequestApprovalLocked($request, $tenantId, $actorId, 'reject');
                }

                $request->status = 2;
                $request->approved_at = null;
                $request->approved_by = null;
                $request->approved_amount = null;
                $request->rejection_reason = $validated['rejection_reason'] ?? null;
                $request->save();

                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'petty_cash_request.rejected',
                    'petty_cash_request',
                    (int) $request->id,
                    [
                        'previous_status' => $previousStatus,
                        'new_status' => '2',
                        'rejection_reason' => $request->rejection_reason,
                    ]
                );

                return ['ok' => true, 'changed' => true];
            }

            return ['ok' => false, 'error' => __('Something went wrong. Please try again.')];
        }, 3);
    }

    public function updateReimbursementStatus(int $reimbursementId, int $tenantId, int $actorId, array $validated): array
    {
        return DB::transaction(function () use ($reimbursementId, $tenantId, $actorId, $validated) {
            $reimbursement = PettyCashReimbursement::query()
                ->where('id', $reimbursementId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            $previousStatus = (string) $reimbursement->status;
            $requestedStatus = (string) $validated['status'];

            if ($requestedStatus === '1') {
                if ((string) $reimbursement->status === '1') {
                    return ['ok' => true, 'changed' => false];
                }

                $approvedAmount = round((float) $validated['approved_amount'], 2);

                $existingExpense = PettyCashExpense::query()
                    ->where('created_by', $tenantId)
                    ->where('type', 'reimbursement')
                    ->where('reimbursement_id', $reimbursement->id)
                    ->lockForUpdate()
                    ->first();

                if ($existingExpense) {
                    $reimbursement->status = 1;
                    $reimbursement->approved_date = now();
                    $reimbursement->approved_by = $actorId;
                    $reimbursement->approved_amount = round((float) $existingExpense->amount, 2);
                    $reimbursement->rejection_reason = null;
                    $reimbursement->save();

                    app(PettyCashAuditLogService::class)->write(
                        $tenantId,
                        $actorId,
                        'reimbursement.approved',
                        'reimbursement',
                        (int) $reimbursement->id,
                        [
                            'previous_status' => $previousStatus,
                            'new_status' => '1',
                            'approved_amount' => round((float) $existingExpense->amount, 2),
                        ]
                    );

                    return ['ok' => true, 'changed' => true];
                }

                $pettyCash = PettyCash::query()
                    ->where('created_by', $tenantId)
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if (!$pettyCash) {
                    return ['ok' => false, 'error' => __('Petty cash not found!')];
                }

                if ((string) $pettyCash->status !== '1') {
                    return ['ok' => false, 'error' => __('Please approve petty cash first before processing reimbursement.')];
                }

                $closingBalance = round((float) $pettyCash->closing_balance - $approvedAmount, 2);

                if ($closingBalance < 0) {
                    return [
                        'ok' => false,
                        'error' => __('Insufficient petty cash balance! Available balance: :balance, Requested amount: :amount', [
                            'balance' => number_format((float) $pettyCash->closing_balance, 2),
                            'amount' => number_format($approvedAmount, 2),
                        ]),
                    ];
                }

                $pettyCash->closing_balance = $closingBalance;
                $pettyCash->total_expense = round((float) $pettyCash->total_expense + $approvedAmount, 2);
                $pettyCash->save();

                $expense = new PettyCashExpense();
                $expense->reimbursement_id = $reimbursement->id;
                $expense->pettycash_id = $pettyCash->id;
                $expense->type = 'reimbursement';
                $expense->amount = $approvedAmount;
                $expense->remarks = $reimbursement->description;
                $expense->status = 1;
                $expense->approved_at = now();
                $expense->approved_by = $actorId;
                $expense->creator_id = $actorId;
                $expense->created_by = $tenantId;
                $expense->save();

                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'petty_cash_expense.created',
                    'petty_cash_expense',
                    (int) $expense->id,
                    [
                        'type' => 'reimbursement',
                        'amount' => $approvedAmount,
                        'pettycash_id' => (int) $expense->pettycash_id,
                        'reimbursement_id' => (int) $expense->reimbursement_id,
                    ]
                );

                CreatePettyCashExpense::dispatch($expense);

                $reimbursement->status = 1;
                $reimbursement->approved_date = now();
                $reimbursement->approved_by = $actorId;
                $reimbursement->approved_amount = $approvedAmount;
                $reimbursement->rejection_reason = null;
                $reimbursement->save();

                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'reimbursement.approved',
                    'reimbursement',
                    (int) $reimbursement->id,
                    [
                        'previous_status' => $previousStatus,
                        'new_status' => '1',
                        'approved_amount' => $approvedAmount,
                        'pettycash_id' => (int) $pettyCash->id,
                    ]
                );

                return ['ok' => true, 'changed' => true];
            }

            if ($requestedStatus === '2') {
                if ((string) $reimbursement->status === '2') {
                    return ['ok' => true, 'changed' => false];
                }

                if ((string) $reimbursement->status === '1') {
                    $this->reverseReimbursementApprovalLocked($reimbursement, $tenantId, $actorId, 'reject');
                }

                $reimbursement->status = 2;
                $reimbursement->approved_date = null;
                $reimbursement->approved_by = null;
                $reimbursement->approved_amount = null;
                $reimbursement->rejection_reason = $validated['rejection_reason'] ?? null;
                $reimbursement->save();

                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'reimbursement.rejected',
                    'reimbursement',
                    (int) $reimbursement->id,
                    [
                        'previous_status' => $previousStatus,
                        'new_status' => '2',
                        'rejection_reason' => $reimbursement->rejection_reason,
                    ]
                );

                return ['ok' => true, 'changed' => true];
            }

            return ['ok' => false, 'error' => __('Something went wrong. Please try again.')];
        }, 3);
    }

    public function updatePettyCashRequestAfterEdit(int $requestId, int $tenantId, int $actorId, array $data): PettyCashRequest
    {
        return DB::transaction(function () use ($requestId, $tenantId, $actorId, $data) {
            $request = PettyCashRequest::query()
                ->where('id', $requestId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((string) $request->status === '1') {
                $this->reversePettyCashRequestApprovalLocked($request, $tenantId, $actorId, 'edit');
            }

            $previousReceiptPath = $request->receipt_path;
            $request->user_id = $data['user_id'];
            $request->categorie_id = $data['categorie_id'];
            $request->requested_amount = $data['requested_amount'];
            $request->status = 0;
            $request->remarks = $data['remarks'] ?? null;
            if (!empty($data['receipt_path'])) {
                $request->receipt_path = $data['receipt_path'];
            }
            $request->approved_at = null;
            $request->approved_by = null;
            $request->approved_amount = null;
            $request->rejection_reason = null;
            $request->save();

            if (!empty($data['receipt_path']) && $previousReceiptPath !== $request->receipt_path) {
                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'petty_cash_request.receipt_updated',
                    'petty_cash_request',
                    (int) $request->id,
                    [
                        'previous_receipt_path' => $previousReceiptPath,
                        'new_receipt_path' => $request->receipt_path,
                    ]
                );
            }

            return $request;
        }, 3);
    }

    public function updateReimbursementAfterEdit(int $reimbursementId, int $tenantId, int $actorId, array $data): PettyCashReimbursement
    {
        return DB::transaction(function () use ($reimbursementId, $tenantId, $actorId, $data) {
            $reimbursement = PettyCashReimbursement::query()
                ->where('id', $reimbursementId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((string) $reimbursement->status === '1') {
                $this->reverseReimbursementApprovalLocked($reimbursement, $tenantId, $actorId, 'edit');
            }

            $previousReceiptPath = $reimbursement->receipt_path;
            $reimbursement->user_id = $data['user_id'];
            $reimbursement->category_id = $data['category_id'];
            $reimbursement->amount = $data['amount'];
            $reimbursement->status = 0;
            $reimbursement->description = $data['description'] ?? null;
            if (!empty($data['receipt_path'])) {
                $reimbursement->receipt_path = $data['receipt_path'];
            }
            $reimbursement->approved_date = null;
            $reimbursement->approved_by = null;
            $reimbursement->approved_amount = null;
            $reimbursement->rejection_reason = null;
            $reimbursement->save();

            if (!empty($data['receipt_path']) && $previousReceiptPath !== $reimbursement->receipt_path) {
                app(PettyCashAuditLogService::class)->write(
                    $tenantId,
                    $actorId,
                    'reimbursement.receipt_updated',
                    'reimbursement',
                    (int) $reimbursement->id,
                    [
                        'previous_receipt_path' => $previousReceiptPath,
                        'new_receipt_path' => $reimbursement->receipt_path,
                    ]
                );
            }

            return $reimbursement;
        }, 3);
    }

    public function deletePettyCashRequestWithReversal(int $requestId, int $tenantId, int $actorId): void
    {
        DB::transaction(function () use ($requestId, $tenantId, $actorId) {
            $request = PettyCashRequest::query()
                ->where('id', $requestId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((string) $request->status === '1') {
                $this->reversePettyCashRequestApprovalLocked($request, $tenantId, $actorId, 'delete');
            }

            app(PettyCashAuditLogService::class)->write(
                $tenantId,
                $actorId,
                'petty_cash_request.deleted',
                'petty_cash_request',
                (int) $request->id,
                [
                    'previous_status' => (string) $request->status,
                ]
            );

            DestroyPettyCashRequest::dispatch($request);
            $request->delete();
        }, 3);
    }

    public function deleteReimbursementWithReversal(int $reimbursementId, int $tenantId, int $actorId): void
    {
        DB::transaction(function () use ($reimbursementId, $tenantId, $actorId) {
            $reimbursement = PettyCashReimbursement::query()
                ->where('id', $reimbursementId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((string) $reimbursement->status === '1') {
                $this->reverseReimbursementApprovalLocked($reimbursement, $tenantId, $actorId, 'delete');
            }

            app(PettyCashAuditLogService::class)->write(
                $tenantId,
                $actorId,
                'reimbursement.deleted',
                'reimbursement',
                (int) $reimbursement->id,
                [
                    'previous_status' => (string) $reimbursement->status,
                ]
            );

            DestroyReimbursement::dispatch($reimbursement);
            $reimbursement->delete();
        }, 3);
    }

    private function reversePettyCashRequestApprovalLocked(PettyCashRequest $request, int $tenantId, int $actorId, string $reason): void
    {
        $expenses = PettyCashExpense::query()
            ->where('created_by', $tenantId)
            ->where('type', 'pettycash')
            ->where('request_id', $request->id)
            ->lockForUpdate()
            ->get();

        if ($expenses->isEmpty()) {
            return;
        }

        $totalAmount = round($expenses->sum(fn ($e) => (float) $e->amount), 2);

        app(PettyCashAuditLogService::class)->write(
            $tenantId,
            $actorId,
            'petty_cash_request.approval_reversed',
            'petty_cash_request',
            (int) $request->id,
            [
                'reason' => $reason,
                'expense_ids' => $expenses->pluck('id')->all(),
                'total_amount' => $totalAmount,
            ]
        );

        $amountsByPettyCash = $expenses
            ->groupBy('pettycash_id')
            ->map(fn ($group) => round($group->sum(fn ($e) => (float) $e->amount), 2));

        foreach ($amountsByPettyCash as $pettyCashId => $amount) {
            if (!$pettyCashId || $amount <= 0) {
                continue;
            }

            $pettyCash = PettyCash::query()
                ->where('id', $pettyCashId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->first();

            if (!$pettyCash) {
                continue;
            }

            $pettyCash->closing_balance = round((float) $pettyCash->closing_balance + $amount, 2);
            $pettyCash->total_expense = max(0, round((float) $pettyCash->total_expense - $amount, 2));
            $pettyCash->save();
        }

        PettyCashExpense::query()
            ->whereKey($expenses->pluck('id')->all())
            ->delete();
    }

    private function reverseReimbursementApprovalLocked(PettyCashReimbursement $reimbursement, int $tenantId, int $actorId, string $reason): void
    {
        $expenses = PettyCashExpense::query()
            ->where('created_by', $tenantId)
            ->where('type', 'reimbursement')
            ->where('reimbursement_id', $reimbursement->id)
            ->lockForUpdate()
            ->get();

        if ($expenses->isEmpty()) {
            return;
        }

        $totalAmount = round($expenses->sum(fn ($e) => (float) $e->amount), 2);

        app(PettyCashAuditLogService::class)->write(
            $tenantId,
            $actorId,
            'reimbursement.approval_reversed',
            'reimbursement',
            (int) $reimbursement->id,
            [
                'reason' => $reason,
                'expense_ids' => $expenses->pluck('id')->all(),
                'total_amount' => $totalAmount,
            ]
        );

        $amountsByPettyCash = $expenses
            ->groupBy('pettycash_id')
            ->map(fn ($group) => round($group->sum(fn ($e) => (float) $e->amount), 2));

        foreach ($amountsByPettyCash as $pettyCashId => $amount) {
            if (!$pettyCashId || $amount <= 0) {
                continue;
            }

            $pettyCash = PettyCash::query()
                ->where('id', $pettyCashId)
                ->where('created_by', $tenantId)
                ->lockForUpdate()
                ->first();

            if (!$pettyCash) {
                continue;
            }

            $pettyCash->closing_balance = round((float) $pettyCash->closing_balance + $amount, 2);
            $pettyCash->total_expense = max(0, round((float) $pettyCash->total_expense - $amount, 2));
            $pettyCash->save();
        }

        PettyCashExpense::query()
            ->whereKey($expenses->pluck('id')->all())
            ->delete();
    }

    public function logApprovalException(string $action, \Throwable $e, array $context = []): void
    {
        Log::error('PettyCashManagement approval error: ' . $action, [
            ...$context,
            'exception_class' => $e::class,
            'exception_message' => $e->getMessage(),
        ]);
    }
}
