<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureTenantAccess — /admin/* rotalarını kontrol eder.
 *
 * Geçiş koşulları:
 * 1. Kullanıcı webmaster/super_admin → HER ZAMAN geçer (platform sahibi)
 * 2. Kullanıcının tenant_id'si varsa → geçer (normal tenant kullanıcı)
 * 3. Hiçbiri → tenant'a atanmamış kullanıcı, erişim yok
 */
class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Platform sahibi → her zaman geçer, tüm panellere erişir
        if ($user->isWebmaster()) {
            return $next($request);
        }

        // Tenant kullanıcısı → geçer
        if ($user->tenant_id) {
            $tenant = $user->tenant;

            // Eğer kiracı onay bekliyorsa ve kullanıcı bekleyenler sayfasında değilse yönlendir
            if ($tenant && $tenant->status === 'pending' && !$request->routeIs('admin.waiting-approval')) {
                return redirect()->route('admin.waiting-approval');
            }

            return $next($request);
        }

        // Hiçbir koşul sağlanmadı — tenant'a atanmamış sıradan kullanıcı
        abort(403, 'Hesabınız bir ajansa atanmamış.');
    }
}
