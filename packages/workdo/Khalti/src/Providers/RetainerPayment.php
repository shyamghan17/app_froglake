<?php

namespace Workdo\Khalti\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\Account\Entities\BankAccount;

class RetainerPayment extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['retainer::retainer.retainerpay'], function ($view)
        {
            $route = \Request::route()->getName();
            if($route == "pay.retainer")
            {
                try {
                    $ids = \Request::segment(3);
                    if(!empty($ids))
                    {
                        try {
                            $id = \Illuminate\Support\Facades\Crypt::decrypt($ids);
                            $invoice = \Workdo\Retainer\Entities\Retainer::where('id',$id)->first();
                            $company_settings = getCompanyAllSetting( $invoice->created_by,$invoice->workspace);
                            $type = 'retainer';
                            $account = BankAccount::where(['created_by'=>$invoice->created_by,'workspace'=>$invoice->workspace])->where('payment_name','Khalti')->first();
                            if(module_is_active('Khalti', $invoice->created_by) && ((isset($company_settings['khalti_payment_is_on'])? $company_settings['khalti_payment_is_on'] : 'off')  == 'on') && (isset($company_settings['khalti_public_key'])) && (isset($company_settings['khalti_secret_key'])))
                            {
                                $view->getFactory()->startPush('retainer_payment_tab', view('khalti::payment.sidebar'));
                                $view->getFactory()->startPush('retainer_payment_div', view('khalti::payment.nav_containt_div',compact('type','invoice','company_settings','account')));
                            }
                        } catch (\Throwable $th)
                        {

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
