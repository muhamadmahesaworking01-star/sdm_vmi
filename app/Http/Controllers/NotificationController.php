<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $announcements = $user->role === 'super_admin'
            ? Announcement::latest('published_at')->take(10)->get()
            : Announcement::whereIn('target_role', ['semua', $user->role])->latest('published_at')->take(10)->get();
        return view('notifications.index', compact('announcements'));
    }
}
