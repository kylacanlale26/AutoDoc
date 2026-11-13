<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function logout(Request $request) {
        Auth::logout();
        $request -> session() -> invalidates();
        $request -> session() -> regenerateToken();
        return redirect -> route('welcome');
    }
}
