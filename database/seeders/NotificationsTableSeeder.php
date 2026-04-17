<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class NotificationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            'New User',
            'Sales Invoice',
            'Sales Invoice Return',
            'Purchase Invoice',
            'Purchase Invoice Return',
            'Helpdesk Ticket',
            'Helpdesk Ticket Reply',
        ];

        $permissions = [
            'manage-users',
              // add your permissions here
            'manage-sales-invoices',
            'manage-sales-return-invoices',
            'manage-purchase-invoices',
            'manage-purchase-return-invoices',
            'manage-email-helpdesk-tickets',
            'manage-email-helpdesk-replies',
        ];
        
        foreach($notifications as $key=>$n){
            $ntfy = Notification::where('action',$n)->where('type','mail')->where('module','general')->exists();
            if(!$ntfy){
                $new = new Notification();
                $new->action = $n;
                $new->status = 'on';
                $new->permissions = $permissions[$key];
                $new->module = 'general';
                $new->type = 'mail';
                $new->save();
            }
        }
    }
}
