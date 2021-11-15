<?php 
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: DELETE, POST, GET, OPTIONS');
// header('Accept: application/json');
// header('Content-type: application/json');
// header('Access-Control-Request-Headers: X-PINGOTHER, Content-Type');
use Illuminate\Http\Request;
use App\Models\Backend\Paymentmethod;
use App\Models\Backend\General;
use App\Http\Controllers\Backend\PosController;

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
function manage_storeapiroute(){
  
  $url_map=array('api'=>'','obj'=>'[a-zA-z_]+','act' => '[a-z0-9]+','id' => '[0-9-]+','title'=>'');
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

      Route::get('/internet', function () {
              return response()->json([
                    'status'  => true
                ]);
      });

       Route::get('/dbinuse', function () {
          $host = \Request::input("host")??'localhost';
     
         $db = config('db_config.localhost');
          if($host){
           $db = config('db_config.'.$host);
          }
         
              return response()->json([
                    'status'  => true,
                    'db' => $db,
                ]);
      });

       Route::get('/gettranslate', function () {
              
              $lang = \Request::get("lang")??'en';
              \App::setLocale($lang);
              return response()->json([
                    'store'  => __('store'),
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
          Route::post('/register', 'Store\ApiAccessController@register');
          Route::post('/login', 'Store\ApiAccessController@apiAuth');
          Route::get('/checkphone', 'Store\ApiAccessController@checkphone');
          Route::post('/resetpwd', 'Store\ApiAccessController@resetpwd');
          
          Route::get('/dbconfig', function () {
              
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
            
               $lang = \Request::input("lang")??'en';
               \App::setLocale($lang);
            $conf_lang_by_db = 'ccms.'.config('ccms.backend').'_multilang';
            
          $store_lang = [];
          if(null!==config($conf_lang_by_db)){
    
            config(['ccms.multilang' => config($conf_lang_by_db)]);
            $store_lang = config($conf_lang_by_db);
          }
           
            /*swith language*/
          $found = [];
          $unfound=[];
           
          foreach(config('ccms.multilang') as $lang_record){
            
            $key = $lang;
            if($key==$lang_record[0]){
             $found = $lang_record;
            }
            else{
              $unfound[]=$lang_record;
            }
          }
          array_unshift($unfound, $found);
          if(!empty($found)){
            config(['ccms.multilang' => $unfound]);
          }
            
          
            
           
             
        /*swith end language*/
            
            
              $trans = [
                    'tran' =>[
                          'ccms'  => __('ccms'),
                          'label' => __('label'),
                          'config'  => config('ccms'),
                         
                      ]
                ];
              
              //dd(__('label'));
              $paymentmethod = Paymentmethod::getlist('en');
            
              $dbconfig = [
                    'success' => true,
                    'sysconfig' => config('sysconfig'),
                    'currencyinfo'  => config('currencyinfo'),
                    'paymentmethod' => $paymentmethod,
                    'pageinfo' => General::find(1)->toArray(),
                    'multilang' => $store_lang,
                ];
            
              $withtran = \Request::get("withtran")??'';
              if($withtran=='yes'){
                $dbconfig = array_merge($dbconfig, $trans);
              }
              
              return response()->json($dbconfig);
         });	
        

         Route::get('/restdata', function (Request $request) {
           
            $lang = \Request::get("lang")??'en';
                  \App::setLocale($lang);
           
           
           $conf_lang_by_db = 'ccms.'.config('ccms.backend').'_multilang';
        
          if(null!==config($conf_lang_by_db)){
            config(['ccms.multilang' => config($conf_lang_by_db)]);
          }
           
            /*swith language*/
          $found = [];
          $unfound=[];
           
          foreach(config('ccms.multilang') as $lang_record){
            
            $key = $lang;
            if($key==$lang_record[0]){
             $found = $lang_record;
            }
            else{
              $unfound[]=$lang_record;
            }
          }
          array_unshift($unfound, $found);
          if(!empty($found)){
            config(['ccms.multilang' => $unfound]);
          }
             
        /*swith end language*/
           
              $userinfo=[
                'id'=>2, 
                'level_id'=>1,'name'=>'','pwd'=>'', 'branch_id'=>1, 'wh_id'=>1, 'levelsetting' =>''];
             $args=[  
                $request,
                'userinfo'=>$userinfo,
                'routeinfo'=>[],
                 ''
            ];
           
           $pos = new PosController($args);
           return $pos->indexapi($request);
        });
        
        Route::post('/makeorder', 'Store\ApiAccessController@makeorder');
        Route::get('/myorder', 'Store\ApiAccessController@myorder');
         
          Route::group(['middleware' => ['auth:api']], function () {
            
            Route::get('/logout', 'Backend\ApiAccessController@getApiLogout');	
//             Route::get('/user', function () {
//               $dbname = \DB::connection()->getDatabaseName();
//               return $dbname;
//             });
            
           
             
            
            
            manage_storeapiroute();
            

          });	


      });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
