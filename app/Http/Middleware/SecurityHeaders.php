<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Force HTTPS redirect (only in production or when APP_FORCE_HTTPS is true)
        // Skip for local domains (.test, localhost, 127.0.0.1)
        $host = $request->getHost();
        $isLocalDomain = in_array($host, ['localhost', '127.0.0.1']) ||
            str_ends_with($host, '.test') ||
            str_ends_with($host, '.local');

        $forceHttps = env('APP_FORCE_HTTPS', false);
        if (! $request->secure() && ! $isLocalDomain && (app()->environment('production') || $forceHttps)) {
            return redirect()->secure($request->getRequestUri());
        }

        // Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS (Strict-Transport-Security) - Only in production with valid SSL
        // Skip for local domains (.test, localhost, 127.0.0.1)
        if ($request->secure() && app()->environment('production') && ! $isLocalDomain) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // COOP (Cross-Origin-Opener-Policy) - Only in production with valid SSL
        // COOP requires HTTPS with trusted certificate, so skip in local/development
        if (app()->environment('production') && $request->secure() && ! $isLocalDomain) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        // COEP (Cross-Origin-Embedder-Policy) - optional, can be relaxed if needed
        // $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Content Security Policy
        // Note: Trusted Types requires additional implementation in JavaScript
        // For now, we use unsafe-inline and unsafe-eval for compatibility
        // To enable Trusted Types, remove unsafe-inline/unsafe-eval and implement Trusted Types API
        $csp = "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://cdn.jsdelivr.net; ".
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.jsdelivr.net; ".
            "font-src 'self' https://fonts.gstatic.com data:; ".
            "img-src 'self' data: https: blob:; ".
            "media-src 'self' blob: https: http: data:; ".
            "connect-src 'self' https: http:; ".
            "frame-ancestors 'self'; ".
            "base-uri 'self'; ".
            "form-action 'self'; ".
            'upgrade-insecure-requests;';

        // Relax CSP for local development - remove upgrade-insecure-requests
        if ($isLocalDomain || app()->environment('local')) {
            $csp = str_replace('upgrade-insecure-requests;', '', $csp);
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
