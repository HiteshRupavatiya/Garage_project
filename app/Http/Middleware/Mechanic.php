<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Mechanic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->type == 'Mechanic') {
            return $next($request);
        } else {
            return error('You have not access rights', type: 'unauthenticated');
        }
    }
}
