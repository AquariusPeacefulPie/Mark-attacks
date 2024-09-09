<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est un administrateur
        if (auth()->check() && auth()->user()->role == 'admin') {
            return $next($request);
        }

        return redirect()->route('error');

    }
}
