<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();
        if (! is_string($plainToken) || $plainToken === '') {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token = MobileApiToken::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $token || $token->expires_at->isPast()) {
            $token?->delete();

            return response()->json(['message' => 'Your mobile session has expired. Sign in again.'], 401);
        }

        $user = $token->user;
        if (! $user || strtolower(trim((string) $user->account_role)) !== 'student') {
            return response()->json(['message' => 'This mobile app is available to student accounts only.'], 403);
        }

        $token->forceFill(['last_used_at' => now()])->saveQuietly();
        $request->attributes->set('mobileApiToken', $token);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
