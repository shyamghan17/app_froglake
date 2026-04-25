<?php

namespace Workdo\Khalti\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\Khalti\Providers\EventServiceProvider;
use Workdo\Khalti\Providers\RouteServiceProvider;

class KhaltiServiceProvider extends ServiceProvider
{

    protected $moduleName = 'Khalti';
    protected $moduleNameLower = 'khalti';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(BeautySpaSerivceProvider::class);
        $this->app->register(ContentPayment::class);
        $this->app->register(CoursePayment::class);
        $this->app->register(EventShowBookingServiceProvider::class);
        $this->app->register(FacilitiesSerivceProvider::class);
        $this->app->register(HotelRoomBookingPayment::class);
        $this->app->register(MembershipPlanPayment::class);
        $this->app->register(RetainerPayment::class);
        $this->app->register(ViewComposer::class);
        $this->app->register(InvoicePayment::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'khalti');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerTranslations();
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(__DIR__.'/../Resources/lang');
        }
    }
}