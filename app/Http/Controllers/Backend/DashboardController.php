<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Backend\LivedataController;

class DashboardController extends Controller
{
	private $obj_info=['name'=>'home','title'=>'Dashboard','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
  private $livedata;
  public function __construct(array $args){ //public function __construct(Array args){
        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
        $this->livedata = new LivedataController($args);
	} /*../function..*/
  
	public function index(){
    $request = new Request;
    return $this->livedata->index($request);
	}

  
  
  
  
}  