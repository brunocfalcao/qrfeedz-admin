<?php

namespace QRFeedz\Admin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanUseAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('login*')) {
            return $next($request);
        }

        $guard = config('nova.guard') ?? null;

        if (Auth::guard($guard)->check() &&
            Auth::guard($guard)->user()->isAllowedAdminAccess()) {
            return $next($request);
        }

        return abort(403, "You don't have permissions to access QRFeedz admin panel");
    }
}
