<?php

namespace Workdo\SuggestionBox\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SuggestionBoxDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);

        if(config('app.run_demo_seeder'))
        {
            $userId = User::where('email', 'company@example.com')->first()->id;
            (new DemoSuggestionCategorySeeder())->run($userId);
            (new DemoSuggestionSeeder())->run($userId);
            (new DemoSuggestionVoteSeeder())->run($userId);
            (new DemoSuggestionViewSeeder())->run($userId);
            (new DemoSuggestionStatusHistorySeeder())->run($userId);

        }
    }
}