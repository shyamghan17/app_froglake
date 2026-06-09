<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\ProductService\Models\ProductServiceTax;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Workdo\RepairManagementSystem\Models\RepairPart;
use Workdo\RepairManagementSystem\Http\Requests\StoreRepairProductPartRequest;
use Workdo\RepairManagementSystem\Events\CreateRepairProductPart;
use Workdo\RepairManagementSystem\Events\UpdateRepairProductPart;
use Workdo\RepairManagementSystem\Events\DestroyRepairProductPart;
use Workdo\RepairManagementSystem\Models\RepairInvoice;

class RepairProductPartController extends Controller
{
    public function index($id)
    {
        if (Auth::user()->can('manage-repair-product-parts')) {
            $repair_order_request = RepairOrderRequest::where('id', $id)
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-repair-order-requests')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-repair-order-requests')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->first();
            
            if (!$repair_order_request) {
                return redirect()->route('repair-management-system.repair-order-requests.index')->with('error', __('Permission denied'));
            }
            if (Module_is_active('ProductService')) {
                $product_parts = ProductServiceItem::where('created_by', creatorId())
                    ->where('type', 'part')
                    ->get()
                    ->map(function($product) {
                        $taxes = [];
                        if ($product->tax_ids) {
                            $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                            if (!empty($tax_ids)) {
                                $taxes = ProductServiceTax::whereIn('id', $tax_ids)
                                    ->get()
                                    ->map(function($tax) {
                                        return [
                                            'id' => $tax->id,
                                            'tax_name' => $tax->tax_name,
                                            'rate' => $tax->rate
                                        ];
                                    })
                                    ->toArray();
                            }
                        }
                        
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sale_price' => $product->sale_price ?? 0,
                            'unit' => $product->unit ?? '',
                            'taxes' => $taxes
                        ];
                    })
                    ->toArray();
                $product_parts_count = count($product_parts);
            } else {
                $product_parts = [];
                $product_parts_count = 0;
            }
            
            $existing_parts = RepairPart::where('repair_id', $id)
                ->where('created_by', creatorId())
                ->get()
                ->map(function($part) {
                    $taxPercentage = 0;
                    $discountPercentage = 0;
                    
                    if (Module_is_active('ProductService') && $part->product_id) {
                        $product = ProductServiceItem::find($part->product_id);
                        if ($product && $product->tax_ids) {
                            $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                            if (!empty($tax_ids)) {
                                $all_taxes = ProductServiceTax::whereIn('id', $tax_ids)->get();
                                foreach ($all_taxes as $tax) {
                                    $taxPercentage += $tax->rate;
                                }
                            }
                        }
                    }
                    
                    // Calculate discount percentage from discount amount
                    $subtotal = $part->quantity * $part->price;
                    if ($subtotal > 0) {
                        $discountPercentage = ($part->discount / $subtotal) * 100;
                    }
                    
                    // Calculate amounts
                    $discountAmount = $part->discount;
                    $afterDiscount = $subtotal - $discountAmount;
                    $taxAmount = ($afterDiscount * $taxPercentage) / 100;
                    $totalAmount = $afterDiscount + $taxAmount;
                    
                    return [
                        'id' => $part->id,
                        'product_id' => $part->product_id,
                        'quantity' => $part->quantity,
                        'unit_price' => $part->price,
                        'discount_percentage' => $discountPercentage,
                        'discount_amount' => $discountAmount,
                        'tax_percentage' => $taxPercentage,
                        'tax_amount' => $taxAmount,
                        'total_amount' => $totalAmount
                    ];
                })
                ->toArray();
            
            $repairstatuses = RepairOrderRequest::getStatuses();
            
