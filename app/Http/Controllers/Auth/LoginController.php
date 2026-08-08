<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private function portalRedirect(): string
    {
        $user = Auth::user();
        if ($user->hasRole('parent'))  return route('portal.parent.dashboard');
        if ($user->hasRole('student')) return route('portal.student.dashboard');
        return route('dashboard');
    }

    private function roleLabel(?\App\Models\User $user): string
    {
        if (!$user) return '';
        $role = $user->getRoleNames()->first();
        return $role ? ucwords(str_replace('_', ' ', $role)) : '';
    }

    public function landing()
    {
        return view('landing');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->portalRedirect());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return redirect()->intended($this->portalRedirect());
        }

        return back()->withErrors(['email' => 'Invalid credentials. Please try again.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
