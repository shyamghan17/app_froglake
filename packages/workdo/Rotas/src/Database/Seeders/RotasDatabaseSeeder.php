<?php

namespace Workdo\Rotas\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RotasDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if (config('app.run_demo_seeder')) {
            // Add here your demo data seeders
            $userId = User::where('email', 'company@example.com')->first()->id;
            (new DemoBranchSeeder())->run($userId);
            (new DemoDepartmentSeeder())->run($userId);
            (new DemoDesignationSeeder())->run($userId);
            (new DemoShiftSeeder())->run($userId);
            (new DemoLeaveTypeSeeder())->run($userId);
            (new DemoEmployeeDocumentTypeSeeder())->run($userId);
            (new DemoEmployeeSeeder())->run($userId);
            (new DemoLeaveApplicationSeeder())->run($userId);
            (new DemoAnnouncementCategorySeeder())->run($userId);
            (new DemoAnnouncementSeeder())->run($userId);
            (new DemoRotasDataSeeder())->run($userId);
        }
    }
}
