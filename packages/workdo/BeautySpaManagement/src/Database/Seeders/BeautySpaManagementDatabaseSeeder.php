<?php

namespace Workdo\BeautySpaManagement\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BeautySpaManagementDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if (config('app.run_demo_seeder')) {
            // Add here your demo data seeders
            $userId = User::where('email', 'company@example.com')->first()->id;

            (new DemoBeautySetupSeeder())->run($userId);
            (new DemoBeautyGiftCardSeeder())->run($userId);
            (new DemoBeautyServiceTypeSeeder())->run($userId);
            (new DemoBeautyTrainingSeeder())->run($userId);
            (new DemoBeautyCertificationSeeder())->run($userId);
            (new DemoBeautyWorkingSeeder())->run($userId);
            (new DemoBeautyServiceSeeder())->run($userId);
            (new DemoBeautyMembershipSeeder())->run($userId);
            (new DemoBeautyServiceOfferSeeder())->run($userId);
            (new DemoBeautyLoyaltyProgramSeeder())->run($userId);
            (new DemoBeautyBookingSeeder())->run($userId);
            (new DemoBeautyPaymentSeeder())->run($userId);
            (new DemoBeautyContactSeeder())->run($userId);
            (new DemoBeautyReviewSeeder())->run($userId);
            (new DemoBeautySubscriberSeeder())->run($userId);
        }
    }
}
