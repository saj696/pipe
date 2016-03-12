<?php

namespace App\Http\Middleware;

use App\Helpers\CommonHelper;
use App\Helpers\UserHelper;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckClosing
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
        $user = Auth::user();
        $route_uri = $request->route()->getName();
        $route = strstr($route_uri, '.', true);

        $currentYear = CommonHelper::get_current_financial_year();
        $closingStatus = DB::table('account_closings')->where(['year'=> $currentYear, 'workspace_id'=>$user->workspace_id])->value('status');
        $existingYearDetail = DB::table('financial_years')->where('year', $currentYear)->first();

        $permission = UserHelper::get_route_permission($route);

        if ($closingStatus==1 && ($existingYearDetail->end_date < strtotime(date('Y-m-d'))))
        {
            return $next($request);
        }
        else
        {
            Session()->flash('warning_message', 'Year closed already! If you want to do any Transaction, Please request for the Rollback!');
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
