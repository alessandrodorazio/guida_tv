<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class IsAdminMiddleware
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
        if(User::where([['api_token', $request->sid],['admin', true]])->count() > 0) {
            return $next($request);
        } else {
            return response()->json(['error' => 'Non sei autorizzato ad eseguire questa operazione'], 403);
        }
    }
}
