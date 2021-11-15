<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Backend\Customer;
use Validator;

class LoginController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='cm_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'login','title'=>'Login','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;

	public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb10');

        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
        $this->model = new Customer;
	} /*../function..*/
  
  public function default()
  {
    return [];
  } /*../function.s.*/
   
	public function login(Request $request, $condition=[], $setting=[]){
    $obj_info=$this->obj_info;
    $default=$this->default();
    
    $getcustomer = false;
    if ($request->has('username') && $request->has('password')) {
        $username = $request->input('username');
        $password = $request->input('password');
        $haspassword = \Hash::make($password);
       
        ////
          $user = $this->model
        ->selectRaw('*')
        ->whereRaw("latinname='$username' OR cemail='$username' OR cphone='$username'")->get()??false;
        
          if($user) {
              foreach($user as $row){
                
                if(\Hash::check($password, $row->password)) {
                 
                   $getcustomer = $row;
                    break;
                }
              }
              
          }
      
        
    }
    return response()->json([
              'customerinfo'  => $getcustomer
          ]);
	}
  
  public function ismember(Request $request){
   if($request->isMethod('post')){
      $validator = Validator::make($request->all(), [
        'phone_number' => 'required'
      ]);

      if($validator->fails()){
        return response()->json([
          'status' => 'error',
          'message' => $validator->errors()->first()
        ]);
      }

      $customer = $this->model->where('cphone', $request->input('phone_number') )->first();
     
      if($customer){
        return response()->json([
          'status' => 'error',
          'message' => 'This phone number has been registered already.'
        ]);
      }
      
     return response()->json([
        'status' => 'success',
        'message' => 'This phone number does not register yet.'
      ]);
     
   }
  }
  
  public function listingModel()
  {
      #DEFIND MODEL#
      return $this->model;
  } /*../function..*/

  
  
}  