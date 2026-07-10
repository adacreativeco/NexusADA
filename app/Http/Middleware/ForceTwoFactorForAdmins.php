<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force 2FA setup for platform administrators (webmaster, super_admin).
 *
 * If a platform admin hasn't set up 2FA yet, redirect them to the setup page.
 * Regular tenant users are not affected — 2FA is optional for them.
 */
class ForceTwoFactorForAdmins
{
    /**
     * Routes that should be accessible even without 2FA setup.
     */
    private array $exceptRoutes = [
        'admin.two-factor.setup',
        'admin.two-factor.verify',
        'admin.two-factor.confirm',
        'admin.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Only enforce for platform admins
        if (!$user->isWebmaster()) {
            return $next($request);
        }

        // If user already has 2FA enabled, let them through
        if ($user->two_factor_enabled) {
            return $next($request);
        }

        // Allow access to 2FA setup and logout routes
        $currentRoute = $request->route()?->getName();
        if ($currentRoute && in_array($currentRoute, $this->exceptRoutes)) {
            return $next($request);
        }

        // Redirect to 2FA setup
        return redirect()->route('admin.two-factor.setup')
            ->with('warning', 'Platform yöneticileri için iki faktörlü doğrulama zorunludur. Lütfen kurulumu tamamlayın.');
    }
}
