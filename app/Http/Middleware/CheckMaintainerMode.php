<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintainerMode
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::where('key', 'maintainer_mode')->first();

        if ($setting && $setting->value == '1' && (!Auth::check() || !Auth::user()->isAdmin())) {
            return redirect()->route('maintainer-mode');
        }

        return $next($request);
    }
}
