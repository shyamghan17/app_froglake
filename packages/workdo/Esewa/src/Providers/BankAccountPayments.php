<?php

namespace Workdo\Esewa\Providers;

use Illuminate\Support\ServiceProvider;

class BankAccountPayments extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    public function boot()
    {
        view()->composer(['account::bankAccount.create','account::bankAccount.edit'], function ($view)
        {
            if(\Auth::check() && module_is_active('Esewa'))
            {
                $data = $view->getData();

                $payment_type = isset($data['bankAccount']) ? $data['bankAccount']->payment_name : null;

                $selected = ($payment_type == 'eSewa') ? 'selected' : '';

                $view->getFactory()->startPush('bank_payments', '<option value="eSewa" '.$selected.'>eSewa</option>');
            };
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
