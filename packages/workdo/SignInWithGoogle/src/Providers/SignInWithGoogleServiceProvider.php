<?php

namespace Workdo\SignInWithGoogle\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Workdo\SignInWithGoogle\Http\Middleware\GoogleConfigMiddleware;

class SignInWithGoogleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerTranslations();
        $routesPath = __DIR__.'/../Routes/web.php';
        if (file_exists($routesPath)) {
            $this->loadRoutesFrom($routesPath);
        }
        
        $migrationsPath = __DIR__.'/../Database/Migrations';
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }

        // Configure Google OAuth settings dynamically
        $this->configureGoogleOAuth();
        
        // Register middleware
        $this->app['router']->pushMiddlewareToGroup('web', GoogleConfigMiddleware::class);
    }

    public function register(): void
    {
        //
    }

    private function configureGoogleOAuth(): void
    {
        // Only configure if not already done and if we're handling Google auth routes
        if (!app()->bound('google.oauth.configured') && $this->shouldConfigureGoogle()) {
            try {
                if (class_exists(Setting::class)) {
                    $clientId = admin_setting('google_client_id');

                    $clientSecret = admin_setting('google_client_secret');

                    if ($clientId && $clientSecret) {
                        $socialite = app('Laravel\Socialite\Contracts\Factory');
                        $socialite->extend('google', function ($app) use ($clientId, $clientSecret, $socialite) {
                            $config = [
                                'client_id' => $clientId,
                                'client_secret' => $clientSecret,
                                'redirect' => url('/auth/google/callback'),
                            ];
                            return $socialite->buildProvider('Laravel\Socialite\Two\GoogleProvider', $config);
                        });
                        
                        app()->instance('google.oauth.configured', true);
                    }
                }
            } catch (\Exception $e) {
                // Silently handle exceptions during boot
            }
        }
    }

    private function shouldConfigureGoogle(): bool
    {
        $request = request();
        return $request && (
            str_contains($request->path(), 'auth/google') ||
            str_contains($request->path(), 'login') || str_contains($request->path(), 'register')
        );
    }
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        // Load from main app lang folder (all languages)
        $mainLangPath = resource_path('lang');
        if (is_dir($mainLangPath)) {
            $this->loadJsonTranslationsFrom($mainLangPath);
        }

        // Load from package lang folder (fallback)
        $packageLangPath = __DIR__.'/../Resources/lang';
        if (is_dir($packageLangPath)) {
            $this->loadJsonTranslationsFrom($packageLangPath);
        }
    }
}