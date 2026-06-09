<?php

namespace Workdo\PhotoStudioManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSetup;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCustomPage;
use App\Models\User;
use App\Classes\Module;

class PhotoStudioSharedDataMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->route()?->getName() ?? '', 'photo-studio-management.frontend.')) {
            $userId = $this->getUserIdFromRequest($request);

            $user = User::find($userId);
            $userSlug = $request->route('userSlug');
            $sanitizedUserSlug = $userSlug ? htmlspecialchars($userSlug, ENT_QUOTES, 'UTF-8') : null;

            Inertia::share([
                'photoStudioSettings' => $this->getPhotoStudioSettings($userId),
                'customPages'         => $this->getCustomPages($userId),
                'companyAllSetting'   => getCompanyAllSetting($userId, true),
                'userSlug'            => $sanitizedUserSlug,
                'auth'                => [
                    'user' => ['activatedPackages' => ActivatedModule($userId ?? null)],
                    'lang' => $user && $user->lang ? $user->lang : 'en',
                ],
                'packages'        => (new Module())->allModules(),
                'imageUrlPrefix'  => $user ? getImageUrlPrefix() : url('/'),
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
                \Log::error('Database error in PhotoStudioSharedDataMiddleware: ' . $e->getMessage());
                abort(500, 'Database error');
            }
        }

        abort(404, 'Photo studio page not found');
    }

    private function getPhotoStudioSettings($userId): array
    {
        try {
            $settings = PhotoStudioSetup::where('created_by', $userId)->pluck('value', 'key')->toArray();

            return [
                'brand_settings' => [
                    'logo'               => $settings['logo'] ?? null,
                    'favicon'            => $settings['favicon'] ?? null,
                    'footer_logo'        => $settings['footer_logo'] ?? null,
                    'site_title'         => $settings['site_title'] ?? null,
                    'footer_text'        => $settings['footer_text'] ?? null,
                    'footer_description' => $settings['footer_description'] ?? null,
                ],
                'banner_section'   => json_decode($settings['banner_section'] ?? '{}', true),
                'about_section'    => json_decode($settings['about_section'] ?? '{}', true),
                'title_section'    => json_decode($settings['title_section'] ?? '{}', true),
                'testimonials'     => json_decode($settings['testimonials'] ?? '{}', true),
                'faq_section'      => json_decode($settings['faq_section'] ?? '{}', true),
                'media_section'    => json_decode($settings['media_section'] ?? '{}', true),
                'award_section'    => json_decode($settings['award_section'] ?? '{}', true),
                'gallery_section'  => json_decode($settings['gallery_section'] ?? '{}', true),
                'contact_section'  => json_decode($settings['contact_section'] ?? '{}', true),
                'footer_section'   => json_decode($settings['footer_section'] ?? '{}', true),
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching photo studio settings: ' . $e->getMessage());
            return [
                'brand_settings'  => [],
                'banner_section'  => [],
                'about_section'   => [],
                'title_section'   => [],
                'testimonials'    => [],
                'faq_section'     => [],
                'media_section'   => [],
                'award_section'   => [],
                'gallery_section' => [],
                'contact_section' => [],
                'footer_section'  => [],
            ];
        }
    }

    private function getCustomPages($userId): array
    {
        try {
            return PhotoStudioCustomPage::where('created_by', $userId)
                ->get(['id', 'title', 'slug'])
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching photo studio custom pages: ' . $e->getMessage());
            return [];
        }
    }
}
