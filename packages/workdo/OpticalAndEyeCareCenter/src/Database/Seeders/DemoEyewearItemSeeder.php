<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearItem;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\ProductService\Models\ProductServiceCategory;
use App\Models\Warehouse;
use Workdo\ProductService\Models\WarehouseStock;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Workdo\ProductService\Models\ProductServiceTax;
use Workdo\ProductService\Models\ProductServiceUnit;

class DemoEyewearItemSeeder extends Seeder
{
    public function run($userId): void
    {
        if (!Module_is_active('ProductService', $userId) || ProductServiceItem::where('type', 'eyewear')->where('created_by', $userId)->exists()) {
            return;
        }

        // Create optical-specific categories
        $opticalCategories = [
            ['name' => 'Eyeglasses', 'color' => '#3B82F6'],
            ['name' => 'Sunglasses', 'color' => '#F59E0B'],
            ['name' => 'Contact Lenses', 'color' => '#10B981'],
            ['name' => 'Optical Frames', 'color' => '#8B5CF6'],
        ];
        
        $categoryIds = [];
        foreach ($opticalCategories as $cat) {
            $category = ProductServiceCategory::firstOrCreate(
                ['name' => $cat['name'], 'created_by' => $userId],
                ['color' => $cat['color'], 'creator_id' => $userId]
            );
            $categoryIds[$cat['name']] = $category->id;
        }

        $warehouses = Warehouse::where('created_by', $userId)->pluck('id')->toArray();
        $taxes = ProductServiceTax::where('created_by', $userId)->pluck('id')->toArray();
        $units = ProductServiceUnit::where('created_by', $userId)->pluck('id')->toArray();
        $now = Carbon::now();

        $items = [
            ['name' => 'Ray-Ban Aviator Classic', 'type' => 'sunglasses', 'brand' => 'Ray-Ban', 'price' => 159.99, 'cost' => 80.00, 'prescription' => 'Non-prescription', 'image' => 'opt_images1.jpeg', 'images' => ['opt_images1.jpeg', 'opt_images2.jpg'], 'category' => 'Sunglasses'],
            ['name' => 'Oakley Holbrook', 'type' => 'sunglasses', 'brand' => 'Oakley', 'price' => 189.99, 'cost' => 95.00, 'prescription' => 'Non-prescription', 'image' => 'opt_images2.jpg', 'images' => ['opt_images2.jpg', 'opt_images3.jpeg'], 'category' => 'Sunglasses'],
            ['name' => 'Gucci GG0061S', 'type' => 'glasses', 'brand' => 'Gucci', 'price' => 420.00, 'cost' => 210.00, 'prescription' => 'SPH: -1.50, CYL: -0.75', 'image' => 'opt_images3.jpeg', 'images' => ['opt_images3.jpeg', 'opt_images4.jpeg'], 'category' => 'Eyeglasses'],
            ['name' => 'Prada PR 17WS', 'type' => 'sunglasses', 'brand' => 'Prada', 'price' => 350.00, 'cost' => 175.00, 'prescription' => 'Non-prescription', 'image' => 'opt_images4.jpeg', 'images' => ['opt_images4.jpeg', 'opt_images5.jpeg'], 'category' => 'Sunglasses'],
            ['name' => 'Versace VE3284', 'type' => 'glasses', 'brand' => 'Versace', 'price' => 280.00, 'cost' => 140.00, 'prescription' => 'SPH: -2.50', 'image' => 'opt_images5.jpeg', 'images' => ['opt_images5.jpeg', 'opt_images6.jpeg'], 'category' => 'Eyeglasses'],
            ['name' => 'Tom Ford FT5524', 'type' => 'glasses', 'brand' => 'Tom Ford', 'price' => 395.00, 'cost' => 197.50, 'prescription' => 'SPH: -1.75, CYL: -0.25', 'image' => 'opt_images6.jpeg', 'images' => ['opt_images6.jpeg', 'opt_images7.jpeg'], 'category' => 'Optical Frames'],
            ['name' => 'Maui Jim Peahi', 'type' => 'sunglasses', 'brand' => 'Maui Jim', 'price' => 329.00, 'cost' => 164.50, 'prescription' => 'Non-prescription', 'image' => 'opt_images7.jpeg', 'images' => ['opt_images7.jpeg', 'opt_images9.jpeg'], 'category' => 'Sunglasses'],
            ['name' => 'Warby Parker Haskell', 'type' => 'glasses', 'brand' => 'Warby Parker', 'price' => 95.00, 'cost' => 47.50, 'prescription' => 'SPH: -3.50', 'image' => 'opt_images9.jpeg', 'images' => ['opt_images9.jpeg', 'opt_images10.jpeg'], 'category' => 'Eyeglasses'],
            ['name' => 'Acuvue Oasys', 'type' => 'contact_lenses', 'brand' => 'Acuvue', 'price' => 45.00, 'cost' => 22.50, 'prescription' => 'SPH: -3.00', 'image' => 'opt_images10.jpeg', 'images' => ['opt_images10.jpeg', 'opt_images1.jpeg'], 'category' => 'Contact Lenses'],
        ];

        foreach ($items as $index => $itemData) {
            $categoryId = $categoryIds[$itemData['category']] ?? null;
            $createdAt = $now->copy()->subDays(rand(30, 150));

            $product = ProductServiceItem::create([
                'name' => $itemData['name'],
                'sku' => 'EYE-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'description' => $itemData['brand'] . ' ' . $itemData['type'],
                'sale_price' => $itemData['price'],
                'purchase_price' => $itemData['cost'],
                'image' => $itemData['image'],
                'images' => json_encode($itemData['images']),
                'tax_ids' => !empty($taxes) ? [array_rand(array_flip($taxes))] : [],
                'unit' => !empty($units) ? json_encode(array_rand(array_flip($units))) : null,
                'type' => 'eyewear',
                'is_active' => true,
                'category_id' => $categoryId,
                'creator_id' => $userId,
                'created_by' => $userId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            EyewearItem::create([
                'product_id' => $product->id,
                'product_type' => $itemData['type'],
                'brand_name' => $itemData['brand'],
                'prescription_detail' => $itemData['prescription'],
                'numbering_status' => ['numbering', 'non-numbering'][array_rand(['numbering', 'non-numbering'])],
                'customization_details' => 'Standard frame',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);

            if (!empty($warehouses)) {
                WarehouseStock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouses[array_rand($warehouses)],
                    'quantity' => rand(10, 50),
                ]);
            }
        }
    }
}
