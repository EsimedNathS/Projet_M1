<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->admin) {
            return $next($request);
        }

        abort(403, 'Accès non autorisé');
    }
}
