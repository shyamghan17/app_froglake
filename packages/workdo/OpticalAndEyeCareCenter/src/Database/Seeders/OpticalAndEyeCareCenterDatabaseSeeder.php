<?php

namespace Workdo\OpticalAndEyeCareCenter\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\OpticalAndEyeCareCenter\Helpers\OpticalUtility;
use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Workdo\OpticalAndEyeCareCenter\Models\EyeTestPrescription;
use Workdo\OpticalAndEyeCareCenter\Models\EyeCareAppoinment;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearItem;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;

class OpticalAndEyeCareCenterDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if(config('app.run_demo_seeder'))
        {
            $user = User::where('email', 'company@example.com')->first();
            if($user)
            {
                $userId = $user->id;
                OpticalUtility::defaultdata($userId);

                if(OpticalDoctor::where('created_by', $userId)->doesntExist()) {
                    (new DemoOpticalDoctorSeeder())->run($userId);
                }
                if(EyePatient::where('created_by', $userId)->doesntExist()) {
                    (new DemoEyePatientSeeder())->run($userId);
                }
                if(EyeTestPrescription::where('created_by', $userId)->doesntExist()) {
                    (new DemoEyeTestPrescriptionSeeder())->run($userId);
                }
                if(EyeCareAppoinment::where('created_by', $userId)->doesntExist()) {
                    (new DemoEyeCareAppoinmentSeeder())->run($userId);
                }
                if(EyewearItem::where('created_by', $userId)->doesntExist()) {
                    (new DemoEyewearItemSeeder())->run($userId);
                }
                if(EyewearOrder::where('created_by', $userId)->doesntExist()) {
                    (new DemoEyewearOrderSeeder())->run($userId);
                }
            }
        }
    }
}
