<?php

namespace Workdo\Esewa\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'Esewa';
        $menu = $event->menu;
        $menu->add([
           'title' => __('Esewa'),
            'name' => 'esewa',
            'order' => 1430,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'home',
            'navigation' => 'esewa-payment',
            'module' => $module,
            'permission' => 'esewa manage'
        ]);
    }
}
