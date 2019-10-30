<?php

namespace App\Http\Middleware;

use Closure;

class Ps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->get('ps_person')) {
            return redirect('/log_login');
        }

        return $next($request);
    }
}
