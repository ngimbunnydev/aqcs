<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Backend\Customer;
use Validator;
use App\Http\Controllers\Backend\CustomerController;

class ForgotController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='cm_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'forgot','title'=>'Forgot','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
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
   
	public function resetpassword(Request $request, $condition=[], $setting=[]){
    $obj_info=$this->obj_info;
    $default=$this->default();
    $rule = [
      'cphone' => 'required',
      'cpassword' => 'required'
    ];
    
   $custom_attributes = [
      'cphone' => 'phone number',
      'cpassword' => 'password',
    ];
    
    $validator = Validator::make($request->all(), $rule, [], $custom_attributes);
    
    if($validator->fails()){
      return response()->json([
        'status' => 'error',
        'message' => $validator->errors()->first()
      ]);
    }
    
    $cphone = $request->input('cphone') ?? '';
    $cphone = str_replace("+855","",$cphone);
    $cpassword = !empty($request->input('cpassword'))? \Hash::make($request->input('cpassword')): '';
    $updategeneral = $this->model->where('cphone', 'like', '%'.$cphone.'%')->update(['password'=>$cpassword]); 
    
    
    if($updategeneral){
      return response()->json([
        'status' => 'success',
        'msg' => 'successfully reset password!',
        'cphone' => $cphone,
      ]);
    }
 
    
    return response()->json([
        'status' => 'error',
        'message' => 'Something wrong...!',
        'updategeneral' => $cphone,
      ]);
	}
  
  public function checkphone(Request $request){
    $phone = !empty($request->input('cphone'))? $request->input('cphone'): '';
    $phone = str_replace(' ', '', $phone);
    $getcustomer = $this->model->select('*')->where('cphone', $phone);
    if($getcustomer->count()==1){
      return response()->json([
        'status' => 'success',
        'getcustomer' => $getcustomer->count()
      ]);
    }
    else{
      return response()->json([
        'status' => 'error',
        'message' => __('ccms.wrongphone'),
      ]);
    }
 
    
    return response()->json([
        'status' => 'error',
        'message' => 'Something wrong...!'
      ]);
  }
  
  public function listingModel()
  {
      #DEFIND MODEL#
      return $this->model;
  } /*../function..*/

  
  
}  