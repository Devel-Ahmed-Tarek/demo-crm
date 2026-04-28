<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLeadApiToken
{
    /**
     * يقبل Authorization: Bearer <token> أو X-Api-Key: <token>
     * يُعرّف في .env: LEAD_API_TOKEN
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('lead_api.token');
        if (! is_string($expected) || $expected === '') {
            return response()->json([
                'success' => false,
                'message' => 'Lead API is not configured (LEAD_API_TOKEN).',
            ], 503);
        }

        $bearer = $request->bearerToken();
        $headerKey = $request->header('X-Api-Key');
        $provided = is_string($bearer) && $bearer !== '' ? $bearer : (is_string($headerKey) ? $headerKey : '');

        if (! hash_equals($expected, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
