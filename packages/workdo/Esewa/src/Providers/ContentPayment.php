<?php

namespace Workdo\Esewa\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\ModuleFacade as Module;

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
                        $module = Module::find('Esewa');
                        $store = \Workdo\TVStudio\Entities\TVStudioStore::where('slug',$ids)->first();
                        $company_settings = getCompanyAllSetting($store->created_by,$store->workspace);

                        if(module_is_active('Esewa', $store->created_by) && ((isset($company_settings['esewa_payment_is_on']) ? $company_settings['esewa_payment_is_on']:'off') == 'on') && (isset($company_settings['esewa_merchant_id'])))
                        {
                            $view->getFactory()->startPush('content_payment', view('esewa::payment.content_payment',compact('store', 'module')));
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
