<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est un étudiant
        if (auth()->check() && auth()->user()->role == 'student') {
            return $next($request);
        }

        return redirect()->route('error');

    }

}
