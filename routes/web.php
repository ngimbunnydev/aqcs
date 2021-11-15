<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
      echo "hello";
});







$company = $api = 'aqcs';
if($company=='api' || $company=='restapi' || $company=='storeapi')
{
  $company = \Request::segment(2) ?? 'aqcs';

}

config(['database.connections.mysql.database' => $company]);
config(['ccms.backend' => $company]);

config(['ccms.js_env.ajaxpublic_url' => url('/').'/admin/ajax_access']);
config(['ccms.js_env.ajaxadmin_url' => url('/').'/admin/ajax_access']);


//dd(config('currencyinfo'));
config(['sysconfig' => config($company.'_sysconfig')]);
if(strlen(config('sysconfig.pdfwatermask'))>0)
{
	config(['pdf.show_watermark' => true]);
	config(['pdf.watermark' => config('sysconfig.pdfwatermask')]);
}

try {
        DB::connection()->getPdo();
        // if(DB::connection()->getDatabaseName()){
        //     echo "Yes! Successfully connected to the DB: " . DB::connection()->getDatabaseName();

        // }else{
        //     die("Could not find the database. Please check your configuration.");
        // }
} catch (\Exception $e) {
    if($api=='api' || $api=='restapi')
    {
      return response()->json([
            'success' => false,
            'message' => 'Invalid Database Server',
        ], 401);
    }
    else{
        //die("Could not open connection to database server.  Please check your configuration.");
        return view('welcome');
    }
        
}

			/******************************************************* Shall creat other route for AJAX 8888888888888888888888*/
			Route::group(['prefix' => 'ajax'], function () {

			  manage_backendroute();
			  /*************************************************************************************************/
			});

			Route::group(['prefix' => 'admin'], function () {

				Route::get('login', ['as'=>'admin.login','uses'=>'Backend\AdminLoginController@getAdminLogin']);
			  	Route::get('logout', ['as'=>'admin.logout','uses'=>'Backend\AdminLoginController@getAdminLogout']);
			  	Route::post('login', ['as'=>'admin.auth','uses'=>'Backend\AdminLoginController@adminAuth']);
			    //Route::any('', ['as'=>'admin.loginprotex','uses'=>'Backend\TestController']);

			    Route::get('forget', ['as'=>'admin.forget','uses'=>'Backend\AdminLoginController@getAdminForget']);
			    Route::post('forget', ['as'=>'forget.auth','uses'=>'Backend\AdminLoginController@forgetAuth']);

			    Route::get('newpwd', ['as'=>'admin.newpwd','uses'=>'Backend\AdminLoginController@getAdminNewpwd']);
			    Route::post('newpwd', ['as'=>'newpwd.auth','uses'=>'Backend\AdminLoginController@newpwdAuth']);
			    
				Route::group(['middleware' => ['admin']], function () {
					manage_backendroute();
				});	
			}); 

function manage_backendroute(){
  $url_map=array('obj'=>'[a-zA-z_]+','act' => '[a-z0-9]+','id' => '[0-9-]+','title'=>'');
        $ind=1;
        $url='';
        $exp=array();
        $c=count($url_map);

        foreach ($url_map as $key => $value) {
          
          $url.='{'.$key.'?}'.'/';
          if(!empty($value))$exp = array_add($exp, $key, $value);

	          Route::any(substr($url,0,-1),['as'=>'admin.controller'.$ind,'uses'=>'Backend\AdminLoginController@index'])
	          ->where($exp);

	   //        Route::any(substr($url,0,-1), function( $page ){
				//     dd($page);
				// });

	          $ind++;
        }
        
};
