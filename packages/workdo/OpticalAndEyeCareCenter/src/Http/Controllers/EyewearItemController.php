<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Controllers;

use App\Models\Warehouse;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\ProductService\Models\ProductServiceTax;
use Workdo\ProductService\Models\ProductServiceCategory;
use Workdo\ProductService\Models\ProductServiceUnit;
use Workdo\ProductService\Models\WarehouseStock;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\StoreEyewearItemRequest;
use Workdo\OpticalAndEyeCareCenter\Http\Requests\UpdateEyewearItemRequest;
use Workdo\OpticalAndEyeCareCenter\Events\CreateEyewearItem;
use Workdo\OpticalAndEyeCareCenter\Events\UpdateEyewearItem;
use Workdo\OpticalAndEyeCareCenter\Events\DestroyEyewearItem;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearItem;

class EyewearItemController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-eyewear-items')) {
            $eyewearitems = [];
            if (Module_is_active('ProductService')) {
                $eyewearitems = ProductServiceItem::query()
                    ->with(['category', 'warehouseStocks'])
                    ->where('type', 'eyewear')
                    ->where(function ($q) {
                        if (Auth::user()->can('manage-any-eyewear-items')) {
                            $q->where('created_by', creatorId());
                        } elseif (Auth::user()->can('manage-own-eyewear-items')) {
                            $q->where('creator_id', Auth::id());
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    })
                    ->when(request('name'), function ($q) {
                        $q->where(function ($query) {
                            $query->where('name', 'like', '%' . request('name') . '%');
                            $query->orWhere('description', 'like', '%' . request('name') . '%');
                        });
                    })
                    ->when(request('category_id') && request('category_id') !== '', fn($q) => $q->where('category_id', request('category_id')))
                    ->when(request('is_active') !== null && request('is_active') !== '', fn($q) => $q->where('is_active', request('is_active') === '1' ? 1 : 0))
                    ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                    ->paginate(request('per_page', 10))
                    ->withQueryString();

                $eyewearitems->getCollection()->transform(function ($item) {
                    $item->quantity = $item->warehouseStocks->sum('quantity');
                    return $item;
                });
            }
            return Inertia::render('OpticalAndEyeCareCenter/EyewearItems/Index', [
                'eyewearitems' => $eyewearitems,
                'categories' => ProductServiceCategory::where('created_by', creatorId())->get(['id', 'name']),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create-eyewear-items')) {
            $taxes = ProductServiceTax::where('created_by', creatorId())->get(['id', 'tax_name', 'rate']);
            $categories = ProductServiceCategory::where('created_by', creatorId())->get(['id', 'name']);
            $units = ProductServiceUnit::where('created_by', creatorId())->get(['id', 'unit_name']);
            $warehouses = Warehouse::where('is_active', true)->where('created_by', creatorId())->get(['id', 'name']);

            return Inertia::render('OpticalAndEyeCareCenter/EyewearItems/Create', [
                'taxes' => $taxes,
                'categories' => $categories,
                'units' => $units,
                'warehouses' => $warehouses,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
    public function store(StoreEyewearItemRequest $request)
    {
        if (Auth::user()->can('create-eyewear-items')) {
            if (Module_is_active('ProductService')) {
                $validated = $request->validated();

                $item                   = new ProductServiceItem();
                $item->name             = $validated['name'];
                $item->sku              = $validated['sku'];
                $item->tax_ids          =  (!empty($validated['tax_ids'])) ? array_map('intval', $validated['tax_ids']) : null;
                $item->category_id      = $validated['category_id'] === 'none' ? null : $validated['category_id'];
                $item->description      = $validated['description'] ?? null;
                $item->long_description = $validated['long_description'] ?? null;
                $item->sale_price       = $validated['sale_price'];
                $item->purchase_price   = $validated['purchase_price'];
                $item->unit             = $validated['unit'] === 'none' ? null : $validated['unit'];
                $item->type             = 'eyewear';
                $item->creator_id       = Auth::id();
                $item->created_by       = creatorId();


                if (!empty($validated['image'])) {
                    $item->image = basename($validated['image']);
                }

                if (!empty($validated['images'])) {
                    $processedImages = array_map('basename', $validated['images']);
                    $item->images = json_encode($processedImages);
                }

                $item->save();

                // Create warehouse stock entry if warehouse and quantity are provided
                if (isset($validated['warehouse_id']) && $validated['warehouse_id'] !== 'none' && isset($validated['quantity'])) {
                    $warehouseStock               = new WarehouseStock();
                    $warehouseStock->product_id   = $item->id;
                    $warehouseStock->warehouse_id = $validated['warehouse_id'];
                    $warehouseStock->quantity     = $validated['quantity'] ?? 0;
                    $warehouseStock->save();
                }

                $eyewearItem                        = new EyewearItem();
                $eyewearItem->product_id            = $item->id;
                $eyewearItem->product_type          = $validated['product_type'] ?? null;
                $eyewearItem->brand_name            = $validated['brand_name'] ?? null;
                $eyewearItem->prescription_detail   = $validated['prescription_detail'] ?? null;
                $eyewearItem->numbering_status      = $validated['numbering_status'] ?? 'numbering';
                $eyewearItem->customization_details = $validated['customization_details'] ?? null;
                $eyewearItem->creator_id            = Auth::id();
                $eyewearItem->created_by            = creatorId();
                $eyewearItem->save();

                CreateEyewearItem::dispatch($request, $eyewearItem);

                return redirect()->route('optical-and-eye-care-center.eyewear-items.index')->with('success', __('The eye wear item has been created successfully.'));
            } else {
                return back()->with('error', __('Product & Service Add-on is not active.'));
            }
        } else {
            return redirect()->route('optical-and-eye-care-center.eyewear-items.index')->with('error', __('Permission denied'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('edit-eyewear-items')) {
            $item = ProductServiceItem::with('warehouseStocks')->findOrFail($id);
            if ($item->created_by == creatorId()) {
                $taxes = ProductServiceTax::where('created_by', creatorId())->get(['id', 'tax_name', 'rate']);
                $categories = ProductServiceCategory::where('created_by', creatorId())->get(['id', 'name']);
                $units = ProductServiceUnit::where('created_by', creatorId())->get(['id', 'unit_name']);
                $warehouses = Warehouse::where('is_active', true)->where('created_by', creatorId())->get(['id', 'name']);

                $item->quantity = $item->warehouseStocks->sum('quantity');

                $eyewearItem = EyewearItem::where('product_id', $item->id)->first();
                $item->customization_details = $eyewearItem ? $eyewearItem->customization_details : '';
                $item->product_type = $eyewearItem ? $eyewearItem->product_type : '';
                $item->brand_name = $eyewearItem ? $eyewearItem->brand_name : '';
                $item->prescription_detail = $eyewearItem ? $eyewearItem->prescription_detail : '';
                $item->numbering_status = $eyewearItem ? $eyewearItem->numbering_status : 'numbering';

                return Inertia::render('OpticalAndEyeCareCenter/EyewearItems/Edit', [
                    'item' => $item,
                    'taxes' => $taxes,
                    'categories' => $categories,
                    'units' => $units,
                    'warehouses' => $warehouses,
                ]);
            } else {
                return back()->with('error', __('Permission denied'));
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateEyewearItemRequest $request, $id)
    {
        if (Module_is_active('ProductService')) {
            if (Auth::user()->can('edit-eyewear-items')) {
                $item = ProductServiceItem::findOrFail($id);
                $validated = $request->validated();

                $item->name             = $validated['name'];
                $item->sku              = $validated['sku'];
                $item->tax_ids          = $validated['tax_ids'];
                $item->category_id      = $validated['category_id'];
                $item->description      = $validated['description'] ?? null;
                $item->long_description = $validated['long_description'] ?? null;
                $item->sale_price       = $validated['sale_price'];
                $item->purchase_price   = $validated['purchase_price'];
                $item->unit             = $validated['unit'];
                $item->type             = 'eyewear';


                if (!empty($validated['image'])) {
                    $item->image = basename($validated['image']);
                }

                if (!empty($validated['images'])) {
                    $processedImages = is_array($validated['images']) ? array_map('basename', $validated['images']) : [];
                    $item->images = json_encode($processedImages);
                }

                $item->save();

                // Update warehouse stock if quantity is provided
                if (isset($validated['quantity']) && isset($validated['warehouse_id'])) {
                    WarehouseStock::updateOrCreate(
                        [
                            'product_id' => $item->id,
                            'warehouse_id' => $validated['warehouse_id'],
                        ],
                        [
                            'quantity' => $validated['quantity'],
                        ]
                    );
                }

                $eyewearItem = EyewearItem::where('product_id', $item->id)->first();
                if ($eyewearItem) {
                    $eyewearItem->product_type          = $validated['product_type'] ?? null;
                    $eyewearItem->brand_name            = $validated['brand_name'] ?? null;
                    $eyewearItem->prescription_detail   = $validated['prescription_detail'] ?? null;
                    $eyewearItem->numbering_status      = $validated['numbering_status'] ?? 'numbering';
                    $eyewearItem->customization_details = $validated['customization_details'] ?? null;
                    $eyewearItem->save();
                } else {
                    $eyewearItem = new EyewearItem();
                    $eyewearItem->product_id            = $item->id;
                    $eyewearItem->product_type          = $validated['product_type'] ?? null;
                    $eyewearItem->brand_name            = $validated['brand_name'] ?? null;
                    $eyewearItem->prescription_detail   = $validated['prescription_detail'] ?? null;
                    $eyewearItem->numbering_status      = $validated['numbering_status'] ?? 'numbering';
                    $eyewearItem->customization_details = $validated['customization_details'] ?? null;
                    $eyewearItem->creator_id            = Auth::id();
                    $eyewearItem->created_by            = creatorId();
                    $eyewearItem->save();
                }

                UpdateEyewearItem::dispatch($request, $eyewearItem);

                return redirect()->route('optical-and-eye-care-center.eyewear-items.index')->with('success', __('The eye wear item details are updated successfully.'));
            } else {
                return redirect()->route('optical-and-eye-care-center.eyewear-items.index')->with('error', __('Permission denied'));
            }
        } else {
            return back()->with('error', __('Product & Service Add-on is not active.'));
        }
    }

    public function show($id)
    {
        if (Auth::user()->can('view-eyewear-items')) {
            $item = ProductServiceItem::with(['category', 'unitRelation', 'warehouseStocks.warehouse:id,name'])->findOrFail($id);

            if ($item->created_by == creatorId()) {
                $taxes = [];
                if ($item->tax_ids) {
                    $taxIds = $item->tax_ids;
                    if (!empty($taxIds) && is_array($taxIds) && count($taxIds) > 0) {
                        $taxes = ProductServiceTax::whereIn('id', $taxIds)
                            ->where('created_by', creatorId())
                            ->get(['id', 'tax_name', 'rate'])
                            ->toArray();
                    }
                }

                $itemData = $item->toArray();
                $itemData['taxes'] = $taxes;
                $itemData['total_quantity'] = $item->warehouseStocks->sum('quantity');

                $itemData['warehouse_stocks'] = $item->warehouseStocks->map(function($stock) {
                    return [
                        'warehouse_name' => $stock->warehouse->name,
                        'quantity' => $stock->quantity
                    ];
                });

                $eyewearItem = EyewearItem::where('product_id', $item->id)->first();
                $itemData['customization_details'] = $eyewearItem ? $eyewearItem->customization_details : '';
                $itemData['product_type'] = $eyewearItem ? $eyewearItem->product_type : '';
                $itemData['brand_name'] = $eyewearItem ? $eyewearItem->brand_name : '';
                $itemData['prescription_detail'] = $eyewearItem ? $eyewearItem->prescription_detail : '';
                $itemData['numbering_status'] = $eyewearItem ? $eyewearItem->numbering_status : 'numbering';

                return Inertia::render('OpticalAndEyeCareCenter/EyewearItems/Show', [
                    'item' => $itemData,
                ]);
            } else {
             return back()->with('error', __('Permission denied'));
            }
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete-eyewear-items')) {
            if (Module_is_active('ProductService')) {
                $item = ProductServiceItem::find($id);

                if ($item->image) {
                    delete_file($item->image);
                }

                if ($item->images) {
                    $images = json_decode($item->images, true);
                    if (is_array($images)) {
                        foreach ($images as $image) {
                            delete_file($image);
                        }
                    }
                }

                $eyewearItem = EyewearItem::where('product_id', $item->id)->first();
                if ($eyewearItem) {
                    DestroyEyewearItem::dispatch($eyewearItem);
                    $eyewearItem->delete();
                }

                WarehouseStock::where('product_id', $item->id)->delete();

                $item->delete();

                return redirect()->back()->with('success', __('The eye wear item has been deleted.'));
            } else {
                return back()->with('error', __('Product & Service Add-on is not active.'));
            }
        } else {
            return redirect()->route('optical-and-eye-care-center.eyewear-items.index')->with('error', __('Permission denied'));
        }
    }
}
