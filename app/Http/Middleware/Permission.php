<?php

namespace App\Http\Middleware;

use App\Helpers\UserHelper;
use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $route_uri = $request->route()->getName();
        $route = strstr($route_uri, '.', true);

        if (Auth::check()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                $permission = UserHelper::get_route_permission($route);
                if ($route_uri == $route . '.index' && isset($permission->list) && $permission->list == 1) {
                    return $next($request);
                } elseif ($route_uri == $route . '.show' && isset($permission->view) && $permission->view == 1) {
                    return $next($request);
                } elseif ($route_uri == $route . '.create' && isset($permission->add) && $permission->add == 1) {
                    return $next($request);
                } elseif ($route_uri == $route . '.store' && isset($permission->add) && $permission->add == 1) {
                    return $next($request);
                } elseif ($route_uri == $route . '.edit' && isset($permission->edit) && $permission->edit == 1) {
                    return $next($request);
                } elseif ($route_uri == $route . '.update' && isset($permission->edit) && $permission->edit == 1) {
                    return $next($request);
                } else {
                    Session()->flash('warning_message', 'You do not have permission to access!');
                    if (isset($permission->list) && $permission->list == 1) {
                        return redirect($route);
                    } else {
                        return redirect('/home');
                    }
                }
            }
        } else {
            return redirect()->guest('login');
        }
    }
}
