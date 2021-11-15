<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class Protectme
{
    public function handle(Request $request, Closure $next)
	{

	    if (1==1) {
	    	
	        return redirect()->route('admin.logout');
	    }

	    return $next($request);

    }
}
?>