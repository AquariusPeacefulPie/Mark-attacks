<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class TeacherOrAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est un teacher
        if (auth()->check() &&  ( auth()->user()->role == 'teacher' || auth()->user()->role == 'admin' ) ){
            return $next($request);
        }

        return redirect()->route('error');

    }


}
