<?php

namespace Workdo\PettyCashManagement\Http\Controllers;

use Workdo\PettyCashManagement\Models\PettyCashReimbursement;
use Workdo\PettyCashManagement\Http\Requests\StoreReimbursementRequest;
use Workdo\PettyCashManagement\Http\Requests\UpdateReimbursementRequest;
use Workdo\PettyCashManagement\Events\CreateReimbursement;
use Workdo\PettyCashManagement\Events\UpdateReimbursement;
use Workdo\PettyCashManagement\Events\UpdateStatusReimbursement;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\User;
use Workdo\PettyCashManagement\Models\PettyCashCategory;
use App\Services\DynamicStorageService;
use App\Services\StorageConfigService;
use Workdo\PettyCashManagement\Services\PettyCashApprovalService;
use Workdo\PettyCashManagement\Services\PettyCashAuditLogService;

class ReimbursementController extends Controller
{
    private function sanitizedSort(): array
    {
        $allowedSorts = ['reimbursement_number', 'amount', 'approved_amount', 'status', 'approved_by', 'created_at'];
        $sort = request('sort');
        $direction = request('direction', 'asc');

        return [
            'sort' => in_array($sort, $allowedSorts, true) ? $sort : null,
            'direction' => in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc',
        ];
    }

    public function index()
    {
        if(Auth::user()->can('manage-reimbursements')){
            $sort = $this->sanitizedSort();
            $reimbursements = PettyCashReimbursement::query()
                ->with(['user', 'category', 'approver'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-reimbursements')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-reimbursements')) {
                        $q->where(function($subQ) {
                            $subQ->where('creator_id', Auth::id())->orwhere('user_id', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('reimbursement_number'), function($q) {
                    $q->where('reimbursement_number', 'like', '%' . request('reimbursement_number') . '%');
                })
                ->when(request('user_id') && request('user_id') !== '', fn($q) => $q->where('user_id', request('user_id')))
                ->when(request('category_id') && request('category_id') !== '', fn($q) => $q->where('category_id', request('category_id')))
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('approved_by') && request('approved_by') !== '', fn($q) => $q->where('approved_by', request('approved_by')))
                ->when($sort['sort'], fn($q) => $q->orderBy($sort['sort'], $sort['direction']), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            // Filter users based on permissions
            if(Auth::user()->can('manage-any-reimbursements')) {
                $users = User::where('created_by', creatorId())->emp()->select('id', 'name')->get();
            } elseif(Auth::user()->can('manage-own-reimbursements')) {
                $users = User::where('id', Auth::id())->select('id', 'name')->get();
            } else {
                $users = collect();
            }

            $categories = PettyCashCategory::where('created_by', creatorId())->select('id', 'name')->get();

            return Inertia::render('PettyCashManagement/Reimbursements/Index', [
                'reimbursements' => $reimbursements,
                'users'          => $users,
                'categories'     => $categories
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreReimbursementRequest $request)
    {
        if(Auth::user()->can('create-reimbursements')){
            $validated = $request->validated();

            $reimbursement               = new PettyCashReimbursement();
            $reimbursement->user_id      = $validated['user_id'];
            $reimbursement->category_id  = $validated['category_id'];
            $reimbursement->amount       = $validated['amount'];
            $reimbursement->status       = 0;
            $reimbursement->description  = $validated['description'];
            if ($request->hasFile('receipt_path')) {
                $filenameWithExt = $request->file('receipt_path')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('receipt_path')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request,'receipt_path',$fileNameToStore,'petty_cach_reimbursement');
                if($uplaod['flag'] == 1)
                {
                    $reimbursement->receipt_path = $uplaod['url'];
                }
                else
                {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }
            $reimbursement->creator_id   = Auth::id();
            $reimbursement->created_by   = creatorId();
            $reimbursement->save();

            app(PettyCashAuditLogService::class)->write(
                creatorId(),
                Auth::id(),
                'reimbursement.created',
                'reimbursement',
                (int) $reimbursement->id,
                [
                    'amount' => (float) $reimbursement->amount,
                    'has_receipt' => !empty($reimbursement->receipt_path),
                ]
            );

            if (!empty($reimbursement->receipt_path)) {
                app(PettyCashAuditLogService::class)->write(
                    creatorId(),
                    Auth::id(),
                    'reimbursement.receipt_uploaded',
                    'reimbursement',
                    (int) $reimbursement->id,
                    [
                        'receipt_path' => $reimbursement->receipt_path,
                    ]
                );
            }

            CreateReimbursement::dispatch($request, $reimbursement);

            return redirect()->route('petty-cash-management.reimbursements.index')->with('success', __('The reimbursement has been created successfully.'));
        }
        else{
            return redirect()->route('petty-cash-management.reimbursements.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateReimbursementRequest $request, PettyCashReimbursement $reimbursement)
    {
        if(Auth::user()->can('edit-reimbursements')){
            if ((int) $reimbursement->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            $data = [
                'user_id' => $validated['user_id'],
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
            ];
            if ($request->hasFile('receipt_path')) {
                $filenameWithExt = $request->file('receipt_path')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('receipt_path')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request,'receipt_path',$fileNameToStore,'petty_cach_reimbursement');
                if($uplaod['flag'] == 1)
                {
                    $data['receipt_path'] = $uplaod['url'];
                }
                else
                {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }
            try {
                $reimbursement = app(PettyCashApprovalService::class)->updateReimbursementAfterEdit(
                    $reimbursement->id,
                    creatorId(),
                    Auth::id(),
                    $data
                );
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('edit_reimbursement', $e, [
                    'reimbursement_id' => $reimbursement->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
            }

            UpdateReimbursement::dispatch($request, $reimbursement);

            return redirect()->back()->with('success', __('The reimbursement details are updated successfully.'));
        }
        else{
            return redirect()->route('petty-cash-management.reimbursements.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(PettyCashReimbursement $reimbursement)
    {
        if(Auth::user()->can('delete-reimbursements')){
            if ((int) $reimbursement->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            try {
                app(PettyCashApprovalService::class)->deleteReimbursementWithReversal($reimbursement->id, creatorId(), Auth::id());
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('delete_reimbursement', $e, [
                    'reimbursement_id' => $reimbursement->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
            }

            return redirect()->back()->with('success', __('The reimbursement has been deleted.'));
        }
        else{
            return redirect()->route('petty-cash-management.reimbursements.index')->with('error', __('Permission denied'));
        }
    }

    public function updateStatus(PettyCashReimbursement $reimbursement)
    {
        if(Auth::user()->can('approve-reimbursements')){
            if ((int) $reimbursement->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            $validated = request()->validate([
                'status'           => 'required|in:1,2',
                'approved_amount'  => 'required_if:status,1|numeric|min:0',
                'rejection_reason' => 'required_if:status,2|nullable|string|max:1000'
            ]);

            try {
                $result = app(PettyCashApprovalService::class)->updateReimbursementStatus(
                    $reimbursement->id,
                    creatorId(),
                    Auth::id(),
                    $validated
                );
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('update_reimbursement_status', $e, [
                    'reimbursement_id' => $reimbursement->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
            }

            if (!($result['ok'] ?? false)) {
                return redirect()->back()->with('error', $result['error'] ?? __('Something went wrong. Please try again.'));
            }

            if (($result['changed'] ?? false) === true) {
                UpdateStatusReimbursement::dispatch($reimbursement->fresh());
            }

            $message = $validated['status'] == '1' ? __('The reimbursement has been approved.') : __('The reimbursement has been rejected.');
            return redirect()->back()->with('success', $message);
        }
        else{
            return redirect()->route('petty-cash-management.reimbursements.index')->with('error', __('Permission denied'));
        }
    }

    public function viewReceipt(PettyCashReimbursement $reimbursement)
    {
        $user = Auth::user();
        if ((int) $reimbursement->created_by !== (int) creatorId()) {
            abort(404);
        }

        $this->assertCanAccessReceipt($user, $reimbursement);

        if (empty($reimbursement->receipt_path)) {
            abort(404);
        }

        return $this->streamReceipt($reimbursement->receipt_path, $this->makeDownloadName($reimbursement->reimbursement_number, $reimbursement->receipt_path), false);
    }

    public function downloadReceipt(PettyCashReimbursement $reimbursement)
    {
        $user = Auth::user();
        if ((int) $reimbursement->created_by !== (int) creatorId()) {
            abort(404);
        }

        $this->assertCanAccessReceipt($user, $reimbursement);

        if (empty($reimbursement->receipt_path)) {
            abort(404);
        }

        return $this->streamReceipt($reimbursement->receipt_path, $this->makeDownloadName($reimbursement->reimbursement_number, $reimbursement->receipt_path), true);
    }

    private function streamReceipt(string $receiptPath, string $downloadName, bool $asAttachment)
    {
        DynamicStorageService::configureDynamicDisks();
        $disk = StorageConfigService::getActiveDisk();

        $storagePath = 'media/' . ltrim($receiptPath, '/');

        if (!Storage::disk($disk)->exists($storagePath)) {
            abort(404);
        }

        $stream = Storage::disk($disk)->readStream($storagePath);
        if ($stream === false) {
            abort(404);
        }

        $mimeType = Storage::disk($disk)->mimeType($storagePath) ?: 'application/octet-stream';
        $disposition = $asAttachment ? 'attachment' : 'inline';

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . $downloadName . '"',
        ]);
    }

    private function makeDownloadName(?string $reference, string $receiptPath): string
    {
        $referencePart = $reference ? Str::slug($reference) . '_' : '';
        $basename = basename($receiptPath);
        $basename = preg_replace('/[^A-Za-z0-9._-]/', '_', $basename) ?: 'receipt';

        return $referencePart . $basename;
    }

    private function assertCanAccessReceipt($user, PettyCashReimbursement $reimbursement): void
    {
        if (!$user) {
            abort(403);
        }

        if (
            $user->can('manage-petty-cash-expenses') ||
            $user->can('approve-reimbursements') ||
            $user->can('manage-any-reimbursements')
        ) {
            return;
        }

        $isOwner = (int) $reimbursement->creator_id === (int) $user->id || (int) $reimbursement->user_id === (int) $user->id;

        if ($isOwner && ($user->can('manage-own-reimbursements') || $user->can('view-reimbursements'))) {
            return;
        }

        abort(403);
    }



    public function getCategoriesByUser($userId)
    {
        if(Auth::user()->can('view-categories')){
            $user = User::find($userId);
            if (!$user || (int) $user->created_by !== (int) creatorId()) {
                return response()->json([], 404);
            }

            $categories = PettyCashCategory::query()
                ->where('created_by', creatorId())
                ->when(Schema::hasColumn('petty_cash_categories', 'user_id'), function ($q) use ($user) {
                    $q->where(function ($categoryQuery) use ($user) {
                        $categoryQuery->whereNull('user_id')
                            ->orWhere('user_id', $user->id);
                    });
                })
                ->select('id', 'name')
                ->get();

            return response()->json($categories);
        }
        else{
            return response()->json([], 403);
        }
    }
}
