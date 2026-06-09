<?php

namespace Workdo\Bookings\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\Bookings\Models\BookingSetting;
use App\Models\User;

class BookingSettingSeeder extends Seeder
{
    public function run()
    {
        // Get all company type users
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $user) {
            // Check if booking settings already exist for this user
            $existingSettings = BookingSetting::where('created_by', $user->id)->first();
            
            if (!$existingSettings) {
                BookingSetting::create([
                    'config_data' => BookingSetting::getDefaultConfig(),
                    'created_by' => $user->id
                ]);
            }
        }
        
        // Also create default settings for user ID 1 if it doesn't exist
        $defaultSettings = BookingSetting::where('created_by', 1)->first();
        if (!$defaultSettings) {
            BookingSetting::create([
                'config_data' => BookingSetting::getDefaultConfig(),
                'created_by' => 1
            ]);
        }
    }
}