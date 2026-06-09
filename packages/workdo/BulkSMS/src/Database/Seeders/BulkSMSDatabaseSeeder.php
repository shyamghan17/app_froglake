<?php

namespace Workdo\BulkSMS\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class BulkSMSDatabaseSeeder extends Seeder
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
            (new DemoBulkSmsContactSeeder())->run($userId);
            (new DemoBulkSmsGroupSeeder())->run($userId);
            (new DemoSingleSmsSeeder())->run($userId);
            (new DemoBulksmsSendSeeder())->run($userId);

        }
    }
}