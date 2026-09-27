<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthenticationController extends Controller
{
    public function show()
    {
        return view('authentication');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'array', 'size:4'],
            'code.*' => ['required', 'digits:1'],
        ]);
        $enteredCode = implode('', $request->code);
        $storedCode = session('verification_code');
        $expiresAt = session('verification_code_expires');

        if (!$storedCode || !$expiresAt) {
            return back()->withErrors([
                'code' => 'Invalid code. Try again.'
            ]);
        }

        if (now()->greaterThan($expiresAt)) {

            session()->forget([
                'verification_code',
                'verification_code_expires'
            ]);

            return back()->withErrors([
                'code' => 'Invalid code. Try again.'
            ]);
        }

        if (!Hash::check($enteredCode, $storedCode)) {
            return back()->withErrors([
                'code' => 'Invalid code. Try again.'
            ]);
        }

        session()->forget([
            'verification_code',
            'verification_code_expires'
        ]);

        Auth::loginUsingId(session('verification_user_id'));
        session()->forget('verification_user_id');
        return redirect()->route('dashboard');
    }
}