<?php

namespace App\Http\Middleware;

use App\Helpers\UserHelper;
use Closure;
use Illuminate\Support\Facades\Auth;

class ReportPermission
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
        $route_uri = $request->route();
        $route = $route_uri->uri();

        if (Auth::check())
        {
            if ($request->ajax())
            {
                return response('Unauthorized.', 401);
            }
            else
            {
                $permission = UserHelper::get_route_permission($route);
                if (isset($permission->list) && ($permission->list == 1 || $permission->view == 1))
                {
                    return $next($request);
                }
                else
                {
                    Session()->flash('flash_message', 'You do not have permission to access!');
                    if (isset($permission->list) && $permission->list == 1)
                    {
                        return redirect($route);
                    }
                    else
                    {
                        return redirect('/home');
                    }
                }
            }
        }
        else
        {
            return redirect()->guest('login');
        }
    }
}
