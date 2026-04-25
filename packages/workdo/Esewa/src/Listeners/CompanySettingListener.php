<?php

namespace Workdo\Esewa\Listeners;
use App\Events\CompanySettingEvent;

class CompanySettingListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingEvent $event): void
    {
        $module = 'Esewa';
        $methodName = 'index';
        $controllerClass = "Workdo\\Esewa\\Http\\Controllers\\Company\\SettingsController";
        if (class_exists($controllerClass)) {
            $controller = \App::make($controllerClass);
            if (method_exists($controller, $methodName)) {
                $html = $event->html;
                $settings = $html->getSettings();
                $output =  $controller->{$methodName}($settings);
                $html->add([
                    'html' => $output->toHtml(),
                    'order' => 1430,
                    'module' => $module,
                    'permission' => 'esewa manage'
                ]);
            }
        }
    }
}
