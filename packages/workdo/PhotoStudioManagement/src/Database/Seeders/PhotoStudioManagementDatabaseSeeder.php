<?php

namespace Workdo\PhotoStudioManagement\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCustomPage;

class PhotoStudioManagementDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if(config('app.run_demo_seeder'))
        {
            // Add here your demo data seeders
            $userId = User::where('email', 'company@example.com')->first()->id;
            PhotoStudioCustomPage::defaultdata($userId);
            (new DemoPhotoStudioGalleryTypeSeeder())->run($userId);
            (new DemoPhotoStudioSetupSeeder())->run($userId);
            (new DemoPhotoStudioEquipmentTagSeeder())->run($userId);
            (new DemoPhotoStudioEquipmentTypeSeeder())->run($userId);
            (new DemoPhotoStudioServiceCategorySeeder())->run($userId);
            (new DemoPhotoStudioTeamMemberSeeder())->run($userId);
            (new DemoPhotoStudioCameraKitSeeder())->run($userId);
            (new DemoPhotoStudioServiceSeeder())->run($userId);
            (new DemoPhotoStudioAppointmentSeeder())->run($userId);
            (new DemoPhotoStudioAppointmentPaymentSeeder())->run($userId);
            (new DemoContactSeeder())->run($userId);
            (new DemoSubscriberSeeder())->run($userId);
        }
    }
}