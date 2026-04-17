<?php

namespace Workdo\ActivityLog\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Workdo\ActivityLog\Models\AllActivityLog;
use Carbon\Carbon;

class DemoActivityLogSeeder extends Seeder
{
    public function run(int $userId): void
    {
        if (empty($userId) || AllActivityLog::where('created_by', $userId)->exists()) {
            return;
        }

        $users = User::where('created_by', $userId)->where('type', '!=', 'company')->get();
        if ($users->isEmpty()) {
            return;
        }

        $descriptions = [
            'Sales' => [
                'Account' => [
                    'New Account Created by the ',
                ]
            ],
            'Project' => [
                'Task' => [
                    'New Task created in project by the ',
                    'Task Completed by the ',
                    'Task Assigned by the '
                ]
            ],
            'Visitor' => [
                'Document' => [
                    'New Document Created by the ',
                ]
            ],
            'CRM' => [
                'Meeting' => [
                    'New Meeting created by the ',
                    'Meeting Completed by the '
                ]
            ],
            'HRM' => [
                'Document' => [
                    'New Document Created by the ',
                    'Employee Document Updated by the ',
                    'Document Approved by the '
                ]
            ]
        ];

        for ($i = 0; $i < 25; $i++) {
            $module = array_rand($descriptions);
            $subModule = array_rand($descriptions[$module]);
            $description = $descriptions[$module][$subModule][array_rand($descriptions[$module][$subModule])];

            $user = $users->random();
            
            AllActivityLog::create([
                'module' => $module,
                'sub_module' => $subModule,
                'description' => $description,
                'creator_id' => $user->id,
                'created_by' => $userId,
                'created_at' => Carbon::now()->subDays(rand(0, 90)),
            ]);
        }
    }
}