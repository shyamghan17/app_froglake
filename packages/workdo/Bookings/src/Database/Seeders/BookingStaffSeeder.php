<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingStaff;
use Workdo\ProductService\Models\ProductServiceItem;
use App\Models\User;

class BookingStaffSeeder extends Seeder
{
    public function run($userId)
    {
        if (!empty($userId)) {
            $this->createStaffForUser($userId);
        }
    }
    
    private function createStaffForUser($userId)
    {
        if (BookingStaff::where('created_by', $userId)->exists()) {
            return;
        }
        
        $items = ProductServiceItem::where('created_by', $userId)
            ->where('type', 'bookings')
            ->pluck('id')
            ->toArray();
            
        if (empty($items)) return;
 
        // Get existing staff role users
        $staffUsers = User::where('created_by', $userId)
            ->where(function($query) {
                $query->where('type', 'staff')
                      ->orWhereHas('roles', function($q) {
                          $q->where('name', 'staff');
                      });
            })
            ->get();
            
        if ($staffUsers->isEmpty()) return;
        
        foreach ($staffUsers as $index => $staffUser) {
            // Assign random items to each staff member
            $randomItems = collect($items)->random(min(3, count($items)))->toArray();
            
            BookingStaff::create([
                'staff_id' => $staffUser->id,
                'item_ids' => implode(',', $randomItems),
                'created_by' => $userId,
                'creator_id' => $userId,
            ]);
        }
    }
}