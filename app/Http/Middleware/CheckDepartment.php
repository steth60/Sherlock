<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDepartment
{
    public function handle(Request $request, Closure $next, $department)
    {
        if (!Auth::check() || !Auth::user()->hasDepartment($department)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
