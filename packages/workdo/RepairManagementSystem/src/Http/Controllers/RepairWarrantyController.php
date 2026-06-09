<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Workdo\RepairManagementSystem\Models\RepairWarranty;
use Workdo\RepairManagementSystem\Http\Requests\StoreRepairWarrantyRequest;
use Workdo\RepairManagementSystem\Http\Requests\UpdateRepairWarrantyRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Workdo\RepairManagementSystem\Models\RepairPart;
use Workdo\RepairManagementSystem\Events\CreateRepairWarranty;
use Workdo\RepairManagementSystem\Events\UpdateRepairWarranty;
use Workdo\RepairManagementSystem\Events\DestroyRepairWarranty;

class RepairWarrantyController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-repair-warranties')){
            $repairwarranties = RepairWarranty::query()
                ->with(['repair_order', 'part'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-repair-warranties')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-repair-warranties')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('warranty_number'), function($q) {
                    $q->where(function($query) {
                    $query->where('warranty_number', 'like', '%' . request('warranty_number') . '%');
                    $query->orWhere('warranty_terms', 'like', '%' . request('warranty_number') . '%');
                    });
                })
                ->when(request('repair_order_id') && request('repair_order_id') !== '', fn($q) => $q->where('repair_order_id', request('repair_order_id')))
                ->when(request('part_id') && request('part_id') !== '', fn($q) => $q->where('part_id', request('part_id')))
                ->when(request('claim_status') !== null && request('claim_status') !== '', fn($q) => $q->where('claim_status', request('claim_status')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            // Add part names to the warranties
            $repairwarranties->getCollection()->transform(function ($warranty) {
                if ($warranty->part) {
                    $product = null;
                    if (Module_is_active('ProductService') && $warranty->part->product_id) {
                        $product = ProductServiceItem::find($warranty->part->product_id);
                    }
                    $warranty->part->name = $product ? $product->name : ($warranty->part->description ?: 'Unknown Part');
                }
                return $warranty;
            });

            return Inertia::render('RepairManagementSystem/RepairWarranties/Index', [
                'repairwarranties' => $repairwarranties,
                'repairorderrequests' => RepairOrderRequest::where('created_by', creatorId())->where('status', '!=', 6)->select('id', 'product_name as name')->get(),
                'repairparts' => RepairPart::where('created_by', creatorId())
                    ->get()
                    ->map(function($part) {
                        $product = null;
                        if (Module_is_active('ProductService') && $part->product_id) {
                            $product = ProductServiceItem::find($part->product_id);
                        }
                        return [
                            'id' => $part->id,
                            'name' => $product ? $product->name : ($part->description ?: 'Unknown Part')
                        ];
                    }),
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRepairWarrantyRequest $request)
    {
        if(Auth::user()->can('create-repair-warranties')){
            $validated = $request->validated();

            $repairwarranty = new RepairWarranty();
            $repairwarranty->warranty_number = $validated['warranty_number'];
            $repairwarranty->warranty_period = $validated['warranty_period'];
            $repairwarranty->warranty_terms = $validated['warranty_terms'];
            $repairwarranty->claim_status = $validated['claim_status'];
            $repairwarranty->repair_order_id = $validated['repair_order_id'];
            $repairwarranty->part_id = $validated['part_id'];

            $repairwarranty->creator_id = Auth::id();
            $repairwarranty->created_by = creatorId();
            $repairwarranty->save();

            CreateRepairWarranty::dispatch($request, $repairwarranty);

            return redirect()->route('repair-management-system.repair-warranties.index')->with('success', __('The warranty has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRepairWarrantyRequest $request, RepairWarranty $repair_warranty)
    {
        if(Auth::user()->can('edit-repair-warranties')){
            if ($repair_warranty->created_by == creatorId()) {
                $validated = $request->validated();

                $repair_warranty->warranty_number = $validated['warranty_number'];
                $repair_warranty->warranty_period = $validated['warranty_period'];
                $repair_warranty->warranty_terms = $validated['warranty_terms'];
                $repair_warranty->claim_status = $validated['claim_status'];
                $repair_warranty->repair_order_id = $validated['repair_order_id'];
                $repair_warranty->part_id = $validated['part_id'];

                $repair_warranty->save();

                UpdateRepairWarranty::dispatch($request, $repair_warranty);

                return back()->with('success', __('The warranty details are updated successfully.'));
            } else {
                return back()->with('error', __('Permission denied'));
            }
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(RepairWarranty $repair_warranty)
    {
        if(Auth::user()->can('delete-repair-warranties')){
            if ($repair_warranty->created_by == creatorId()) {
                DestroyRepairWarranty::dispatch($repair_warranty);
                
                $repair_warranty->delete();

                return back()->with('success', __('The warranty has been deleted.'));
            } else {
                return back()->with('error', __('Permission denied'));
            }
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function getPartsByRepairOrder($repair_orderId)
    {
        if(Auth::user()->can('manage-repair-warranties')){
            $parts = RepairPart::where('repair_id', $repair_orderId)
                ->where('created_by', creatorId())
                ->get()
                ->map(function($part) {
                    $product = null;
                    if (Module_is_active('ProductService') && $part->product_id) {
                        $product = ProductServiceItem::find($part->product_id);
                    }
                    return [
                        'id' => $part->id,
                        'name' => $product ? $product->name : ($part->description ?: 'Unknown Part')
                    ];
                });

            return response()->json($parts);
        }
        else{
            return response()->json([], 403);
        }
    }
}