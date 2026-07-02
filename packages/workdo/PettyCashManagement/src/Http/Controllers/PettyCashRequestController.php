<?php

namespace Workdo\PettyCashManagement\Http\Controllers;

use Workdo\PettyCashManagement\Models\PettyCashRequest;
use Workdo\PettyCashManagement\Http\Requests\StorePettyCashRequestRequest;
use Workdo\PettyCashManagement\Http\Requests\UpdatePettyCashRequestRequest;
use Workdo\PettyCashManagement\Events\CreatePettyCashRequest;
use Workdo\PettyCashManagement\Events\UpdatePettyCashRequest;
use Workdo\PettyCashManagement\Events\UpdateStatusPettyCashRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Workdo\PettyCashManagement\Models\PettyCashCategory;
use App\Models\User;
use App\Services\DynamicStorageService;
use App\Services\StorageConfigService;
use Workdo\PettyCashManagement\Services\PettyCashApprovalService;
use Workdo\PettyCashManagement\Services\PettyCashAuditLogService;

class PettyCashRequestController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-petty-cash-requests')){
            $pettycashrequests = PettyCashRequest::query()
                ->with(['user', 'category', 'approver'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-petty-cash-requests')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-petty-cash-requests')) {
                        $q->where(function($subQ) {
                            $subQ->where('creator_id', Auth::id())->orwhere('user_id', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('request_number'), function($q) {
                    $q->where('request_number', 'like', '%' . request('request_number') . '%');
                })
                ->when(request('user_id') && request('user_id') !== '', fn($q) => $q->where('user_id', request('user_id')))
                ->when(request('categorie_id') && request('categorie_id') !== '', fn($q) => $q->where('categorie_id', request('categorie_id')))
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            // Filter users based on permissions
            if(Auth::user()->can('manage-any-petty-cash-requests')) {
                $users = User::where('created_by', creatorId())->emp()->select('id', 'name')->get();
            } elseif(Auth::user()->can('manage-own-petty-cash-requests')) {
                $users = User::where('id', Auth::id())->select('id', 'name')->get();
            } else {
                $users = collect();
            }

            $categories = PettyCashCategory::where('created_by', creatorId())->select('id', 'name')->get();

            return Inertia::render('PettyCashManagement/PettyCashRequests/Index', [
                'pettycashrequests'   => $pettycashrequests,
                'users'               => $users,
                'pettycashcategories' => $categories,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StorePettyCashRequestRequest $request)
    {
        if(Auth::user()->can('create-petty-cash-requests')){
            $validated = $request->validated();

            $pettycashrequest                   = new PettyCashRequest();
            $pettycashrequest->user_id          = $validated['user_id'];
            $pettycashrequest->categorie_id     = $validated['categorie_id'];
            $pettycashrequest->requested_amount = $validated['requested_amount'];
            $pettycashrequest->status           = 0;
            $pettycashrequest->remarks          = $validated['remarks'];
            if ($request->hasFile('receipt_path')) {
                $filenameWithExt = $request->file('receipt_path')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('receipt_path')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'receipt_path', $fileNameToStore, 'petty_cash_request');
                if ($uplaod['flag'] == 1) {
                    $pettycashrequest->receipt_path = $uplaod['url'];
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }
            $pettycashrequest->creator_id       = Auth::id();
            $pettycashrequest->created_by       = creatorId();
            $pettycashrequest->save();

            app(PettyCashAuditLogService::class)->write(
                creatorId(),
                Auth::id(),
                'petty_cash_request.created',
                'petty_cash_request',
                (int) $pettycashrequest->id,
                [
                    'requested_amount' => (float) $pettycashrequest->requested_amount,
                    'has_receipt' => !empty($pettycashrequest->receipt_path),
                ]
            );

            if (!empty($pettycashrequest->receipt_path)) {
                app(PettyCashAuditLogService::class)->write(
                    creatorId(),
                    Auth::id(),
                    'petty_cash_request.receipt_uploaded',
                    'petty_cash_request',
                    (int) $pettycashrequest->id,
                    [
                        'receipt_path' => $pettycashrequest->receipt_path,
                    ]
                );
            }

            CreatePettyCashRequest::dispatch($request, $pettycashrequest);

            return redirect()->route('petty-cash-management.petty-cash-requests.index')->with('success', __('The petty cash request has been created successfully.'));
        }
        else{
            return redirect()->route('petty-cash-management.petty-cash-requests.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdatePettyCashRequestRequest $request, PettyCashRequest $pettycashrequest)
    {
        if(Auth::user()->can('edit-petty-cash-requests')){
            if ((int) $pettycashrequest->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            $data = [
                'user_id' => $validated['user_id'],
                'categorie_id' => $validated['categorie_id'],
                'requested_amount' => $validated['requested_amount'],
                'remarks' => $validated['remarks'],
            ];
            if ($request->hasFile('receipt_path')) {
                $filenameWithExt = $request->file('receipt_path')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('receipt_path')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'receipt_path', $fileNameToStore, 'petty_cash_request');
                if ($uplaod['flag'] == 1) {
                    $data['receipt_path'] = $uplaod['url'];
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }
            try {
                $pettycashrequest = app(PettyCashApprovalService::class)->updatePettyCashRequestAfterEdit(
                    $pettycashrequest->id,
                    creatorId(),
                    Auth::id(),
                    $data
                );
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('edit_petty_cash_request', $e, [
                    'request_id' => $pettycashrequest->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
            }

            UpdatePettyCashRequest::dispatch($request, $pettycashrequest);

            return redirect()->back()->with('success', __('The petty cash request details are updated successfully.'));
        }
        else{
            return redirect()->route('petty-cash-management.petty-cash-requests.index')->with('error', __('Permission denied'));
        }
    }

    public function updateStatus(PettyCashRequest $pettycashrequest)
    {
        if(Auth::user()->can('approve-petty-cash-requests')){
            if ((int) $pettycashrequest->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            $validated = request()->validate([
                'status'           => 'required|in:1,2',
                'approved_amount'  => 'required_if:status,1|numeric|min:0',
                'rejection_reason' => 'required_if:status,2|nullable|string|max:1000'
            ]);

            try {
                $result = app(PettyCashApprovalService::class)->updatePettyCashRequestStatus(
                    $pettycashrequest->id,
                    creatorId(),
                    Auth::id(),
                    $validated
                );
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('update_petty_cash_request_status', $e, [
                    'request_id' => $pettycashrequest->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
            }

            if (!($result['ok'] ?? false)) {
                return redirect()->back()->with('error', $result['error'] ?? __('Something went wrong. Please try again.'));
            }

            if (($result['changed'] ?? false) === true) {
                UpdateStatusPettyCashRequest::dispatch($pettycashrequest->fresh());
            }

            $message = $validated['status'] == '1' ? __('The petty cash request has been approved.') : __('The petty cash request has been rejected.');
            return redirect()->back()->with('success', $message);
        }
        else{
            return redirect()->route('petty-cash-management.petty-cash-requests.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(PettyCashRequest $pettycashrequest)
    {
        if(Auth::user()->can('delete-petty-cash-requests')){
            if ((int) $pettycashrequest->created_by !== (int) creatorId()) {
                return redirect()->back()->with('error', __('Permission denied'));
            }

            try {
                app(PettyCashApprovalService::class)->deletePettyCashRequestWithReversal($pettycashrequest->id, creatorId(), Auth::id());
            } catch (\Throwable $e) {
                app(PettyCashApprovalService::class)->logApprovalException('delete_petty_cash_request', $e, [
                    'request_id' => $pettycashrequest->id,
                    'actor_id' => Auth::id(),
                    'tenant_id' => creatorId(),
                ]);
                return redirect()->back()->with('error', __('Something went wrong. Please try again.'));
            }

            return redirect()->back()->with('success', __('The petty cash request has been deleted.'));
        }
        else{
            return redirect()->route('petty-cash-management.petty-cash-requests.index')->with('error', __('Permission denied'));
        }
    }

    public function getCategoriesByUser(User $user)
    {
        if(Auth::user()->can('view-categories')){
            if ((int) $user->created_by !== (int) creatorId()) {
                return response()->json([], 404);
            }

            $categories = PettyCashCategory::query()
                ->where('created_by', creatorId())
                ->when(
                    Schema::hasColumn('petty_cash_categories', 'user_id'),
                    fn ($q) => $q->where('user_id', $user->id)
                )
                ->select('id', 'name')
                ->get();

            return response()->json($categories);
        }
        else{
            return response()->json([], 403);
        }
    }

    public function viewReceipt(PettyCashRequest $pettycashrequest)
    {
        $user = Auth::user();
        if ((int) $pettycashrequest->created_by !== (int) creatorId()) {
            abort(404);
        }

        $this->assertCanAccessReceipt($user, $pettycashrequest);

        if (empty($pettycashrequest->receipt_path)) {
            abort(404);
        }

        return $this->streamReceipt($pettycashrequest->receipt_path, $this->makeDownloadName($pettycashrequest->request_number, $pettycashrequest->receipt_path), false);
    }

    public function downloadReceipt(PettyCashRequest $pettycashrequest)
    {
        $user = Auth::user();
        if ((int) $pettycashrequest->created_by !== (int) creatorId()) {
            abort(404);
        }

        $this->assertCanAccessReceipt($user, $pettycashrequest);

        if (empty($pettycashrequest->receipt_path)) {
            abort(404);
        }

        return $this->streamReceipt($pettycashrequest->receipt_path, $this->makeDownloadName($pettycashrequest->request_number, $pettycashrequest->receipt_path), true);
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

    private function assertCanAccessReceipt($user, PettyCashRequest $pettycashrequest): void
    {
        if (!$user) {
            abort(403);
        }

        if (
            $user->can('manage-petty-cash-expenses') ||
            $user->can('approve-petty-cash-requests') ||
            $user->can('manage-any-petty-cash-requests')
        ) {
            return;
        }

        $isOwner = (int) $pettycashrequest->creator_id === (int) $user->id || (int) $pettycashrequest->user_id === (int) $user->id;

        if ($isOwner && ($user->can('manage-own-petty-cash-requests') || $user->can('view-petty-cash-requests'))) {
            return;
        }

        abort(403);
    }
}
