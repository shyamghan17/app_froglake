<?php

namespace Workdo\Khalti\Providers;

use Illuminate\Support\ServiceProvider;

class ContentPayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['tvstudio::storefront.*.checkout'], function ($view)
        {
            try {
                $ids = \Request::segment(2);
                if(!empty($ids))
                {
                    try {
                        $store = \Workdo\TVStudio\Entities\TVStudioStore::where('slug',$ids)->first();
                        $company_settings = getCompanyAllSetting($store->created_by,$store->workspace);
                        if(module_is_active('Khalti', $store->created_by) && ((isset($company_settings['khalti_payment_is_on']) ? $company_settings['khalti_payment_is_on']:'off') == 'on') && ($company_settings['khalti_public_key']) && ($company_settings['khalti_secret_key']))
                        {
                            $view->getFactory()->startPush('content_payment', view('khalti::payment.content_payment',compact('store','company_settings')));
                        }
                    } catch (\Throwable $th)
                    {

                    }
                }
            } catch (\Throwable $th) {

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
