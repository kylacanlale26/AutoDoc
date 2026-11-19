<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class MicrosoftAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('microsoft')
            ->with(['prompt' => 'login'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();
            $microsoftId = $microsoftUser->getId();
            $email = $microsoftUser->getEmail();

            if (blank($microsoftId) || blank($email)) {
                throw new \RuntimeException('Microsoft did not return the required account identifiers.');
            }

            $microsoftName = $microsoftUser->getName() ?? $microsoftUser->getNickname();
            $displayName = $microsoftName ?: 'Microsoft User';
            $nameParts = $microsoftName
                ? preg_split('/\s+/', trim($microsoftName), 2)
                : [];

            $user = User::where('microsoft_id', $microsoftId)->first();
            $isNewAccount = false;

            if (! $user) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    $updates = ['microsoft_id' => $microsoftId];

                    if (blank($user->name)) {
                        $updates['name'] = $displayName;
                    }
                    if (blank($user->first_name) && filled($nameParts[0] ?? null)) {
                        $updates['first_name'] = $nameParts[0];
                    }
                    if (blank($user->last_name) && filled($nameParts[1] ?? null)) {
                        $updates['last_name'] = $nameParts[1];
                    }

                    $user->update($updates);
                } else {
                    $user = User::create([
                        'name' => $displayName,
                        'first_name' => $nameParts[0] ?? null,
                        'last_name' => $nameParts[1] ?? null,
                        'email' => $email,
                        'microsoft_id' => $microsoftId,
                        'account_role' => 'student',
                        'password' => bcrypt(Str::random(32)),
                    ]);
                    $isNewAccount = true;
                }
            }

            $role = strtolower(trim((string) $user->account_role));
            if (! in_array($role, ['student', 'admin'], true)) {
                return redirect()->route('welcome')->with('error', 'Your account does not have an authorized role.');
            }

            Auth::login($user);
            $request->session()->regenerate();

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return $isNewAccount || blank($user->profile_completed_at)
                ? redirect()->route('student.profile.complete')
                : redirect()->route('student.dashboard');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('welcome')
                ->with('error', 'Microsoft login failed. Please try again.');
        }
    }
}
