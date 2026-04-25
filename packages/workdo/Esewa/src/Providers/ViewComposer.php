<?php

namespace Workdo\Esewa\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\ModuleFacade as Module;

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

                if(Module::isEnabled('Esewa') && isset($admin_settings['esewa_payment_is_on']) && $admin_settings['esewa_payment_is_on'] == 'on'
                 && !empty($admin_settings['esewa_merchant_id']) )
                {
                    $view->getFactory()->startPush('company_plan_payment', view('esewa::payment.plan_payment'));
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
