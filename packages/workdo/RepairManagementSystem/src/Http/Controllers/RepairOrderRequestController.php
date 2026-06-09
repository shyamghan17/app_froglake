<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\Http\Requests\StoreRepairOrderRequestRequest;
use Workdo\RepairManagementSystem\Http\Requests\UpdateRepairOrderRequestRequest;
use Inertia\Inertia;
use Workdo\RepairManagementSystem\Models\RepairMovementHistory;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Workdo\RepairManagementSystem\Models\RepairPart;
use Workdo\RepairManagementSystem\Models\RepairTechnician;
use Workdo\RepairManagementSystem\Events\CreateRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\UpdateRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\DestroyRepairOrderRequest;
use Workdo\RepairManagementSystem\Models\RepairInvoice;
use Workdo\RepairManagementSystem\Models\RepairInvoicePayment;

class RepairOrderRequestController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-repair-order-requests')) {
            $repairorderrequests = RepairOrderRequest::query()
                ->with(['invoice' => function($query) {
                    $query->where('created_by', creatorId());
                }])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-repair-order-requests')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-repair-order-requests')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('product_name'), function($q) {
                    $q->where(function($query) {
                    $query->where('product_name', 'like', '%' . request('product_name') . '%');
                    $query->orWhere('customer_name', 'like', '%' . request('product_name') . '%');
                    $query->orWhere('customer_email', 'like', '%' . request('product_name') . '%');
                    });
                })
                ->when(request('repair_technician') && request('repair_technician') !== '', fn($q) => $q->where('repair_technician', request('repair_technician')))
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', (int) request('status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('RepairManagementSystem/RepairOrderRequests/Index', [
                'repairorderrequests' => $repairorderrequests,
                'repairtechnicians' => RepairTechnician::where('created_by', creatorId())->select('id', 'name')->get(),
                'repairstatuses' => RepairOrderRequest::getStatuses(),
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StoreRepairOrderRequestRequest $request)
    {
        if (Auth::user()->can('create-repair-order-requests')) {
            $validated = $request->validated();

            $repair_order_request = new RepairOrderRequest();
            $repair_order_request->product_name = $validated['product_name'];
            $repair_order_request->product_quantity = $validated['product_quantity'];
            $repair_order_request->customer_name = $validated['customer_name'];
            $repair_order_request->customer_email = $validated['customer_email'];
            $repair_order_request->customer_mobile_no = $validated['customer_mobile_no'];
            $repair_order_request->date = $validated['date'];
            $repair_order_request->expiry_date = $validated['expiry_date'];
            $repair_order_request->repair_technician = $validated['repair_technician'];
            $repair_order_request->location = 'Main Location';
            $repair_order_request->created_by = creatorId();
            $repair_order_request->creator_id = Auth::id();
            $repair_order_request->save();

            CreateRepairOrderRequest::dispatch($request, $repair_order_request);

            return redirect()->route('repair-management-system.repair-order-requests.index')->with('success', __('The order request has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function update(UpdateRepairOrderRequestRequest $request, RepairOrderRequest $repair_order_request)
    {
        if (Auth::user()->can('edit-repair-order-requests')) {
            if ($repair_order_request->created_by == creatorId()) {
                $validated = $request->validated();

                $repair_order_request->product_name = $validated['product_name'];
                $repair_order_request->product_quantity = $validated['product_quantity'];
                $repair_order_request->customer_name = $validated['customer_name'];
                $repair_order_request->customer_email = $validated['customer_email'];
                $repair_order_request->customer_mobile_no = $validated['customer_mobile_no'];
                $repair_order_request->date = $validated['date'];
                $repair_order_request->expiry_date = $validated['expiry_date'];
                $repair_order_request->repair_technician = $validated['repair_technician'];
                $repair_order_request->created_by = creatorId();
                $repair_order_request->save();

                UpdateRepairOrderRequest::dispatch($request, $repair_order_request);

                return back()->with('success', __('The order request details are updated successfully.'));
            } else {
                return back()->with('error', __('Permission denied.'));
            }
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(RepairOrderRequest $repair_order_request)
    {
        if (Auth::user()->can('delete-repair-order-requests')) {
            if ($repair_order_request->created_by == creatorId()) {
                // Delete movement histories
                RepairMovementHistory::where('repair_order_request_id', $repair_order_request->id)
                    ->where('created_by', creatorId())
                    ->delete();
                
                // Delete repair parts
                RepairPart::where('repair_id', $repair_order_request->id)
                    ->where('created_by', creatorId())
                    ->delete();
                
                // Delete invoice and payments if exists
                $invoice = RepairInvoice::where('repair_id', $repair_order_request->id)
                    ->where('created_by', creatorId())
                    ->first();
                if ($invoice) {
                    // Delete invoice payments first
                    RepairInvoicePayment::where('repair_id', $repair_order_request->id)
                        ->where('created_by', creatorId())
                        ->delete();
                    // Delete invoice
                    $invoice->delete();
                }
                
                DestroyRepairOrderRequest::dispatch($repair_order_request);
                
                $repair_order_request->delete();
                return back()->with('success', __('The order request has been deleted.'));
            } else {
                return back()->with('error', __('Permission denied.'));
            }
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function repairOrderStepsChange(RepairOrderRequest $repair_order_request, $response)
    {
        if(Auth::user()->can('update-status-repair-order-requests')){
            // Verify repair order belongs to current user
            if ($repair_order_request->created_by !== creatorId()) {
                return back()->with('error', __('Permission denied'));
            }
            
            try {
                $repair_order_request->status = $response;
                if ($response == 1) {
                    RepairMovementHistory::movementHistoryStore($repair_order_request->id, 'Main Location', 'Workshop Location', 'Repair');
                    $repair_order_request->location = 'Workshop Location';
                    $msg = 'Repair Started!';
                } elseif ($response == 2) {
                    RepairMovementHistory::movementHistoryStore($repair_order_request->id, 'Workshop Location', 'Waiting For Testing Location', 'Repair');
                    $repair_order_request->location = 'Waiting For Testing Location';
                    $msg = 'Repair Ended!';
                } elseif ($response == 3) {
                    RepairMovementHistory::movementHistoryStore($repair_order_request->id, 'Waiting For Testing Location', 'Testing Location', 'Testing');
                    $repair_order_request->location = 'Testing Location';
                    $msg = 'Testing Started!';
                } elseif ($response == 4) {
                    RepairMovementHistory::movementHistoryStore($repair_order_request->id, 'Testing Location', 'Finish Location', 'Testing');
                    $repair_order_request->location = 'Finish Location';
                    $msg = 'Testing Ended!';
                } elseif ($response == 5) {
                    RepairMovementHistory::movementHistoryStore($repair_order_request->id, $repair_order_request->location, 'Irrepairable Location', 'Irrepairable');
                    $repair_order_request->location = 'Irrepairable Location';
                    $msg = 'Product Irrepairabled';
                } elseif ($response == 6) {
                    RepairMovementHistory::movementHistoryStore($repair_order_request->id, $repair_order_request->location, 'Cancel Location', 'Cancel');
                    $repair_order_request->location = 'Cancel Location';
                    $msg = 'Product Cancelled';
                }
                $repair_order_request->save();
                return back()->with('success', $msg);
            } catch (\Exception $e) {
                return back()->with('error', __('Something went wrong!'));
            }
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }
}