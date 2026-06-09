<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Workdo\RepairManagementSystem\Models\RepairInvoice;
use Workdo\RepairManagementSystem\Models\RepairInvoicePayment;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Workdo\RepairManagementSystem\Events\CreateRepairInvoice;
use Workdo\RepairManagementSystem\Events\DestroyRepairInvoice;
use Workdo\RepairManagementSystem\Models\RepairPart;

class RepairInvoiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-repair-invoices')) {
            $repairinvoices = RepairInvoice::query()
                ->with(['repair_order.repairParts'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-repair-invoices')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-repair-invoices')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('invoice_id'), function ($q) {
                    $q->where('invoice_id', request('invoice_id'));
                })
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('sort'), function ($q) {
                    $sort = request('sort');
                    $direction = request('direction', 'asc');

                    if ($sort === 'repair_order.product_name') {
                        $q->join('repair_order_requests', 'repair_invoices.repair_id', '=', 'repair_order_requests.id')
                            ->orderBy('repair_order_requests.product_name', $direction)
                            ->select('repair_invoices.*');
                    } else {
                        $q->orderBy($sort, $direction);
                    }
                }, fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            // Calculate correct total amounts with tax fallback logic and due amounts
            $repairinvoices->getCollection()->transform(function ($invoice) {
                if ($invoice->repair_order) {
                    $repair_order = $invoice->repair_order;
                    $repair_parts = $repair_order->repairParts ?? collect();

                    // Apply tax fallback logic for each part
                    foreach ($repair_parts as $part) {
                        if (empty($part->tax) && Module_is_active('ProductService') && $part->product_id) {
                            $product = ProductServiceItem::find($part->product_id);
                            if ($product && $product->tax_ids) {
                                $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                                if (!empty($tax_ids)) {
                                    $part->tax = implode(',', $tax_ids);
                                    $part->save();
                                }
                            }
                        }
                    }

                    // Calculate correct total and update stored value
                    $calculated_total = $repair_order->getTotal($invoice->repair_charge);
                    if ($invoice->total_amount != $calculated_total) {
                        $invoice->total_amount = $calculated_total;
                        $invoice->save();
                    }

                    // Calculate due amount
                    $total_paid = $invoice->payments()->sum('amount');
                    $invoice->due_amount = $invoice->total_amount - $total_paid;
                }
                return $invoice;
            });

            return Inertia::render('RepairManagementSystem/RepairInvoices/Index', [
                'repairinvoices' => $repairinvoices,
                'repairorderrequests' => RepairOrderRequest::where('created_by', creatorId())->select('id', 'product_name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    private function repairInvoiceNumber()
    {
        $latest = RepairInvoice::where('created_by', creatorId())
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$latest || !$latest->invoice_id) {
            return '#INVO001';
        }

        $lastNumber = (int) str_replace('#INVO', '', $latest->invoice_id);
        $nextNumber = $lastNumber + 1;

        return '#INVO' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function createInvoice(Request $request, RepairOrderRequest $repair_order)
    {
        if (Auth::user()->can('create-repair-invoices')) {
            if ($repair_order->created_by !== creatorId()) {
                return back()->with('error', __('Permission denied'));
            }

            $validated = $request->validate([
                'repair_charge' => 'required|numeric'
            ]);

            $repair_order->update(['status' => 7]);
            $repair_order->load('repairParts');

            $totalAmount = $repair_order->getTotal($validated['repair_charge']);

            $repairinvoice = new RepairInvoice();
            $repairinvoice->invoice_id = $this->repairInvoiceNumber();
            $repairinvoice->repair_id = $repair_order->id;
            $repairinvoice->repair_charge = $validated['repair_charge'];
            $repairinvoice->total_amount = $totalAmount;
            $repairinvoice->status = '0';
            $repairinvoice->creator_id = Auth::id();
            $repairinvoice->created_by = creatorId();
            $repairinvoice->save();

            CreateRepairInvoice::dispatch($request, $repairinvoice);

            return back()->with('success', __('The Invoice has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function show($id)
    {
        if (Auth::user()->can('view-repair-invoices')) {
            $repairinvoice = RepairInvoice::where('id', $id)
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-repair-invoices')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-repair-invoices')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->first();

            if (!$repairinvoice) {
                return back()->with('error', __('Permission denied'));
            }

            $repairinvoice->load(['repair_order.repairParts', 'payments']);
            $repair_order = $repairinvoice->repair_order;

            if (!$repair_order) {
                return back()->with('error', __('Repair order not found'));
            }

            // Ensure repairParts are loaded
            $repair_order->load('repairParts');

            // Load product names for parts
            if (Module_is_active('ProductService')) {
                foreach ($repair_order->repairParts as $part) {
                    if ($part->product_id) {
                        $product = ProductServiceItem::find($part->product_id);
                        $part->product_name = $product ? $product->name : ($part->description ?: 'Part #' . $part->id);
                    } else {
                        $part->product_name = $part->description ?: 'Part #' . $part->id;
                    }
                }
            } else {
                foreach ($repair_order->repairParts as $part) {
                    $part->product_name = $part->description ?: 'Part #' . $part->id;
                }
            }

            $repair_parts = $repair_order->repairParts ?? collect();

            // Calculate individual part taxes for display
            foreach ($repair_parts as $part) {
                $itemTotal = ($part->price * $part->quantity) - $part->discount;
                $taxRate = 0;

                // If tax field is empty, get tax from product configuration
                if (empty($part->tax) && Module_is_active('ProductService') && $part->product_id) {
                    $product = ProductServiceItem::find($part->product_id);
                    if ($product && $product->tax_ids) {
                        $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                        if (!empty($tax_ids)) {
                            $part->tax = implode(',', $tax_ids); // Set tax from product
                        }
                    }
                }

                // Calculate tax rate
                if (!empty($part->tax) && Module_is_active('ProductService')) {
                    $taxRate = $repair_order->totalTaxRate($part->tax);
                }

                $part->tax_rate = $taxRate;
                $part->tax_amount = $taxRate > 0 ? ($taxRate / 100) * $itemTotal : 0;
                $part->item_total_with_tax = $itemTotal + $part->tax_amount;
            }

            // Force recalculate tax from individual parts to match edit page
            $manual_tax_total = 0;
            foreach ($repair_parts as $part) {
                $manual_tax_total += $part->tax_amount ?? 0;
            }

            // Calculate totals using old system method
            $subtotal = $repair_order->getSubTotal();
            $total_discount = $repair_order->getTotalDiscount();
            $total_tax = $manual_tax_total; // Use manually calculated tax
            $calculated_total = ($subtotal - $total_discount + $total_tax + $repairinvoice->repair_charge);

            // Always use the calculated total to match edit page
            $repairinvoice->total_amount = $calculated_total;
            $repairinvoice->save();

            // Calculate total_paid separately (don't assign to model)
            $total_paid = $repairinvoice->payments()->sum('amount');

            return Inertia::render('RepairManagementSystem/RepairInvoices/Show', [
                'repairinvoice' => array_merge($repairinvoice->toArray(), ['total_paid' => $total_paid]),
                'repair_order' => $repair_order,
                'repair_parts' => $repair_parts,
                'subtotal' => $subtotal,
                'total_discount' => $total_discount,
                'total_tax' => $total_tax,
                'total_amount' => $calculated_total
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function print(RepairInvoice $repairinvoice)
    {
        if (Auth::user()->can('view-repair-invoices')) {
            // Verify invoice belongs to current user
            if ($repairinvoice->created_by !== creatorId()) {
                return back()->with('error', __('Permission denied'));
            }

            $repairinvoice->load(['repair_order.repairParts', 'payments']);
            $repair_order = $repairinvoice->repair_order;

            if (!$repair_order) {
                return back()->with('error', __('Repair order not found'));
            }

            // Load product names for parts
            if (Module_is_active('ProductService')) {
                foreach ($repair_order->repairParts as $part) {
                    if ($part->product_id) {
                        $product = ProductServiceItem::find($part->product_id);
                        $part->product_name = $product ? $product->name : ($part->description ?: 'Part #' . $part->id);
                    } else {
                        $part->product_name = $part->description ?: 'Part #' . $part->id;
                    }
                }
            } else {
                foreach ($repair_order->repairParts as $part) {
                    $part->product_name = $part->description ?: 'Part #' . $part->id;
                }
            }

            $repair_parts = $repair_order->repairParts ?? collect();

            // Calculate individual part taxes for display (same as show method)
            foreach ($repair_parts as $part) {
                $itemTotal = ($part->price * $part->quantity) - $part->discount;
                $taxRate = 0;

                // If tax field is empty, get tax from product configuration
                if (empty($part->tax) && Module_is_active('ProductService') && $part->product_id) {
                    $product = ProductServiceItem::find($part->product_id);
                    if ($product && $product->tax_ids) {
                        $tax_ids = is_string($product->tax_ids) ? json_decode($product->tax_ids, true) : $product->tax_ids;
                        if (!empty($tax_ids)) {
                            $part->tax = implode(',', $tax_ids); // Set tax from product
                        }
                    }
                }

                // Calculate tax rate
                if (!empty($part->tax) && Module_is_active('ProductService')) {
                    $taxRate = $repair_order->totalTaxRate($part->tax);
                }

                $part->tax_rate = $taxRate;
                $part->tax_amount = $taxRate > 0 ? ($taxRate / 100) * $itemTotal : 0;
                $part->item_total_with_tax = $itemTotal + $part->tax_amount;
            }

            // Force recalculate tax from individual parts to match show page
            $manual_tax_total = 0;
            foreach ($repair_parts as $part) {
                $manual_tax_total += $part->tax_amount ?? 0;
            }

            // Calculate totals using old system method (same as show method)
            $subtotal = $repair_order->getSubTotal();
            $total_discount = $repair_order->getTotalDiscount();
            $total_tax = $manual_tax_total; // Use manually calculated tax
            $calculated_total = ($subtotal - $total_discount + $total_tax + $repairinvoice->repair_charge);

            // Always use the calculated total to match show page
            $repairinvoice->total_amount = $calculated_total;
            $repairinvoice->save();

            return Inertia::render('RepairManagementSystem/RepairInvoices/Print', [
                'repairinvoice' => $repairinvoice,
                'repair_order' => $repair_order,
                'repair_parts' => $repair_parts,
                'subtotal' => $subtotal,
                'total_discount' => $total_discount,
                'total_tax' => $total_tax,
                'total_amount' => $calculated_total
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }



    public function makePayment(Request $request, RepairInvoice $repairinvoice)
    {
        if (Auth::user()->can('make-payment-repair-invoices')) {

            $amount = $request->query('amount');
            $note = $request->query('note');
            if (!$amount || !is_numeric($amount) || $amount <= 0) {
                return redirect()->route('repair-management-system.repair-invoices.show', $repairinvoice->id)->with('error', __('Invalid payment amount'));
            }

            $validated = ['amount' => (float) $amount, 'note' => $note];

            $repairinvoice->load('repair_order');
            $totalAmount = $repairinvoice->total_amount;
            $paidAmount = $repairinvoice->payments()->sum('amount');
            $due = $totalAmount - $paidAmount;

            if ($due < $validated['amount']) {
                return redirect()->route('repair-management-system.repair-invoices.show', $repairinvoice->id)->with('error', __('Amount must be smaller or equal to the due amount.'));
            }

            $repair_invoice_payment = new RepairInvoicePayment();
            $repair_invoice_payment->invoice_id = $repairinvoice->id;
            $repair_invoice_payment->repair_id = $repairinvoice->repair_id;
            $repair_invoice_payment->amount = $validated['amount'];
            $repair_invoice_payment->notes = $validated['note'];
            $repair_invoice_payment->payment_method = 'Manual';
            $repair_invoice_payment->creator_id = Auth::id();
            $repair_invoice_payment->created_by = creatorId();
            $repair_invoice_payment->save();

            if (($due == $validated['amount'])) {
                $repairinvoice->status = 2;
                $repairinvoice->save();
                // Quantity Minus in ProductService
                $repair_parts = RepairPart::where('repair_id', $repairinvoice->repair_id)->get();
                for ($i = 0; $i < count($repair_parts); $i++) {
                    if (Module_is_active('ProductService')) {
                        RepairPart::total_quantity('minus', $repair_parts[$i]['quantity'], $repair_parts[$i]['product_id']);
                    }
                }
            } else {
                $repairinvoice->status = 1;
                $repairinvoice->save();
            }

            return redirect()->route('repair-management-system.repair-invoices.show', $repairinvoice->id)->with('success', __('The Payment has been added successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function paymentHistory(RepairInvoice $repairinvoice)
    {
        if (Auth::user()->can('view-repair-invoices')) {
            if ($repairinvoice->created_by !== creatorId()) {
                return response()->json(['error' => __('Permission denied')], 403);
            }

            $payments = $repairinvoice->payments()->orderBy('created_at', 'desc')->get();

            return response()->json([
                'payments' => $payments,
                'invoice_id' => $repairinvoice->invoice_id
            ]);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function destroy(RepairInvoice $repairinvoice)
    {
        if (Auth::user()->can('delete-repair-invoices')) {
            DestroyRepairInvoice::dispatch($repairinvoice);
            $repairinvoice->delete();

            return back()->with('success', __('The repair invoice has been deleted'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}