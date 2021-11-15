<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
/**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @param  string|null  $guard
 * @return mixed
 */
public function handle($request, Closure $next, $guard = 'admin')
{

	////
	//echo "==".session('companyusing');
	if(session('companyusing')) 
            {
               	$company = session('companyusing');
                if($company!=$request->segment(1)){

                    //return redirect($request->segment(1).'/logout'); //admin/login
                    return redirect()->route('admin.logout');
                }
            }
            else
            {
                $company = $request->segment(1) ?? 'nodb';
                
                session(['companyusing' => $company]);
            }
    /////

    if (!Auth::guard($guard)->check()) {
    	$company=session('companyusing');
        //return redirect($company.'/login'); //admin/login
        return redirect()->route('admin.login');
    }

    return $next($request);
    }
}  