<?php

namespace Workdo\Bookings\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\Bookings\Models\BookingCustomPage;
use Workdo\Bookings\Models\BookingSetting;

class BookingsDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if (config('app.run_demo_seeder')) {
            // Add here your demo data seeders
            $userId = User::where('email', 'company@example.com')->first()->id;

            if ($userId) {
                BookingCustomPage::defaultdata($userId);
                BookingSetting::defaultdata($userId);

                (new BookingBusinessHoursSeeder())->run($userId);
                (new BookingSocialLinkSeeder())->run($userId);
                (new BookingExtraServiceSeeder())->run($userId);
                (new BookingContactSeeder())->run($userId);
                (new BookingItemSeeder())->run($userId);
                (new BookingPackageSeeder())->run($userId);
                (new BookingStaffSeeder())->run($userId);
                (new BookingCustomerSeeder())->run($userId);
                (new BookingAppointmentSeeder())->run($userId);
                (new BookingReviewSeeder())->run($userId);
                (new DemoExtraServiceSeeder())->run($userId);
            }
        }
    }
}
