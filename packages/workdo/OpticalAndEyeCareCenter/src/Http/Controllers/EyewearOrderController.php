<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrderItem;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrderItemTax;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\StoreEyewearOrderRequest;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\UpdateEyewearOrderRequest;
use Workdo\OpticalAndEyeCareCenter\Events\CreateEyewearOrder;
use Workdo\OpticalAndEyeCareCenter\Events\UpdateEyewearOrder;
use Workdo\OpticalAndEyeCareCenter\Events\DestroyEyewearOrder;
use App\Models\Warehouse;
use Workdo\ProductService\Models\ProductServiceItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Workdo\OpticalAndEyeCareCenter\Events\PostEyewearOrder;

class EyewearOrderController extends Controller
{
    private function checkOrderAccess(EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('manage-any-eyewear-orders')) {
            return true;
        } elseif(Auth::user()->can('manage-own-eyewear-orders')) {
            if($eyewearOrder->creator_id != Auth::id()) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function index(Request $request)
    {
        if(Auth::user()->can('manage-eyewear-orders')){
            $query = EyewearOrder::with(['patient', 'items.product'])
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-eyewear-orders')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-eyewear-orders')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                });

            // Apply filters
            if ($request->patient_id) {
                $query->where('patient_id', $request->patient_id);
            }
            if ($request->payment_status) {
                $query->where('payment_status', $request->payment_status);
            }
            if ($request->search) {
                $query->where('order_number', 'like', '%' . $request->search . '%');
            }
            if ($request->date_range) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $query->whereBetween('order_date', [$dates[0], $dates[1]]);
                }
            }

            // Apply sorting
            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');

            $allowedSortFields = ['order_number', 'order_date', 'delivery_date', 'subtotal', 'tax_amount', 'total_amount', 'balance_amount', 'payment_status', 'created_at'];
            if (!in_array($sortField, $allowedSortFields) || empty($sortField)) {
                $sortField = 'created_at';
            }

            $query->orderBy($sortField, $sortDirection);

            $perPage = $request->get('per_page', 10);
            $orders = $query->paginate($perPage);

            $patients = EyePatient::where('created_by', creatorId())->select('id', 'patient_name', 'contact_no')->get();
            $warehouses = Warehouse::where('is_active', true)->where('created_by', creatorId())->select('id', 'name')->get();

            return Inertia::render('OpticalAndEyeCareCenter/EyewearOrders/Index', [
                'orders' => $orders,
                'patients' => $patients,
                'warehouses' => $warehouses,
                'filters' => $request->only(['patient_id', 'payment_status', 'search', 'date_range'])
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('create-eyewear-orders')){
            $patients = EyePatient::where('created_by', creatorId())->select('id', 'patient_name', 'contact_no')->get();
            $warehouses = Warehouse::where('is_active', true)->where('created_by', creatorId())->select('id', 'name', 'address')->get();

            return Inertia::render('OpticalAndEyeCareCenter/EyewearOrders/Create', [
                'patients' => $patients,
                'warehouses' => $warehouses,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreEyewearOrderRequest $request)
    {
        if(Auth::user()->can('create-eyewear-orders')){
            $totals = $this->calculateTotals($request->items);

            $order = new EyewearOrder();
            $order->order_number = $this->generateOrderNumber();
            $order->order_date = $request->order_date;
            $order->patient_id = $request->patient_id;
            $order->warehouse_id = $request->warehouse_id;
            $order->delivery_date = $request->delivery_date;
            $order->payment_method = $request->payment_method;
            $order->extra_charge = $request->extra_charge ?? 0;
            $order->prescription_details = $request->prescription_details;
            $order->special_notes = $request->special_notes;
            $order->subtotal = $totals['subtotal'];
            $order->tax_amount = $totals['tax_amount'];
            $order->discount_amount = $totals['discount_amount'];
            $order->total_amount = $totals['total_amount'] + ($request->extra_charge ?? 0);
            $order->balance_amount = $totals['total_amount'] + ($request->extra_charge ?? 0);
            $order->creator_id = Auth::id();
            $order->created_by = creatorId();
            $order->save();
            // Create order items
            $this->createOrderItems($order->id, $request->items);

            // Dispatch event for packages to handle their fields
            CreateEyewearOrder::dispatch($request, $order);

            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('success', __('The eyewear order has been created successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
        }
    }

    public function show(EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('view-eyewear-orders') && $eyewearOrder->created_by == creatorId()){
            if(!$this->checkOrderAccess($eyewearOrder)) {
                return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
            }

            $eyewearOrder->load(['patient', 'items.product', 'items.taxes', 'items.eyewearItem']);

            return Inertia::render('OpticalAndEyeCareCenter/EyewearOrders/Show', [
                'eyewearorder' => $eyewearOrder
            ]);
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
        }
    }

    public function edit(EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('edit-eyewear-orders') && $eyewearOrder->created_by == creatorId()){
            if(!$this->checkOrderAccess($eyewearOrder)) {
                return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
            }

            $eyewearOrder->load(['items.taxes', 'items.eyewearItem']);

            $patients = EyePatient::where('created_by', creatorId())->select('id', 'patient_name', 'contact_no')->get();
            $warehouses = Warehouse::where('is_active', true)->where('created_by', creatorId())->select('id', 'name', 'address')->get();

            return Inertia::render('OpticalAndEyeCareCenter/EyewearOrders/Edit', [
                'order' => $eyewearOrder,
                'patients' => $patients,
                'warehouses' => $warehouses,
            ]);
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateEyewearOrderRequest $request, EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('edit-eyewear-orders') && $eyewearOrder->created_by == creatorId()){
            $totals = $this->calculateTotals($request->items);

            $eyewearOrder->order_date = $request->order_date;
            $eyewearOrder->patient_id = $request->patient_id;
            $eyewearOrder->warehouse_id = $request->warehouse_id;
            $eyewearOrder->delivery_date = $request->delivery_date;
            $eyewearOrder->payment_method = $request->payment_method;
            $eyewearOrder->extra_charge = $request->extra_charge ?? 0;
            $eyewearOrder->prescription_details = $request->prescription_details;
            $eyewearOrder->special_notes = $request->special_notes;
            $eyewearOrder->subtotal = $totals['subtotal'];
            $eyewearOrder->tax_amount = $totals['tax_amount'];
            $eyewearOrder->discount_amount = $totals['discount_amount'];
            $eyewearOrder->total_amount = $totals['total_amount'] + ($request->extra_charge ?? 0);
            $eyewearOrder->balance_amount = $totals['total_amount'] + ($request->extra_charge ?? 0) - $eyewearOrder->paid_amount;
            $eyewearOrder->save();

            // Delete existing items and recreate
            $eyewearOrder->items()->delete();
            $this->createOrderItems($eyewearOrder->id, $request->items);

            // Dispatch event for packages to handle their fields
            UpdateEyewearOrder::dispatch($request, $eyewearOrder);

            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('success', __('The eyewear order details are updated successfully.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('delete-eyewear-orders')){
            DestroyEyewearOrder::dispatch($eyewearOrder);

            $eyewearOrder->delete();

            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('success', __('The eyewear order has been deleted.'));
        }
        else{
            return redirect()->route('optical-and-eye-care-center.eyewear-orders.index')->with('error', __('Permission denied'));
        }
    }

    private function calculateTotals($items)
    {
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $discountAmount = ($lineTotal * ($item['discount_percentage'] ?? 0)) / 100;
            $afterDiscount = $lineTotal - $discountAmount;
            $taxAmount = ($afterDiscount * ($item['tax_percentage'] ?? 0)) / 100;

            $subtotal += $lineTotal;
            $totalDiscount += $discountAmount;
            $totalTax += $taxAmount;
        }

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
            'total_amount' => $subtotal + $totalTax - $totalDiscount
        ];
    }

    private function createOrderItems($orderId, $items)
    {
        foreach ($items as $itemData) {

            $item = new EyewearOrderItem();
            $item->order_id = $orderId;
            $item->product_id = $itemData['product_id'];
            $item->item_type = $itemData['item_type'] ?? 'standard';
            $item->quantity = $itemData['quantity'];
            $item->unit_price = $itemData['unit_price'];
            $item->discount_percentage = $itemData['discount_percentage'] ?? 0;
            $item->discount_amount = ($itemData['quantity'] * $itemData['unit_price'] * ($itemData['discount_percentage'] ?? 0)) / 100;
            $item->tax_percentage = $itemData['tax_percentage'] ?? 0;
            $item->tax_amount = (($itemData['quantity'] * $itemData['unit_price'] - $item->discount_amount) * ($itemData['tax_percentage'] ?? 0)) / 100;
            $item->total_amount = ($itemData['quantity'] * $itemData['unit_price']) - $item->discount_amount + $item->tax_amount;
            $item->save();

            // Store individual taxes
            if (isset($itemData['taxes']) && is_array($itemData['taxes'])) {
                foreach ($itemData['taxes'] as $tax) {
                    $orderItemTax = new EyewearOrderItemTax();
                    $orderItemTax->item_id = $item->id;
                    $orderItemTax->tax_name = $tax['tax_name'];
                    $orderItemTax->tax_rate = $tax['tax_rate'] ?? $tax['rate'] ?? 0;
                    $orderItemTax->save();
                }
            }
        }
    }

    private function generateOrderNumber()
    {
        $lastOrder = EyewearOrder::where('created_by', creatorId())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -6));
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return 'EWO-' . date('Y') . '-' . $newNumber;
    }

    public function getWarehouseProducts(Request $request)
    {
        if(Auth::user()->can('create-eyewear-orders') || Auth::user()->can('edit-eyewear-orders')){
            $warehouseId = $request->warehouse_id;

            if (!$warehouseId) {
                return response()->json([]);
            }

            $products = ProductServiceItem::select('id', 'name', 'sku', 'sale_price', 'tax_ids', 'unit', 'type')
                ->where('is_active', true)
                ->where('type', 'eyewear')
                ->where('created_by', creatorId())
                ->whereHas('warehouseStocks', function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)
                      ->where('quantity', '>', 0);
                })
                ->with(['warehouseStocks' => function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
                }])
                ->get()
                ->map(function ($product) {
                    $stock = $product->warehouseStocks->first();
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'sale_price' => $product->sale_price,
                        'unit' => $product->unit,
                        'type' => $product->type,
                        'stock_quantity' => $stock ? $stock->quantity : 0,
                        'taxes' => $product->taxes->map(function ($tax) {
                            return [
                                'id' => $tax->id,
                                'tax_name' => $tax->tax_name,
                                'rate' => $tax->rate
                            ];
                        })
                    ];
                });

            return response()->json($products);
        }
        else{
            return response()->json([], 403);
        }
    }

    public function post(EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('post-eyewear-orders')){
            if ($eyewearOrder->payment_status !== 'draft') {
                return back()->withErrors(['error' => __('Only draft orders can be posted.')]);
            }

            try {
                PostEyewearOrder::dispatch($eyewearOrder);
            } catch (\Throwable $th) {
                return back()->with('error', $th->getMessage());
            }
            $eyewearOrder->update(['payment_status' => 'paid']);

            return back()->with('success', __('The eyewear order has been posted successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function print(EyewearOrder $eyewearOrder)
    {
        if(Auth::user()->can('print-eyewear-orders')){
            $eyewearOrder->load(['patient', 'items.product', 'items.taxes', 'items.eyewearItem']);

            return Inertia::render('OpticalAndEyeCareCenter/EyewearOrders/Print', [
                'order' => $eyewearOrder
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }
}
