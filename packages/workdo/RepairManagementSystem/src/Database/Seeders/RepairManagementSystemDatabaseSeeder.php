<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RepairManagementSystemDatabaseSeeder extends Seeder
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
            (new DemoRepairTechnicianSeeder())->run($userId);
            (new DemoRepairOrderRequestSeeder())->run($userId);
            (new DemoRepairWarrantySeeder())->run($userId);
            (new DemoRepairInvoiceSeeder())->run($userId);
        }
    }
}