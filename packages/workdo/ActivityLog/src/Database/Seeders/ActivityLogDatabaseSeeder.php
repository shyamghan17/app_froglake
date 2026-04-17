<?php

namespace Workdo\ActivityLog\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ActivityLogDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if(config('app.run_demo_seeder'))
        {
            $userId = User::where('email', 'company@example.com')->first()->id;
            (new DemoActivityLogSeeder())->run($userId);
        }
    }
}