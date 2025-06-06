<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PasswordResetNotification;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return View
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Tambahkan debug info lengkap
            $user = Auth::user();
            Log::info('User detail after login', [
                'id' => $user->id ?? 'null',
                'id_user' => $user->id_user ?? 'not set',
                'email' => $user->email,
                'role' => $user->role,
                'class' => get_class($user),
                'attributes' => is_object($user) ? (array)$user : 'not an object' // Ganti getAttributes()
            ]);

            // Reroute berdasarkan role
            if ($user->role === 'superadmin' || $user->role === 'admin') {
                return redirect('/dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Kredensial tidak valid']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */ public function logout(Request $request)
    {
        // Just handle standard web logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
