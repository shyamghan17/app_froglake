<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingCustomer;
use App\Models\User;

class BookingCustomerSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createCustomersForUser($userId);
        }
    }
    
    private function createCustomersForUser($userId)
    {
        if (BookingCustomer::where('created_by', $userId)->exists()) {
            return;
        }
        
        $customers = [
            ['first_name' => 'Emma', 'last_name' => 'Johnson', 'mobile_number' => '+1-555-0101', 'description' => 'Regular customer, prefers morning appointments'],
            ['first_name' => 'Sophia', 'last_name' => 'Williams', 'mobile_number' => '+1-555-0102', 'description' => 'VIP customer, books monthly spa packages'],
            ['first_name' => 'Olivia', 'last_name' => 'Brown', 'mobile_number' => '+1-555-0103', 'description' => 'New customer, interested in facial treatments'],
            ['first_name' => 'Ava', 'last_name' => 'Davis', 'mobile_number' => '+1-555-0104', 'description' => 'Loyal customer, loves hair styling services'],
            ['first_name' => 'Isabella', 'last_name' => 'Miller', 'mobile_number' => '+1-555-0105', 'description' => 'Frequent visitor, prefers weekend slots'],
            ['first_name' => 'Mia', 'last_name' => 'Wilson', 'mobile_number' => '+1-555-0106', 'description' => 'Professional client, books during lunch hours'],
            ['first_name' => 'Charlotte', 'last_name' => 'Moore', 'mobile_number' => '+1-555-0107', 'description' => 'Bride-to-be, preparing for wedding'],
            ['first_name' => 'Amelia', 'last_name' => 'Taylor', 'mobile_number' => '+1-555-0108', 'description' => 'Student, prefers budget-friendly packages'],
            ['first_name' => 'Harper', 'last_name' => 'Anderson', 'mobile_number' => '+1-555-0109', 'description' => 'Busy mom, needs flexible scheduling'],
            ['first_name' => 'Evelyn', 'last_name' => 'Thomas', 'mobile_number' => '+1-555-0110', 'description' => 'Senior customer, enjoys relaxation services'],
            ['first_name' => 'Abigail', 'last_name' => 'Jackson', 'mobile_number' => '+1-555-0111', 'description' => 'Fashion enthusiast, loves nail art'],
            ['first_name' => 'Emily', 'last_name' => 'White', 'mobile_number' => '+1-555-0112', 'description' => 'Health-conscious, prefers organic treatments'],
            ['first_name' => 'Elizabeth', 'last_name' => 'Harris', 'mobile_number' => '+1-555-0113', 'description' => 'Corporate executive, values efficiency'],
            ['first_name' => 'Sofia', 'last_name' => 'Martin', 'mobile_number' => '+1-555-0114', 'description' => 'Artist, experimental with hair colors'],
            ['first_name' => 'Avery', 'last_name' => 'Thompson', 'mobile_number' => '+1-555-0115', 'description' => 'Fitness trainer, needs quick services']
        ];

        foreach ($customers as $index => $customer) {
            BookingCustomer::create([
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'email' => strtolower($customer['first_name'] . '.' . $customer['last_name'] . $userId . '@example.com'),
                'mobile_number' => $customer['mobile_number'],
                'customer' => $customer['first_name'] . ' ' . $customer['last_name'],
                'description' => $customer['description'],
                'created_by' => $userId,
                'creator_id' => $userId,
            ]);
        }
    }
}