<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::user()->notifications()->latest()->get();

        return view('student.notifications', compact('notifications'));
    }

    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('student.notifications');
    }
}
