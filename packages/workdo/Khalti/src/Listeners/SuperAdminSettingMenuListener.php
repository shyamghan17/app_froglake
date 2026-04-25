<?php

namespace Workdo\Khalti\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'Khalti';
        $menu = $event->menu;
        $menu->add([
            'title' => 'Khalti',
            'name' => 'khalti',
            'order' => 1250,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'khalti-sidenav',
            'module' => $module,
            'permission' => 'khalti payment manage'
        ]);
    }
}
