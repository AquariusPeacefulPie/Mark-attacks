<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est un teacher
        if (auth()->check() && auth()->user()->role == 'teacher') {
            return $next($request);
        }

        return redirect()->route('error');

    }


}
