<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Backend\Customer;
use Validator;
use App\Http\Controllers\Backend\CustomerController;

class RegisterController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='cm_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'register','title'=>'Register','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;
  private $customerObj;

	public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb10');

        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
        $this->model = new Customer;
    $this->customerObj = new CustomerController($args);
	} /*../function..*/
  
  public function default()
  {
    return [];
  } /*../function.s.*/
   
	public function register(Request $request, $condition=[], $setting=[]){
    $obj_info=$this->obj_info;
    $default=$this->default();
    $rule = [
      'cphone' => 'required|distinct|unique:pos_customer',
      'latinname' => 'required|distinct|unique:pos_customer',
      
    ];
    
    if(!empty($request->input('cemail'))){
      $rule['cemail'] = 'distinct|unique:pos_customer';
    }
    
   $custom_attributes = [
      'latinname' => 'real name',
      'cphone' => 'phone number',
     'cemail' => 'email',
    ];
    
    $validator = Validator::make($request->all(), $rule, [], $custom_attributes);
    
    if($validator->fails()){
      return response()->json([
        'status' => 'error',
        'message' => $validator->errors()->first()
      ]);
    }
    
    $data = $this->customerObj->setinfo($request);
    $saved=$this->model->insert($data['tableData']);
    if($saved){
      return response()->json([
        'status' => 'success',
        'message' => __('ccms.registered'),
      ]);
    }
 
    
    return response()->json([
        'status' => 'error',
        'message' => 'Something wrong...!'
      ]);
	}
  
  public function resetpassword(Request $request, $condition=[], $setting=[]){
     return response()->json([
        'status' => 'success',
      ]);
  }
  
  
  public function listingModel()
  {
      #DEFIND MODEL#
      return $this->model;
  } /*../function..*/

  
  
}  