<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['login'])
            ->orWhere('login_id', $credentials['login'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['login' => 'Email, NIP, atau password tidak sesuai.'])
                ->onlyInput('login');
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        ActivityLog::create(['user_id' => Auth::id(), 'action' => 'login', 'route' => 'login.store', 'method' => 'POST', 'ip_address' => $request->ip(), 'description' => 'User berhasil login']);

        if (Auth::user()->status_akun === 'suspend') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login' => 'Akun sedang disuspend. Hubungi Super Admin.'])
                ->onlyInput('login');
        }

        $homeRoute = Auth::user()->homeRoute();

        return redirect()->intended(route($homeRoute));
    }

    public function destroy(Request $request)
    {
        if ($request->user()) {
            ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'logout', 'route' => 'logout', 'method' => 'POST', 'ip_address' => $request->ip(), 'description' => 'User keluar dari sistem']);
        }
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
