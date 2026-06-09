<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

use Workdo\RepairManagementSystem\Models\RepairOrderRequest;
use Workdo\RepairManagementSystem\Models\RepairTechnician;
use Workdo\RepairManagementSystem\Models\RepairPart;
use Workdo\RepairManagementSystem\Models\RepairMovementHistory;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoRepairOrderRequestSeeder extends Seeder
{
    public function run($userId): void
    {
        if (RepairOrderRequest::where('created_by', $userId)->exists()) {
            return; // Skip seeding if data already exists
        }

        if (!empty($userId)) {
            $technicians = RepairTechnician::where('created_by', $userId)->pluck('id')->toArray();

            if (empty($technicians)) {
                return;
            }

            $countryCodes = ['+1', '+44', '+91', '+61', '+81', '+49', '+33', '+39', '+55', '+971', '+86', '+7', '+27', '+82', '+34'];
            
            $repairOrders = [
                ['product' => 'iPhone 14 Pro', 'quantity' => 2, 'customer' => 'Cassandra Blackwood', 'email' => 'cassandra.blackwood@repairmail.com', 'status' => 0, 'days_ago' => 180, 'parts' => []],
                ['product' => 'Samsung Galaxy Tab S8', 'quantity' => 1, 'customer' => 'Demetrius Ashford', 'email' => 'demetrius.ashford@fixhub.com', 'status' => 1, 'days_ago' => 175, 'parts' => [['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 2], ['name' => 'Belt', 'price' => 250.00, 'qty' => 1]]],
                ['product' => 'MacBook Air M2', 'quantity' => 1, 'customer' => 'Evangeline Thornton', 'email' => 'evangeline.thornton@techclinic.com', 'status' => 2, 'days_ago' => 170, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 3]]],
                ['product' => 'iPad Pro 12.9"', 'quantity' => 1, 'customer' => 'Fitzgerald Caldwell', 'email' => 'fitzgerald.caldwell@devicedepot.com', 'status' => 0, 'days_ago' => 165, 'parts' => []],
                ['product' => 'Dell XPS 13', 'quantity' => 1, 'customer' => 'Guinevere Pemberton', 'email' => 'guinevere.pemberton@repairworks.com', 'status' => 3, 'days_ago' => 160, 'parts' => [['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 2], ['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 1]]],
                ['product' => 'iPhone 13 Mini', 'quantity' => 2, 'customer' => 'Hortensia Blackthorne', 'email' => 'hortensia.blackthorne@mobilecenter.com', 'status' => 1, 'days_ago' => 155, 'parts' => [['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 3]]],
                ['product' => 'Samsung Galaxy S23', 'quantity' => 1, 'customer' => 'Ignatius Weatherby', 'email' => 'ignatius.weatherby@smartrepairs.com', 'status' => 4, 'days_ago' => 150, 'parts' => [['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 2], ['name' => 'Belt', 'price' => 250.00, 'qty' => 1]]],
                ['product' => 'MacBook Pro 16"', 'quantity' => 1, 'customer' => 'Josephine Fairfax', 'email' => 'josephine.fairfax@techstation.com', 'status' => 0, 'days_ago' => 145, 'parts' => []],
                ['product' => 'HP Pavilion 15', 'quantity' => 2, 'customer' => 'Kingsley Montague', 'email' => 'kingsley.montague@gadgetcare.com', 'status' => 2, 'days_ago' => 140, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 2]]],
                ['product' => 'Google Pixel 7', 'quantity' => 1, 'customer' => 'Lavinia Whitmore', 'email' => 'lavinia.whitmore@repairshop.com', 'status' => 1, 'days_ago' => 135, 'parts' => [['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 1], ['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 3]]],
                ['product' => 'iPhone 12 Pro Max', 'quantity' => 1, 'customer' => 'Mortimer Blackwell', 'email' => 'mortimer.blackwell@fixcentral.com', 'status' => 5, 'days_ago' => 130, 'parts' => []],
                ['product' => 'Samsung Galaxy Note 20', 'quantity' => 1, 'customer' => 'Nicolette Ashworth', 'email' => 'nicolette.ashworth@devicefix.com', 'status' => 2, 'days_ago' => 125, 'parts' => [['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 2]]],
                ['product' => 'Surface Pro 9', 'quantity' => 1, 'customer' => 'Octavian Pembroke', 'email' => 'octavian.pembroke@techrepair.com', 'status' => 0, 'days_ago' => 120, 'parts' => []],
                ['product' => 'Lenovo ThinkPad X1', 'quantity' => 2, 'customer' => 'Prudence Fairchild', 'email' => 'prudence.fairchild@mobileworks.com', 'status' => 3, 'days_ago' => 115, 'parts' => [['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 3], ['name' => 'Belt', 'price' => 250.00, 'qty' => 2]]],
                ['product' => 'OnePlus 11', 'quantity' => 1, 'customer' => 'Quinton Blackstone', 'email' => 'quinton.blackstone@repairplus.com', 'status' => 1, 'days_ago' => 110, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 2], ['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 1]]],
                ['product' => 'iPhone 11', 'quantity' => 2, 'customer' => 'Rosalinda Thornfield', 'email' => 'rosalinda.thornfield@gadgetdoctor.com', 'status' => 6, 'days_ago' => 105, 'parts' => []],
                ['product' => 'Samsung Galaxy A54', 'quantity' => 1, 'customer' => 'Silvester Whitfield', 'email' => 'silvester.whitfield@techsolutions.com', 'status' => 3, 'days_ago' => 100, 'parts' => [['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 2], ['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 3]]],
                ['product' => 'MacBook Air M1', 'quantity' => 1, 'customer' => 'Tabitha Blackburn', 'email' => 'tabitha.blackburn@devicecenter.com', 'status' => 4, 'days_ago' => 95, 'parts' => [['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 3], ['name' => 'Belt', 'price' => 250.00, 'qty' => 2], ['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 1]]],
                ['product' => 'ASUS ROG Laptop', 'quantity' => 2, 'customer' => 'Ulysses Fairmont', 'email' => 'ulysses.fairmont@repairstation.com', 'status' => 0, 'days_ago' => 90, 'parts' => []],
                ['product' => 'iPhone 13 Pro', 'quantity' => 1, 'customer' => 'Vivienne Ashcroft', 'email' => 'vivienne.ashcroft@mobileclinic.com', 'status' => 7, 'days_ago' => 89, 'parts' => [['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 2], ['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 1]]],
                ['product' => 'iPhone 12', 'quantity' => 1, 'customer' => 'Winifred Blackwood', 'email' => 'winifred.blackwood@fixtech.com', 'status' => 7, 'days_ago' => 88, 'parts' => [['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 2], ['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 1]]],
                ['product' => 'Xiaomi Mi 13', 'quantity' => 1, 'customer' => 'Xerxes Pemberton', 'email' => 'xerxes.pemberton@gadgetrepair.com', 'status' => 2, 'days_ago' => 85, 'parts' => [['name' => 'Belt', 'price' => 250.00, 'qty' => 3]]],
                ['product' => 'iPhone SE 2022', 'quantity' => 2, 'customer' => 'Yvette Thornbury', 'email' => 'yvette.thornbury@techcare.com', 'status' => 7, 'days_ago' => 80, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 2]]],
                ['product' => 'Samsung Galaxy S21', 'quantity' => 1, 'customer' => 'Zacharias Whitlock', 'email' => 'zacharias.whitlock@repairworld.com', 'status' => 7, 'days_ago' => 77, 'parts' => [['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 2], ['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 1], ['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 3]]],
                ['product' => 'Samsung Galaxy S22', 'quantity' => 1, 'customer' => 'Arabella Fairweather', 'email' => 'arabella.fairweather@deviceworks.com', 'status' => 4, 'days_ago' => 75, 'parts' => [['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 2]]],
                ['product' => 'iPad Mini 6', 'quantity' => 1, 'customer' => 'Bartholomew Ashford', 'email' => 'bartholomew.ashford@mobilefixes.com', 'status' => 1, 'days_ago' => 70, 'parts' => [['name' => 'Belt', 'price' => 250.00, 'qty' => 3]]],
                ['product' => 'MacBook Air 2020', 'quantity' => 1, 'customer' => 'Cordelia Blackthorne', 'email' => 'cordelia.blackthorne@techservice.com', 'status' => 7, 'days_ago' => 67, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 3], ['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 2], ['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 1]]],
                ['product' => 'iPad 9th Gen', 'quantity' => 1, 'customer' => 'Donatello Pembroke', 'email' => 'donatello.pembroke@gadgetstation.com', 'status' => 4, 'days_ago' => 66, 'parts' => [['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 2], ['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 1]]],
                ['product' => 'Microsoft Surface Laptop', 'quantity' => 1, 'customer' => 'Esmeralda Whitmore', 'email' => 'esmeralda.whitmore@repaircentral.com', 'status' => 3, 'days_ago' => 65, 'parts' => [['name' => 'Belt', 'price' => 250.00, 'qty' => 2], ['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 3]]],
                ['product' => 'Huawei P50 Pro', 'quantity' => 1, 'customer' => 'Ferdinand Ashworth', 'email' => 'ferdinand.ashworth@devicerepair.com', 'status' => 5, 'days_ago' => 60, 'parts' => []],
                ['product' => 'iPhone 11 Pro', 'quantity' => 1, 'customer' => 'Gwendolyn Fairchild', 'email' => 'gwendolyn.fairchild@mobiledoctor.com', 'status' => 7, 'days_ago' => 55, 'parts' => [['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 2]]],
                ['product' => 'iPhone X', 'quantity' => 1, 'customer' => 'Humphrey Blackstone', 'email' => 'humphrey.blackstone@techfixes.com', 'status' => 6, 'days_ago' => 55, 'parts' => []],
                ['product' => 'Samsung Galaxy S20', 'quantity' => 1, 'customer' => 'Isadora Thornfield', 'email' => 'isadora.thornfield@gadgetclinic.com', 'status' => 6, 'days_ago' => 50, 'parts' => []],
                ['product' => 'MacBook Pro 2017', 'quantity' => 1, 'customer' => 'Jasper Whitfield', 'email' => 'jasper.whitfield@repairpro.com', 'status' => 7, 'days_ago' => 45, 'parts' => [['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 3], ['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 2], ['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 1]]],
                ['product' => 'Samsung Galaxy A53', 'quantity' => 1, 'customer' => 'Katarina Blackburn', 'email' => 'katarina.blackburn@devicehub.com', 'status' => 7, 'days_ago' => 44, 'parts' => [['name' => 'Belt', 'price' => 250.00, 'qty' => 2]]],
                ['product' => 'iPhone 8 Plus', 'quantity' => 2, 'customer' => 'Leopold Fairmont', 'email' => 'leopold.fairmont@mobileworks.com', 'status' => 4, 'days_ago' => 40, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 2], ['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 1]]],
                ['product' => 'Samsung Galaxy A32', 'quantity' => 1, 'customer' => 'Magdalena Ashcroft', 'email' => 'magdalena.ashcroft@techstation.com', 'status' => 2, 'days_ago' => 35, 'parts' => [['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 3]]],
                ['product' => 'iPhone 13', 'quantity' => 1, 'customer' => 'Nathaniel Pemberton', 'email' => 'nathaniel.pemberton@gadgetcare.com', 'status' => 7, 'days_ago' => 30, 'parts' => [['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 3], ['name' => 'Laptop Battery', 'price' => 89.99, 'qty' => 2], ['name' => 'Belt', 'price' => 250.00, 'qty' => 1]]],
                ['product' => 'Samsung Galaxy S21 Ultra', 'quantity' => 2, 'customer' => 'Ophelia Thornbury', 'email' => 'ophelia.thornbury@repairworks.com', 'status' => 7, 'days_ago' => 25, 'parts' => [['name' => 'Ink Cartridge', 'price' => 25.99, 'qty' => 3], ['name' => 'Light Bulb', 'price' => 300.00, 'qty' => 2], ['name' => 'Yoga Mat', 'price' => 150.00, 'qty' => 1]]],
                ['product' => 'MacBook Pro M1', 'quantity' => 1, 'customer' => 'Percival Whitlock', 'email' => 'percival.whitlock@devicecenter.com', 'status' => 1, 'days_ago' => 20, 'parts' => [['name' => 'Brake Pad Set', 'price' => 80.00, 'qty' => 2]]]
            ];

            $statusLocations = [
                0 => 'Main Location',
                1 => 'Workshop Location',
                2 => 'Waiting For Testing Location',
                3 => 'Testing Location',
                4 => 'Finish Location',
                5 => 'Irrepairable Location',
                6 => 'Cancel Location',
                7 => 'Finish Location'
            ];

            foreach ($repairOrders as $orderData) {
                $technicianId = $technicians[array_rand($technicians)];
                $createdDate = Carbon::now()->subDays($orderData['days_ago']);
                $expiryDate = $createdDate->copy()->addDays(rand(15, 45));

                $repairOrder = RepairOrderRequest::create([
                    'product_name' => $orderData['product'],
                    'product_quantity' => $orderData['quantity'],
                    'customer_name' => $orderData['customer'],
                    'customer_email' => $orderData['email'],
                    'customer_mobile_no' => $countryCodes[array_rand($countryCodes)] . mt_rand(1000000000, 9999999999),
                    'date' => $createdDate->format('Y-m-d'),
                    'expiry_date' => $expiryDate->format('Y-m-d'),
                    'repair_technician' => $technicianId,
                    'location' => $statusLocations[$orderData['status']],
                    'status' => $orderData['status'],
                    'creator_id' => $userId,
                    'created_by' => $userId,
                ]);

                // Create movement history
                $this->createMovementHistory($repairOrder, $orderData['status'], $createdDate, $userId);

                // Create repair parts for orders with parts data
                if (!empty($orderData['parts']) && $orderData['status'] >= 1 && !in_array($orderData['status'], [5, 6])) {
                    $this->createRepairParts($repairOrder, $orderData['parts'], $userId);
                }
            }
        }
    }

    private function createMovementHistory($repairOrder, $status, $createdDate, $userId)
    {
        $currentDate = $createdDate->copy()->addHours(1);

        if ($status >= 1) {
            RepairMovementHistory::create([
                'repair_order_request_id' => $repairOrder->id,
                'date_time' => $currentDate->format('Y-m-d H:i:s'),
                'movement_from' => 'Main Location',
                'movement_to' => 'Workshop Location',
                'movement_reason' => 'Repair',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
            $currentDate->addHours(rand(4, 24));
        }

        if ($status >= 2) {
            RepairMovementHistory::create([
                'repair_order_request_id' => $repairOrder->id,
                'date_time' => $currentDate->format('Y-m-d H:i:s'),
                'movement_from' => 'Workshop Location',
                'movement_to' => 'Waiting For Testing Location',
                'movement_reason' => 'Repair',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
            $currentDate->addHours(rand(2, 12));
        }

        if ($status >= 3) {
            RepairMovementHistory::create([
                'repair_order_request_id' => $repairOrder->id,
                'date_time' => $currentDate->format('Y-m-d H:i:s'),
                'movement_from' => 'Waiting For Testing Location',
                'movement_to' => 'Testing Location',
                'movement_reason' => 'Testing',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
            $currentDate->addHours(rand(1, 8));
        }

        if ($status >= 4 || $status == 7) {
            RepairMovementHistory::create([
                'repair_order_request_id' => $repairOrder->id,
                'date_time' => $currentDate->format('Y-m-d H:i:s'),
                'movement_from' => 'Testing Location',
                'movement_to' => 'Finish Location',
                'movement_reason' => 'Testing',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }

        if ($status == 5) {
            RepairMovementHistory::create([
                'repair_order_request_id' => $repairOrder->id,
                'date_time' => $currentDate->format('Y-m-d H:i:s'),
                'movement_from' => 'Workshop Location',
                'movement_to' => 'Irrepairable Location',
                'movement_reason' => 'Irrepairable',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }

        if ($status == 6) {
            RepairMovementHistory::create([
                'repair_order_request_id' => $repairOrder->id,
                'date_time' => $currentDate->format('Y-m-d H:i:s'),
                'movement_from' => 'Main Location',
                'movement_to' => 'Cancel Location',
                'movement_reason' => 'Cancel',
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }

    private function createRepairParts($repairOrder, $partsData, $userId)
    {
        foreach ($partsData as $partData) {
            $productId = null;
            // if (Module_is_active('ProductService')) {
                $product = \Workdo\ProductService\Models\ProductServiceItem::where('created_by', $userId)
                    ->where('name', $partData['name'])
                    ->where('type', 'part')
                    ->first();
                $productId = $product ? $product->id : null;
            // }
            
            RepairPart::create([
                'repair_id' => $repairOrder->id,
                'product_id' => $productId,
                'quantity' => $partData['qty'],
                'price' => $partData['price'],
                'discount' => rand(0, 1) ? rand(5, 15) : 0,
                'tax' => '',
                'description' => $partData['name'],
                'creator_id' => $userId,
                'created_by' => $userId,
            ]);
        }
    }
}