<?php

namespace Workdo\Esewa\Providers;

use Illuminate\Support\ServiceProvider;

class RetainerPayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

     public function boot(){
        view()->composer(['retainer::retainer.retainerpay'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route =='pay.retainer')
            {
                try {
                    $ids = \Request::segment(3);
                    if(!empty($ids))
                    {
                        $id = \Illuminate\Support\Facades\Crypt::decrypt($ids);

                        $invoice = \Workdo\Retainer\Entities\Retainer::where('id',$id)->first();
                        $type = 'retainer';
                        $company_settings = getCompanyAllSetting( $invoice->created_by,$invoice->workspace);
                        if(module_is_active('Esewa', $invoice->created_by) && ($company_settings['esewa_payment_is_on']  == 'on') && ($company_settings['esewa_merchant_id']) )
                        {
                            $view->getFactory()->startPush('retainer_payment_tab', view('esewa::payment.sidebar'));
                            $view->getFactory()->startPush('retainer_payment_div', view('esewa::payment.nav_content_div',compact('type','invoice','company_settings')));
                        }
                    }
                } catch (\Throwable $th) {

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