            return Inertia::render('RepairManagementSystem/RepairProductParts/Index', [
                'repairOrderRequest' => [
                    'id' => $repair_order_request->id,
                    'product_name' => $repair_order_request->product_name,
                    'product_quantity' => $repair_order_request->product_quantity,
                    'customer_name' => $repair_order_request->customer_name,
                    'customer_email' => $repair_order_request->customer_email,
                    'customer_mobile_no' => $repair_order_request->customer_mobile_no,
                    'location' => $repair_order_request->location,
                    'date' => $repair_order_request->date,
                    'expiry_date' => $repair_order_request->expiry_date,
                    'status' => $repair_order_request->status
                ],
                'productParts' => $product_parts,
                'product_parts_count' => $product_parts_count,
                'existingParts' => $existing_parts,
                'repairstatuses' => $repairstatuses,
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }

    public function store(StoreRepairProductPartRequest $request)
    {
        if (Module_is_active('ProductService')) {
            if (Auth::user()->can('manage-repair-product-parts')) {
                // Verify repair order belongs to current user
                $repair_order = RepairOrderRequest::where('id', $request->repair_id)
                    ->where('created_by', creatorId())
                    ->first();
                
                if (!$repair_order) {
                    return redirect()->route('repair-management-system.repair-order-requests.index')->with('error', __('Permission denied'));
                }
                
                $products = $request->items;

                for ($i = 0; $i < count($products); $i++) {
                    if (isset($products[$i]['id']) && $products[$i]['id']) {
                        $repair_part = RepairPart::where('id', $products[$i]['id'])
                            ->where('created_by', creatorId())
                            ->first();
                        if ($repair_part) {
                            $product = ProductServiceItem::find($products[$i]['item']);
                            $tax = isset($products[$i]['tax']) ? $products[$i]['tax'] : '';
                            if (empty($tax) && $product && $product->tax_ids) {
                                $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                                $tax = !empty($tax_ids) ? implode(',', $tax_ids) : '';
                            }
                            $repair_part->product_id = $products[$i]['item'];
                            $repair_part->quantity = $products[$i]['quantity'];
                            $repair_part->tax = $tax;
                            $repair_part->discount = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                            $repair_part->price = $products[$i]['price'];
                            $repair_part->description = $product ? ($product->description ?? '') : '';
                            $repair_part->save();
                            
                            UpdateRepairProductPart::dispatch($request, $repair_part);
                        }
                    } else {
                        $product = ProductServiceItem::find($products[$i]['item']);
                        $tax = isset($products[$i]['tax']) ? $products[$i]['tax'] : '';
                        if (empty($tax) && $product && $product->tax_ids) {
                            $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                            $tax = !empty($tax_ids) ? implode(',', $tax_ids) : '';
                        }
                        $repair_part = new RepairPart();
                        $repair_part->repair_id = $request->repair_id;
                        $repair_part->product_id = $products[$i]['item'];
                        $repair_part->quantity = $products[$i]['quantity'];
                        $repair_part->tax = $tax;
                        $repair_part->discount = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                        $repair_part->price = $products[$i]['price'];
                        $repair_part->description = $product ? ($product->description ?? '') : '';
                        $repair_part->creator_id = Auth::id();
                        $repair_part->created_by = creatorId();
                        $repair_part->save();
                        
                        CreateRepairProductPart::dispatch($request, $repair_part);
                    }
                }

                // Update invoice total if invoice exists
                $invoice = RepairInvoice::where('repair_id', $request->repair_id)
                    ->where('created_by', creatorId())
                    ->first();
                
                if ($invoice) {
                    $repair_order->load('repairParts');
                    $newTotal = $repair_order->getTotal($invoice->repair_charge);
                    $invoice->update(['total_amount' => $newTotal]);
                }
                
                return redirect()->route('repair-management-system.repair-order-requests.index')->with('success', __('The repair parts has been created successfully.'));
            } else {
                return back()->with('error', __('Permission denied.'));
            }
        } else {
            return back()->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function product(Request $request)
    {
        if(Auth::user()->can('manage-repair-product-parts')){
            $product = ProductServiceItem::find($request->product_id);
            
            if ($product) {
                $data['product'] = [
                    'id' => $product->id,
                    'sale_price' => $product->sale_price ?? 0,
                    'description' => $product->description ?? ''
                ];
                $data['unit'] = $product->unit ?? '';
                
                $taxRate = 0;
                $taxes = '';
                
                if ($product->tax_ids) {
                    $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                    if (!empty($tax_ids)) {
                        $all_taxes = ProductServiceTax::whereIn('id', $tax_ids)->get();
                        foreach ($all_taxes as $tax) {
                            $taxRate += $tax->rate;
                            $taxes .= '<span class="badge bg-primary p-2 px-3 mt-1 me-1">' . $tax->tax_name . ' (' . $tax->rate . '%)</span>';
                        }
                    }
                }
                
                $data['taxRate'] = $taxRate;
                $data['taxes'] = $taxes;
                $data['totalAmount'] = $product->sale_price ?? 0;
            } else {
                $data = ['error' => __('Product not found')];
            }

            return json_encode($data);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('manage-repair-product-parts')) {
            $repair_part = RepairPart::where('id', $id)
                ->where('created_by', creatorId())
                ->first();
            
            if ($repair_part) {
                DestroyRepairProductPart::dispatch($repair_part);
                
                $repair_part->delete();
                return response()->json(['success' => true]);
            }
            
            return response()->json(['error' => __('Part not found')], 404);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }
}