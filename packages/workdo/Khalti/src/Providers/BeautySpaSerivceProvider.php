<?php

namespace Workdo\Khalti\Providers;

use App\Models\WorkSpace;
use Illuminate\Support\ServiceProvider;

class BeautySpaSerivceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot(){

        view()->composer(['beauty-spa-management::frontend.booking'], function ($view)
        {
                $slug = \Request::segment(1);
                $workspace        = WorkSpace::where('slug', $slug)->first();
                $company_settings = getCompanyAllSetting($workspace->created_by,$workspace->id);
                if(module_is_active('Khalti', $workspace->created_by) && ((isset($company_settings['khalti_payment_is_on']) ? $company_settings['khalti_payment_is_on']:'off') == 'on') && ($company_settings['khalti_public_key']) && ($company_settings['khalti_secret_key']))
                {
                    $view->getFactory()->startPush('beauty_payment', view('khalti::payment.beauty_payment',compact('slug','company_settings')));
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
