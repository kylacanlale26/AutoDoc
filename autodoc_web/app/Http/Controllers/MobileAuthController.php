<?php

namespace App\Http\Controllers;

use App\Models\MobileApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MobileAuthController extends Controller
{
    public function config(): JsonResponse
    {
        return response()->json([
            'client_id' => config('services.microsoft.client_id'),
            'tenant' => config('services.microsoft.tenant') ?: 'organizations',
            'android_redirect_uri' => config('services.microsoft.mobile_android_redirect_uri'),
            'ios_redirect_uri' => config('services.microsoft.mobile_ios_redirect_uri'),
            'graph_scopes' => ['openid', 'profile', 'email', 'offline_access', 'User.Read'],
        ]);
    }

    public function microsoft(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'access_token' => ['required', 'string', 'max:20000'],
        ]);

        try {
            $graphResponse = Http::withToken($validated['access_token'])
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(12)
                ->get('https://graph.microsoft.com/v1.0/me', [
                    '$select' => 'id,displayName,givenName,surname,mail,userPrincipalName',
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Could not contact Microsoft to verify this account.'], 502);
        }

        if (! $graphResponse->successful()) {
            return response()->json(['message' => 'Microsoft did not accept the sign-in token. Sign in again.'], 401);
        }

        $profile = $graphResponse->json();
        $microsoftId = trim((string) data_get($profile, 'id'));
        $email = trim((string) (data_get($profile, 'mail') ?: data_get($profile, 'userPrincipalName')));
        $displayName = trim((string) data_get($profile, 'displayName'));

        if ($microsoftId === '' || $email === '') {
            return response()->json(['message' => 'Microsoft did not return the account details AutoDoc requires.'], 422);
        }

        $user = DB::transaction(function () use ($profile, $microsoftId, $email, $displayName): User {
            $user = User::query()->where('microsoft_id', $microsoftId)->first();
            $nameParts = $displayName !== '' ? preg_split('/\s+/', $displayName, 2) : [];

            if (! $user) {
                $user = User::query()->where('email', $email)->first();
                if ($user) {
                    $updates = ['microsoft_id' => $microsoftId];
                    if (blank($user->name)) {
                        $updates['name'] = $displayName ?: 'Microsoft User';
                    }
                    if (blank($user->first_name)) {
                        $updates['first_name'] = data_get($profile, 'givenName') ?: ($nameParts[0] ?? null);
                    }
                    if (blank($user->last_name)) {
                        $updates['last_name'] = data_get($profile, 'surname') ?: ($nameParts[1] ?? null);
                    }
                    $user->update($updates);
                } else {
                    $user = User::create([
                        'name' => $displayName ?: 'Microsoft User',
                        'first_name' => data_get($profile, 'givenName') ?: ($nameParts[0] ?? null),
                        'last_name' => data_get($profile, 'surname') ?: ($nameParts[1] ?? null),
                        'email' => $email,
                        'microsoft_id' => $microsoftId,
                        'account_role' => 'student',
                        'password' => bcrypt(Str::random(48)),
                    ]);
                }
            }

            return $user;
        });

        if (strtolower(trim((string) $user->account_role)) !== 'student') {
            return response()->json(['message' => 'This mobile app is available to student accounts only.'], 403);
        }

        $plainToken = Str::random(80);
        $expiresAt = now()->addDays(30);
        MobileApiToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => $this->userPayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->attributes->get('mobileApiToken')?->delete();

        return response()->json(['message' => 'Signed out.']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'student_number' => $user->student_number,
            'email' => $user->email,
            'course' => $user->course,
            'year_level' => $user->year_level,
            'section' => $user->section,
        ];
    }
}
