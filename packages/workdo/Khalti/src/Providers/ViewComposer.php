<?php

namespace Workdo\Khalti\Providers;

use App\Facades\ModuleFacade as Module;
use Illuminate\Support\ServiceProvider;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['plans.marketplace','plans.planpayment'], function ($view)
        {
            if(\Auth::check())
            {
                $admin_settings = getAdminAllSetting();

                if(Module::isEnabled('Khalti') && isset($admin_settings['khalti_payment_is_on']) && $admin_settings['khalti_payment_is_on'] == 'on' && !empty($admin_settings['khalti_public_key']) && !empty($admin_settings['khalti_secret_key']))
                {
                    $view->getFactory()->startPush('company_plan_payment', view('khalti::payment.plan_payment',compact('admin_settings')));
                }
            }
        });
    }
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
