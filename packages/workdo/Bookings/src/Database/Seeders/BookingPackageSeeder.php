<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingPackage;
use App\Models\User;
use Workdo\ProductService\Models\ProductServiceItem;
use Workdo\Bookings\Models\BookingExtraService;
use Carbon\Carbon;

class BookingPackageSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createPackagesForUser($userId);
        }
    }
    
    private function createPackagesForUser($userId)
    {
        if (BookingPackage::where('created_by', $userId)->exists()) {
            return;
        }
        
        $items = ProductServiceItem::where('created_by', $userId)
            ->where('type', 'bookings')
            ->get();
            
        if ($items->isEmpty()) return;
        
        $packages = [
            ['name' => 'Express Hair Cut', 'services' => 'Quick hair cut and basic styling', 'time' => '30', 'period' => 'minutes', 'price' => 35],
            ['name' => 'Premium Hair Styling', 'services' => 'Hair cut, wash, styling and treatment', 'time' => '2', 'period' => 'hours', 'price' => 85],
            ['name' => 'Basic Facial', 'services' => 'Cleansing, exfoliation and moisturizing', 'time' => '1', 'period' => 'hours', 'price' => 65],
            ['name' => 'Deluxe Facial Package', 'services' => 'Deep cleansing, mask, massage and serum', 'time' => '2', 'period' => 'hours', 'price' => 120],
            ['name' => 'Relaxation Massage', 'services' => 'Full body Swedish massage', 'time' => '1', 'period' => 'hours', 'price' => 90],
            ['name' => 'Deep Tissue Therapy', 'services' => 'Therapeutic deep tissue massage', 'time' => '2', 'period' => 'hours', 'price' => 110],
            ['name' => 'Basic Manicure', 'services' => 'Nail shaping, cuticle care and polish', 'time' => '45', 'period' => 'minutes', 'price' => 45],
            ['name' => 'Luxury Mani-Pedi', 'services' => 'Complete manicure and pedicure with spa treatment', 'time' => '2', 'period' => 'hours', 'price' => 95],
            ['name' => 'Eyebrow Shaping', 'services' => 'Professional eyebrow threading and shaping', 'time' => '20', 'period' => 'minutes', 'price' => 25],
            ['name' => 'Bridal Makeup Trial', 'services' => 'Complete bridal makeup consultation and trial', 'time' => '3', 'period' => 'hours', 'price' => 150],
            ['name' => 'Hair Color Touch-up', 'services' => 'Root touch-up and color refresh', 'time' => '1', 'period' => 'hours', 'price' => 75],
            ['name' => 'Full Hair Color', 'services' => 'Complete hair coloring service', 'time' => '3', 'period' => 'hours', 'price' => 140],
            ['name' => 'Acne Treatment Session', 'services' => 'Specialized acne treatment facial', 'time' => '1', 'period' => 'hours', 'price' => 80],
            ['name' => 'Gel Nail Application', 'services' => 'Gel nail extensions with design', 'time' => '2', 'period' => 'hours', 'price' => 70],
            ['name' => 'Anti-Aging Facial', 'services' => 'Premium anti-aging treatment with collagen mask', 'time' => '2', 'period' => 'hours', 'price' => 160]
        ];
        
        $extraServices = BookingExtraService::where('created_by', $userId)->get();
        
        foreach ($packages as $index => $package) {
            if ($index >= $items->count()) break;
            
            $item = $items[$index];
            
            // Get random 2-3 extra services for each package
            $randomServices = !empty($extraServices) ? $extraServices->random(min(3, $extraServices->count()))->pluck('id')->toArray() : [];
            
            // Calculate item price with tax
            $itemPrice = $item->sale_price;
            if ($item->tax_ids) {
                $taxRate = 0.1; // 10% tax
                $itemPrice = $itemPrice + ($itemPrice * $taxRate);
            }
            
            // Add extra services amounts
            $extraServicesTotal = 0;
            foreach ($randomServices as $serviceId) {
                $service = $extraServices->firstWhere('id', $serviceId);
                if ($service) {
                    $extraServicesTotal += $service->amount;
                }
            }
            
            $totalPrice = $itemPrice + $extraServicesTotal;
            
            BookingPackage::create([
                'name' => $package['name'],
                'item_id' => $item->id,
                'services' => json_encode($randomServices),
                'delivery_time' => $package['time'],
                'delivery_period' => $package['period'],
                'price' => round($totalPrice, 2),
                'created_by' => $userId,
                'creator_id' => $userId,
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
            ]);
        }
    }
}