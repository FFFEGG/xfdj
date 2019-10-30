<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class UserOpenid
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

        $user = User::whereOpenid($request->openid)->first();
        if ($user) {
            $request['userinfo'] = $user;
            $response = $next($request);
            return $response;
        } else {
            return 999;
        }




    }
}
