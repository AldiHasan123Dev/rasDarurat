<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockLoginOnLandingDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (
            in_array($host, ['ptras.id', 'www.ptras.id']) &&
            $request->is('login')
        ) {
            return redirect('/');
        }

        return $next($request);
    }
}