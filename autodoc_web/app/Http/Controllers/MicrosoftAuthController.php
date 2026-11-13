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
            $user = User::where(
                'microsofty_id',
                $microsoftUser -> getId()
            ) -> first();
            if (!$user) {
                $user = User::where(
                    'email',
                    $microsoftUser -> getEmail()
                ) -> first();

                if ($user) {
                    $user -> update([
                        'microsoft_id' => $microsoftUser -> getId(),
                    ]);
                } else {
                    $user = User::create ([
                        'name' => $microsoftUser -> getName()
                            ?? $microsoftUser -> getNickname()
                            ?? 'Microsoft User',
                        'email' => $microsoftUser -> getEmail(),
                        'microsoft_id' => $microsoftUser -> getId(),
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
