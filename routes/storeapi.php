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
  
      Route::get( '/index', 'Store\ApiAccessController@index');


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
