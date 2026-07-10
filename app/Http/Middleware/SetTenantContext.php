<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetTenantContext — Request'e tenant bilgisini ekler.
 *
 * Bu middleware auth user'ın tenant_id'sini app config'e yazar.
 * BelongsToTenant trait bu bilgiyi global scope'ta kullanır.
 *
 * Platform (webmaster) route'larında tenant_id null olduğu için
 * tüm veriler görünür kalır.
 */
class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            config(['app.tenant_id' => $request->user()->tenant_id]);
        }

        return $next($request);
    }
}
