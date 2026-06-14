<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerrouillageSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('verrouille', false)) {
            return redirect()->route('verrouillage.show');
        }

        return $next($request);
    }
}
