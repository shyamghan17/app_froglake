<?php

namespace Workdo\Bookings\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Workdo\Bookings\Models\BookingSetting;
use Workdo\Bookings\Models\BookingSocialLink;
use App\Models\User;
use App\Classes\Module;

class BookingSharedDataMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->route()?->getName() ?? '', 'booking.')) {
            $userId = $this->getUserIdFromRequest($request);
            
            $user = User::find($userId);
            $userSlug = $request->route('userSlug');
            // Sanitize userSlug to prevent XSS
            $sanitizedUserSlug = $userSlug ? htmlspecialchars($userSlug, ENT_QUOTES, 'UTF-8') : null;
            
            Inertia::share([
                'bookingSettings' => $this->getBookingSettings($userId),
                'companyAllSetting' => getCompanyAllSetting($userId, true),
                'userSlug' => $sanitizedUserSlug,
                'auth' => [
                    'user' =>  ['activatedPackages' => ActivatedModule($userId ?? null)],
                ],
                'packages' => (new Module())->allModules(),
                'imageUrlPrefix' => $user ? getImageUrlPrefix() : url('/'),
            ]);
        }

        return $next($request);
    }

    private function getUserIdFromRequest(Request $request): int
    {        
        // Fallback to slug-based detection
        $userSlug = $request->route('userSlug');
        if ($userSlug) {
            try {
                $user = User::where('slug', $userSlug)->first();
                if ($user) {
                    return $user->id;
                }
            } catch (\Exception $e) {
                \Log::error('Database error in BookingSharedDataMiddleware: ' . $e->getMessage());
                // Redirect to 404 instead of aborting
                if (request()->route() && !str_contains(request()->route()->getName() ?? '', '404')) {
                    return redirect()->route('booking.404', ['userSlug' => $userSlug])->send();
                }
            }
        }
        
        // For 404 routes, return a default user ID or throw exception
        if (request()->route() && str_contains(request()->route()->getName() ?? '', '404')) {
            return 1; // Default user ID for 404 page
        }
        
        abort(404, 'Booking page not found');
    }

    private function getBookingSettings($userId): array
    {
        try {
            $settings = BookingSetting::getSettings($userId);
            if (!$settings) {
                \Log::warning('No booking settings found for user: ' . $userId);
                return [
                    'navigation_items' => [],
                    'header_settings' => [],
                    'footer_settings' => [],
                    'color_settings' => [],
                    'page_settings' => []
                ];
            }
            $socialLinks = BookingSocialLink::where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get()->toArray();
            return [
                'navigation_items' => $settings->config_data['general']['header']['navigation_items'] ?? [],
                'header_settings' => $settings->config_data['general']['header'] ?? [],
                'footer_settings' => $settings->config_data['general']['footer'] ?? [],
                'color_settings' => $settings->config_data['general']['colors'] ?? [],
                'page_settings' => $settings->config_data ?? [],
                'social_links' => $socialLinks ?? [],
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching booking settings: ' . $e->getMessage());
            return [
                'navigation_items' => [],
                'header_settings' => [],
                'footer_settings' => [],
                'color_settings' => [],
                'page_settings' => []
            ];
        }
    }
}