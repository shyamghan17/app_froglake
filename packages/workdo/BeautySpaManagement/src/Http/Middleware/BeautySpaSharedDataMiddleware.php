<?php

namespace Workdo\BeautySpaManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Models\BeautySetup;
use Workdo\BeautySpaManagement\Models\BeautyCustomPage;
use Workdo\BeautySpaManagement\Models\BeautyWorking;
use App\Models\User;
use App\Classes\Module;

class BeautySpaSharedDataMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->route()?->getName() ?? '', 'beauty-spa.')) {
            $userId = $this->getUserIdFromRequest($request);
            
            $user = User::find($userId);
            $userSlug = $request->route('userSlug');
            $sanitizedUserSlug = $userSlug ? htmlspecialchars($userSlug, ENT_QUOTES, 'UTF-8') : null;
            
            Inertia::share([
                'beautySpaSettings' => $this->getBeautySpaSettings($userId),
                'customPages' => $this->getCustomPages($userId),
                'companyAllSetting' => getCompanyAllSetting($userId, true),
                'userSlug' => $sanitizedUserSlug,
                'auth' => [
                    'user' => ['activatedPackages' => ActivatedModule($userId ?? null)],
                    'lang' => $user && $user->lang ? $user->lang : 'en',
                ],
                'packages' => (new Module())->allModules(),
                'imageUrlPrefix' => $user ? getImageUrlPrefix() : url('/'),
            ]);
        }

        return $next($request);
    }

    private function getUserIdFromRequest(Request $request): int
    {
        $userSlug = $request->route('userSlug');
        if ($userSlug) {
            try {
                $user = User::where('slug', $userSlug)->first();
                if ($user) {
                    return $user->id;
                }
            } catch (\Exception $e) {
                \Log::error('Database error in BeautySpaSharedDataMiddleware: ' . $e->getMessage());
                abort(500, 'Database error');
            }
        }
        
        abort(404, 'Beauty spa page not found');
    }

    private function getBeautySpaSettings($userId): array
    {
        try {
            $settings = BeautySetup::where('created_by', $userId)->pluck('value', 'key')->toArray();
            
            return [
                'brand_settings' => [
                    'logo' => $settings['logo'] ?? null,
                    'favicon' => $settings['favicon'] ?? null,
                    'footer_logo' => $settings['footer_logo'] ?? null,
                    'footer_text' => $settings['footer_text'] ?? null,
                    'footer_description' => $settings['footer_description'] ?? null,
                ],
                'banner_section' => json_decode($settings['banner_section'] ?? '{}', true),
                'home_section' => json_decode($settings['home_section'] ?? '{}', true),
                'about_section' => json_decode($settings['about_section'] ?? '{}', true),
                'feature_section' => json_decode($settings['feature_section'] ?? '[]', true),
                'testimonials' => json_decode($settings['testimonials'] ?? '[]', true),
                'contact_info' => json_decode($settings['contact_info'] ?? '{}', true),
                'social_links' => json_decode($settings['social_links'] ?? '{}', true),
                'working_hours' => $this->getWorkingHours($userId),
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching beauty spa settings: ' . $e->getMessage());
            return [
                'brand_settings' => [],
                'banner_section' => [],
                'home_section' => [],
                'about_section' => [],
                'feature_section' => [],
                'testimonials' => [],
                'contact_info' => [],
                'social_links' => [],
                'working_hours' => [],
            ];
        }
    }

    private function getCustomPages($userId): array
    {
        try {
            return BeautyCustomPage::where('created_by', $userId)
                ->get(['id', 'title', 'slug'])
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching custom pages: ' . $e->getMessage());
            return [];
        }
    }

    private function getWorkingHours($userId): array
    {
        try {
            return BeautyWorking::where('created_by', $userId)
                ->get(['day_of_week', 'opening_time', 'closing_time'])
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching working hours: ' . $e->getMessage());
            return [];
        }
    }
}