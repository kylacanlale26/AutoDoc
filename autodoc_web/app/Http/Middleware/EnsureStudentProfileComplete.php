<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user()
            && blank($request->user()->profile_completed_at)
            && ! $request->routeIs('student.profile.complete', 'student.profile.complete.store')
        ) {
            return redirect()->route('student.profile.complete');
        }

        return $next($request);
    }
}
