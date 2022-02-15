<?php

namespace Infinity\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InfinityAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): mixed
    {
        auth()->setDefaultDriver(app('InfinityGuard'));

        if (!Auth::guest()) {
            /** @var \Infinity\Models\Users\User $user */
            $user = Auth::user();
            app()->setLocale($user->locale ?? app()->getLocale());

            return $user->hasPermission('app.access') ? $next($request) : redirect('/');
        }

        $urlLogin = route('infinity.login');

        return redirect()->guest($urlLogin);
    }
}
