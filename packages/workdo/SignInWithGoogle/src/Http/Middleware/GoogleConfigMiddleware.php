<?php

namespace Workdo\SignInWithGoogle\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GoogleConfigMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'googleConfig' => [
                'redirectUrl' => route('google.callback'),
                'isEnabled' => admin_setting('google_signin_enabled') === 'on',
            ]
        ]);

        return $next($request);
    }
}