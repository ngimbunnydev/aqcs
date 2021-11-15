<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
function manage_restapiroute(){
  
  $url_map=array('restapi'=>'','obj'=>'[a-zA-z_]+','act' => '[a-z0-9]+','id' => '[0-9-]+','title'=>'');
        $ind=1;
        $url='';
        $exp=array();
        $c=count($url_map);

        foreach ($url_map as $key => $value) {
     
          $url.='{'.$key.'?}'.'/';
          
          if(!empty($value))$exp = array_add($exp, $key, $value);
      
	          Route::any(substr($url,0,-1),['as'=>'admin.controller'.$ind,'uses'=>'Backend\ApiAccessController@index'])
	          ->where($exp);

	   //        Route::any(substr($url,0,-1), function( $page ){
				//     dd($page);
				// });

	          $ind++;
        }
        
};


 $company = \Request::segment(2) ?? 'beta';
      //dd($company);
       Route::get('/gettranslate', function () {
              
              $lang = \Request::get("lang")??'en';
              \App::setLocale($lang);
              return response()->json([
                    'ccms'  => __('ccms'),
                    'label' => __('label'),
                ]);
      });


      Route::get('/getcontact', function () {
              
              return response()->json([
                    ['icon'=>'fa.phone', 'title'=>'Phone', 'info'=>'012986101'],
                    ['icon'=>'fa.phone', 'title'=>'Phone', 'info'=>'081986101'],
                    ['icon'=>'fa.envelope', 'title'=>'Email', 'info'=>'sales@servingweb.com'],
                    ['icon'=>'fa.globe', 'title'=>'Website', 'info'=>'www.servingweb.com'],
                ]);
      });

      Route::group(['prefix' => $company], function () use($company){
        
          Route::post('/login', 'Backend\ApiAccessController@apiAuth');
          //Route::get('/logout', 'Backend\ApiAccessController@getApiLogout')->middleware('auth:api');
          Route::get('/dbconfig', function () use($company) {
              
              try {
                \DB::connection()->getPdo();  
              } catch (\Exception $e) {
                   $lang = \Request::get("lang")??'en';
                  \App::setLocale($lang);
                  
                  return response()->json([
                          'success' => false,
                          'message' => __('ccms.invaliddb'),
                      ], 401);

              }
              
              //dd(__('label'));
              
              return response()->json([
                    'success' => true,
                    'sysconfig' => config('sysconfig'),
                    'currencyinfo'  => config('currencyinfo'),
                    'multilang' => config('ccms.'.$company.'_multilang') ?? config('ccms.multilang'),
                    'general' => \App\Models\Backend\General::where('g_id', 1)->first(),
                    'modules' => \App\Models\Backend\Module::with(['articles', 'childModules'])->where('moduletype', 'object')->get()->keyBy('modulename'),
                    'menus' => \App\Models\Backend\Menus::with('children')->where('parent_id', 0)->get()->keyBy(function($menu){
                      return json_decode($menu['title'], true)[get_current_request_lan()];   
                    }),
                ]);
         });	
        
        

          Route::group(['middleware' => ['auth:restapi']], function () {
            
            manage_restapiroute();
            

          });	


      });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
