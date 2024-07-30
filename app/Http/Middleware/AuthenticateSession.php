<?php

// App\Http\Middleware\AuthenticateSession
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthenticateSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            DB::table('sessions')
                ->where('id', session()->getId())
                ->update(['user_agent' => $request->userAgent()]);
        }

        return $next($request);
    }
}
