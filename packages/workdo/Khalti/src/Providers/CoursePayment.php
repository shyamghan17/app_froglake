<?php

namespace Workdo\Khalti\Providers;

use Illuminate\Support\ServiceProvider;

class CoursePayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['lms::storefront.*.checkout'], function ($view)
        {
            try {
                $ids = \Request::segment(1);
                if(!empty($ids))
                {
                    try {
                        $store = \Workdo\LMS\Entities\Store::where('slug',$ids)->first();
                        $company_settings = getCompanyAllSetting($store->created_by,$store->workspace);
                        if(module_is_active('Khalti', $store->created_by) && ((isset($company_settings['khalti_payment_is_on']) ? $company_settings['khalti_payment_is_on']:'off') == 'on') && ($company_settings['khalti_public_key']) && ($company_settings['khalti_secret_key']))
                        {
                            $view->getFactory()->startPush('course_payment', view('khalti::payment.course_payment',compact('store','company_settings')));
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
