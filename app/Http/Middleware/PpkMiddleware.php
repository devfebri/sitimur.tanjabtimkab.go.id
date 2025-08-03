<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PpkMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kecualikan request Livewire
        if (
            $request->is('livewire/*') ||
            $request->header('X-Livewire')
        ) {
            return $next($request);
        }
        if (Auth::check()) {
            if (Auth::user()->role == 'ppk') {
                return $next($request);
            } else {
                // dd(auth()->user()->role);
                return redirect(url('/'));
            }
        } else {
            Auth::logout();
            return redirect(url('/'));
        }
    }
}
