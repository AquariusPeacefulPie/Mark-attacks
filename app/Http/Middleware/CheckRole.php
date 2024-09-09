<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
/**
 * @codeCoverageIgnore
 */
class CheckRole
{
    public function handle(Request $request, $role)
    {   dd($request);
        if (! $request->user() ) {
            abort(403, 'Accès non autorisé, vous n\'avez pas les droits' );
        }

        return ;
    }
}

