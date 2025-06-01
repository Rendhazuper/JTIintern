<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (Auth::user()->role !== $role) {
            // Redirect based on actual role
            if (Auth::user()->role === 'admin') {
                return redirect('/dashboard');
            } else if (Auth::user()->role === 'mahasiswa') {
                return redirect('/mahasiswa/dashboard');
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}