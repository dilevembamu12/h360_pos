<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class UserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $level
     * @return mixed
     */
    public function handle($request, Closure $next, $level)
    {
        if (Auth::user()->role()->type == 'admin') {
            return $next($request);
        }

        $permission = json_decode(preference('user_permission'));

        if ($permission && property_exists($permission, $level) && $permission->$level == 1) {
            return abort(404, 'You do not have permission to access this feature.');
        }
        return $next($request);
    }
}
