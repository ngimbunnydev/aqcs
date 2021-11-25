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
function manage_apiroute(){
  
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
	          $ind++;
        }
        
};

 Route::group(['middleware' => ['auth:api']], function () {
    manage_apiroute();
});	

