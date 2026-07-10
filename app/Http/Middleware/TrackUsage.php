<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class TrackUsage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Sadece başarılı GET isteklerini ve /admin veya /platform altındaki sayfaları logla
        if ($request->isMethod('get') && ($request->is('admin*') || $request->is('platform*')) && ! $request->ajax() && $response->getStatusCode() === 200) {
            try {
                // Basitlik için audits tablosunu 'hit' event'i ile kullanıyoruz
                DB::table('audits')->insert([
                    'user_type' => auth()->check() ? get_class(auth()->user()) : null,
                    'user_id' => auth()->id(),
                    'event' => 'hit',
                    'auditable_type' => 'App\Models\Platform', // Dummy type
                    'auditable_id' => 0,
                    'url' => $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Log hatası sistemi durdurmamalı
            }
        }

        return $response;
    }
}
