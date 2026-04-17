<?php

namespace Workdo\SignInWithGoogle\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SignInWithGoogleDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        $this->call(MarketplaceSettingSeeder::class);
    }
}