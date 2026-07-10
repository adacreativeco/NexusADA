<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders — Tüm yanıtlara güvenlik header'larını ekler.
 *
 * OWASP önerilerine uygun:
 * - HSTS (HTTPS zorunlu)
 * - X-Frame-Options (clickjacking koruma)
 * - X-Content-Type-Options (MIME sniffing koruma)
 * - Referrer-Policy (bilgi sızıntısı koruma)
 * - Permissions-Policy (tarayıcı API kısıtlama)
 * - COOP (cross-origin opener koruma)
 * - CSP güçlendirilmiş (upgrade + frame-ancestors)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HSTS — 1 yıl, subdomains dahil
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Clickjacking koruma
        $response->headers->set('X-Frame-Options', 'DENY');

        // MIME sniffing koruma
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer sızıntı koruma
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Tarayıcı API kısıtlama (kamera, mikrofon, geolocation, payment)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Cross-Origin Opener Policy
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // CSP güçlendir — mevcut upgrade-insecure-requests'e ek
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' data: https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https://www.google-analytics.com; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; upgrade-insecure-requests");

        return $response;
    }
}
