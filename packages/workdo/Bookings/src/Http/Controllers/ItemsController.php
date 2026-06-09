<?php

namespace Workdo\Bookings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\ProductService\Models\ProductServiceCategory;
use Workdo\ProductService\Models\ProductServiceUnit;
use Workdo\ProductService\Models\ProductServiceTax;
use Workdo\ProductService\Events\CreateProductServiceItem;
use Workdo\ProductService\Events\UpdateProductServiceItem;
use Workdo\ProductService\Events\DestroyProductServiceItem;
use Workdo\Bookings\Http\Requests\StoreBookingItemRequest;
use Workdo\Bookings\Http\Requests\UpdateBookingItemRequest;
use Workdo\ProductService\Models\WarehouseStock;
use Workdo\Bookings\Models\BookingDuration;

class ItemsController extends Controller
{
    private function checkItemAccess(ProductServiceItem $item)
    {
        if (Auth::user()->can('manage-any-booking-items')) {
            return $item->created_by == creatorId();
        } elseif (Auth::user()->can('manage-own-booking-items')) {
            return $item->creator_id == Auth::id();
        } else {
            return false;
        }
    }
    public function index()
    {
        if (Auth::user()->can('manage-booking-items')) {
            $items = ProductServiceItem::select('id', 'name', 'sku', 'sale_price', 'purchase_price', 'description', 'category_id', 'unit', 'type', 'image', 'created_at')
                ->with(['category:id,name', 'unitRelation:id,unit_name'])
                ->where('type', 'bookings')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-booking-items')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-booking-items')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('name'), fn($q) => $q->where('name', 'like', '%' . request('name') . '%')->orWhere('sku', 'like', '%' . request('name') . '%'))
                ->when(request('category_id'), fn($q) => $q->where('category_id', request('category_id')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest());

            // Otherwise return paginated data for the main items page
            $items = $items->paginate(request('per_page', 10))->withQueryString();

            $categories = ProductServiceCategory::where('created_by', creatorId())
                ->get(['id', 'name']);

            return Inertia::render('Bookings/Items/Index', [
                'items' => $items,
                'categories' => $categories,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create-booking-items')) {
            $categories = ProductServiceCategory::where('created_by', creatorId())
                ->get(['id', 'name']);
            $units = ProductServiceUnit::where('created_by', creatorId())->get(['id', 'unit_name']);
            $taxes = ProductServiceTax::where('created_by', creatorId())->get(['id', 'tax_name', 'rate']);

            return Inertia::render('Bookings/Items/Create', [
                'categories' => $categories,
                'units' => $units,
                'taxes' => $taxes,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreBookingItemRequest $request)
    {
        if (Auth::user()->can('create-booking-items')) {
            $validated = $request->validated();

            $item = new ProductServiceItem();
            $item->name = $validated['name'];
            $item->sku = $validated['sku'];
            $item->tax_ids = (!empty($validated['tax_ids'])) ? array_map('intval', $validated['tax_ids']) : null;
            $item->category_id = $validated['category_id'] === 'none' ? null : $validated['category_id'];
            $item->description = $validated['description'];
            $item->sale_price = $validated['sale_price'];
            $item->purchase_price = $validated['purchase_price'];
            $item->unit = $validated['unit'] === 'none' ? null : $validated['unit'];
            $item->type = $validated['type'] ?: 'bookings';
            $item->creator_id = Auth::id();
            $item->created_by = creatorId();

            // Handle image path from media library
            if (!empty($validated['image'])) {
                $item->image = basename($validated['image']);
            }

            // Handle multiple images
            if (!empty($validated['images'])) {
                $images = array_map('basename', $validated['images']);
                $item->images = json_encode($images);
            }

            $item->save();

            // Store booking duration
            if (!empty($validated['duration'])) {
                BookingDuration::create([
                    'item_id' => $item->id,
                    'duration' => $validated['duration'],
                    'total_slots' => $validated['total_slots'] ?? 1,
                    'creator_id' => Auth::id(),
                    'created_by' => creatorId(),
                ]);
            }

            // Dispatch event for packages to handle their fields
            CreateProductServiceItem::dispatch($request, $item);

            return redirect()->route('bookings.items.index')
                ->with('success', __('The booking item has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function edit(ProductServiceItem $item)
    {
        if (Auth::user()->can('edit-booking-items')) {
            if (!$this->checkItemAccess($item)) {
                return redirect()->route('bookings.items.index')->with('error', __('Permission denied'));
            }

            $categories = ProductServiceCategory::where('created_by', creatorId())
                ->get(['id', 'name']);
            $units = ProductServiceUnit::where('created_by', creatorId())->get(['id', 'unit_name']);
            $taxes = ProductServiceTax::where('created_by', creatorId())->get(['id', 'tax_name', 'rate']);

            // Load duration data
            $duration = BookingDuration::where('item_id', $item->id)->first();
            if ($duration) {
                $item->duration = $duration->duration;
                $item->total_slots = $duration->total_slots;
            }

            return Inertia::render('Bookings/Items/Edit', [
                'item' => $item,
                'categories' => $categories,
                'units' => $units,
                'taxes' => $taxes,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateBookingItemRequest $request, ProductServiceItem $item)
    {
        if (Auth::user()->can('edit-booking-items') && $item->type === 'bookings' && $item->created_by == creatorId()) {
            if (!$this->checkItemAccess($item)) {
                return redirect()->route('bookings.items.index')->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            $item->name = $validated['name'];
            $item->sku = $validated['sku'];
            $item->tax_ids = (!empty($validated['tax_ids'])) ? array_map('intval', $validated['tax_ids']) : $item->tax_ids;
            $item->category_id = $validated['category_id'] === 'none' ? null : $validated['category_id'];
            $item->description = $validated['description'];
            $item->sale_price = $validated['sale_price'];
            $item->purchase_price = $validated['purchase_price'];
            $item->unit = $validated['unit'] === 'none' ? null : $validated['unit'];
            $item->type = $validated['type'];

            // Handle image path from media library
            if (isset($validated['image'])) {
                $item->image = !empty($validated['image']) ? basename($validated['image']) : null;
            }

            // Handle multiple images
            if (isset($validated['images'])) {
                $images = !empty($validated['images']) ? array_map('basename', $validated['images']) : null;
                $item->images = $images ? json_encode($images) : null;
            }

            $item->save();

            // Update booking duration
            if (isset($validated['duration'])) {
                BookingDuration::updateOrCreate(
                    ['item_id' => $item->id],
                    [
                        'duration' => $validated['duration'],
                        'total_slots' => $validated['total_slots'] ?? 1,
                        'creator_id' => Auth::id(),
                        'created_by' => creatorId(),
                    ]
                );
            }

            // Dispatch event for packages to handle their fields
            UpdateProductServiceItem::dispatch($request, $item);

            return redirect()->route('bookings.items.index')
                ->with('success', __('The booking item details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(ProductServiceItem $item)
    {
        if (Auth::user()->can('delete-booking-items') && $item->type === 'bookings' && $item->created_by == creatorId()) {
            if (!$this->checkItemAccess($item)) {
                return back()->with('error', __('Permission denied'));
            }

            // Delete single image using delete_file helper
            if ($item->image) {
                delete_file($item->image);
            }

            // Delete multiple images using delete_file helper
            if ($item->images) {
                $images = json_decode($item->images, true);
                if (is_array($images)) {
                    foreach ($images as $image) {
                        delete_file($image);
                    }
                }
            }

            // Delete booking duration
            BookingDuration::where('item_id', $item->id)->delete();
            // Delete warehouse stock entries
            WarehouseStock::where('product_id', $item->id)->delete();

            DestroyProductServiceItem::dispatch($item);
            $item->delete();
            return back()->with('success', __('The booking item has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function allItems()
    {
        $items = ProductServiceItem::select('id', 'name', 'sku', 'sale_price', 'purchase_price', 'description', 'category_id', 'unit', 'type', 'image', 'created_at')
            ->with(['category:id,name', 'unitRelation:id,unit_name'])
            ->where('type', 'bookings')
            ->where(function ($q) {
                if (Auth::user()->can('manage-any-booking-items')) {
                    $q->where('created_by', creatorId());
                } elseif (Auth::user()->can('manage-own-booking-items')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->when(request('name'), fn($q) => $q->where('name', 'like', '%' . request('name') . '%'))
            ->when(request('category_id'), fn($q) => $q->where('category_id', request('category_id')))
            ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest());

        // If it's an AJAX request (for reviews), return JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json($items->get());
        }

        // Otherwise return paginated data for the main items page
        $items = $items->paginate(request('per_page', 10))->withQueryString();

        $categories = ProductServiceCategory::where('created_by', creatorId())
            ->get(['id', 'name']);

        return Inertia::render('Bookings/Items/Index', [
            'items' => $items,
            'categories' => $categories,
        ]);
    }
}
