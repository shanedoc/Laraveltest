<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified
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
        if($request->user()
            && !$request->user()->hasVerifiedEmail()
            && !$request->is('email/*','logout')){
            return $request->expectsJson()
                ?abort(403,'你的邮箱地址还没认证')
                :redirect()->route('verification.notice');

        }
        return $next($request);
    }
}
