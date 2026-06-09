<?php

namespace Workdo\Rotas\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Classes\Module;

class RotaSharedDataMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->route()?->getName() ?? '', 'rotas.frontend.')) {
            $userId = $this->getUserIdFromRequest($request);
            
            $user = User::find($userId);
            $userSlug = $request->route('userSlug');
            $sanitizedUserSlug = $userSlug ? htmlspecialchars($userSlug, ENT_QUOTES, 'UTF-8') : null;
            
            
            Inertia::share([
                'companyAllSetting' => getCompanyAllSetting($userId),
                'userSlug' => $sanitizedUserSlug,
                'auth' => [
                    'user' => ['activatedPackages' => ActivatedModule($userId ?? null)],
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
                abort(500, 'Database error');
            }
        }
        
        abort(404, 'Rotas page not found');
    }
}