<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    public function redirect() {
        return Socialite::driver('microsoft')
        -> with ([
            'prompt' => 'login',
        ])
        -> redirect();
    }

    public function callback () {
        try {
            $microsoftUser = Socialite::driver('microsoft') -> user();
            $microsoftName = $microsoftUser->getName()
                ?? $microsoftUser->getNickname();
            $displayName = $microsoftName ?? 'Microsoft User';
            $nameParts = $microsoftName
                ? preg_split('/\\s+/', trim($microsoftName), 2)
                : [];
            $user = User::where(
                'microsoft_id',
                $microsoftUser -> getId()
            ) -> first();
            if (!$user) {
                $user = User::where( 
                    'email',
                    $microsoftUser -> getEmail()
                ) -> first();

                if ($user) {
                    $updates = [
                        'microsoft_id' => $microsoftUser -> getId(),
                    ];

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
                    $user = User::create ([
                        'name' => $displayName,
                        'first_name' => $nameParts[0] ?? null,
                        'last_name' => $nameParts[1] ?? null,
                        'email' => $microsoftUser -> getEmail(),
                        'microsoft_id' => $microsoftUser -> getId(),
                        'account_role' => 'student',
                        'password' => bcrypt(
                            Str::random(32)
                        ),
                    ]);
                }
            }

            Auth::login($user);
            
            if ($user->account_role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect() -> route('student.dashboard');

        } catch (\Exception $e) {
            return redirect()
                -> route('welcome')
                -> with(
                    'error',
                    'Microsoft login failed. Please try again.'
                );
        }
    }
}
