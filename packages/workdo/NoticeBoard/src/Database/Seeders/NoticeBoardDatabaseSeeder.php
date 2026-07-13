<?php

namespace Workdo\NoticeBoard\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NoticeBoardDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if (config('app.run_demo_seeder')) {
            // Add here your demo data seeders
            $userId = User::where('email', 'company@example.com')->first()->id;

            (new DemoNoticeSeeder())->run($userId);
            (new DemoNoticeCommentSeeder())->run($userId);
        }
    }
}
