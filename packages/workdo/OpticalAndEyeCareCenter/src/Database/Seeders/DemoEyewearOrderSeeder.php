<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrderItem;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrderItemTax;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\ProductService\Models\ProductServiceItem;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DemoEyewearOrderSeeder extends Seeder
{
    public function run($userId): void
    {
        if (EyewearOrder::where('created_by', $userId)->exists()) {
            return;
        }

        $patients = EyePatient::where('created_by', $userId)->get();
        $warehouses = Warehouse::where('created_by', $userId)->where('is_active', true)->get();

        if ($patients->isEmpty()) {
            return;
        }

        $paymentStatuses = ['draft', 'paid'];
        $paymentMethods = ['cash', 'card', 'online', 'insurance'];
        
        $createdOrders = 0;
        $maxAttempts = 20;
        $attemptCount = 0;

        while ($createdOrders < 8 && $attemptCount < $maxAttempts) {
            $attemptCount++;
            $patient = $patients->random();
            $orderDate = now()->subDays(rand(1, 60));
            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = rand(0, 50);
            
            $warehouse = $warehouses->isNotEmpty() ? $warehouses->random() : null;
            
            $products = collect();
            if ($warehouse) {
                $products = ProductServiceItem::where('type', 'eyewear')
                    ->where('created_by', $userId)
                    ->where('is_active', true)
                    ->whereHas('warehouseStocks', function($q) use ($warehouse) {
                        $q->where('warehouse_id', $warehouse->id)
                          ->where('quantity', '>', 0);
                    })
                    ->get();
            }
            
            if ($products->isEmpty()) {
                $products = ProductServiceItem::where('type', 'eyewear')
                    ->where('created_by', $userId)
                    ->where('is_active', true)
                    ->get();
            }
            
            if ($products->isEmpty()) {
                continue;
            }

            $order = EyewearOrder::create([
                'order_number' => 'EWO-' . date('Y') . '-' . str_pad($createdOrders + 1, 6, '0', STR_PAD_LEFT),
                'order_date' => $orderDate,
                'patient_id' => $patient->id,
                'warehouse_id' => $warehouse?->id,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => $discountAmount,
                'total_amount' => 0,
                'paid_amount' => 0,
                'balance_amount' => 0,
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'delivery_date' => $orderDate->copy()->addDays(rand(7, 14)),
                'prescription_details' => 'SPH: -2.00, CYL: -0.50, AXIS: 180',
                'special_notes' => 'Handle with care',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);

            $itemCount = rand(1, 3);
            foreach ($products->random(min($itemCount, $products->count())) as $product) {
                $quantity = rand(1, 2);
                $unitPrice = $product->sale_price;
                $itemDiscount = rand(0, 10);
                $taxRate = 10;
                $itemTax = (($unitPrice * $quantity) - $itemDiscount) * ($taxRate / 100);
                $itemTotal = ($unitPrice * $quantity) - $itemDiscount + $itemTax;

                $orderItem = EyewearOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'item_type' => ['standard', 'custom'][array_rand(['standard', 'custom'])],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => 5,
                    'discount_amount' => $itemDiscount,
                    'tax_percentage' => $taxRate,
                    'tax_amount' => $itemTax,
                    'total_amount' => $itemTotal,
                ]);

                EyewearOrderItemTax::create([
                    'item_id' => $orderItem->id,
                    'tax_name' => 'VAT',
                    'tax_rate' => $taxRate,
                ]);

                $subtotal += $unitPrice * $quantity;
                $taxAmount += $itemTax;
            }

            $totalAmount = $subtotal - $discountAmount + $taxAmount;
            $paidAmount = $order->payment_status === 'paid' ? $totalAmount : 0;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $totalAmount - $paidAmount,
            ]);
            
            $createdOrders++;
        }
    }
}
