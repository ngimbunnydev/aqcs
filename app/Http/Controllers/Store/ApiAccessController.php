<?php 
namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App,Session;
use App\Models\Backend\Customer;
use Validator;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\QuotationController;


class ApiAccessController extends Controller
{
    protected $redirectTo = '/';

    private $request;
    private $obj;
    private $act;
    private $id;
    private $title;

    private $path="App\Http\Controllers\Backend\\";
    private $path_restapi="App\Http\Controllers\Restapi\\";
    private $ajax_paths;//=config('ccms.linktype');//"App\Plugins\\";

    private $branch;
    private $warehouse;
    private $alertstock;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Request $request,$obj='dashboard',$act='index',$id=0, $title='')
    {
        $this->langConfig($request);
        $this->request= $request;
        $this->obj= $obj;
        $this->act= $act;
        $this->id= $id;
        $this->title= $title;
        $this->ajax_paths=config('ccms.ajax_paths');
        /*888*****///$this->pmntor();/********/
        //$this->middleware('guest', ['except' => 'logout']);

        $conf_lang_by_db = 'ccms.'.config('ccms.backend').'_multilang';
        
        if(null!==config($conf_lang_by_db)){
          config(['ccms.multilang' => config($conf_lang_by_db)]);
        }
      
        if(!empty(config('currencyinfo.symbol')))
           config(['ccms.discounttype.-1' => config('currencyinfo.symbol')]);
        

    }

    
  
    public function apiAuth(Request $request, $condition=[], $setting=[])
    {
        $model = new Customer;
       $getcustomer = false;
       if ($request->has('username') && $request->has('password')) {
            $username = $request->input('username');
            $password = $request->input('password');
            $haspassword = \Hash::make($password);

            ////
              $user = $model
            ->selectRaw('*')
            ->whereRaw("latinname='$username' OR cemail='$username' OR cphone='$username'")->get()??false;

              if($user) {
                  foreach($user as $row){

                    if(\Hash::check($password, $row->password)) {
                        $row->password = '';
                       $getcustomer = $row;
                        break;
                    }
                  }

              }


        }
        if($getcustomer){
          return response()->json([
              'success' => true,
              'customerinfo'  => $getcustomer
          ]);
        }
        return response()->json([
            'success' => false,
            'message' => __('ccms.lgerr'),
        ], 401);
    }
  
  public function checkphone(Request $request){
   
    $model = new Customer;
    $phone = !empty($request->input('cphone'))? $request->input('cphone'): '';
    $phone = str_replace(' ', '', $phone);
    $getcustomer = $model->select('*')->where('cphone', $phone);
    if($getcustomer->count()==1){
      return response()->json([
        'status' => true,
        'getcustomer' => $getcustomer->count()
      ]);
    }
    else{
      return response()->json([
        'status' => false,
        'message' => __('ccms.wrongphone'),
      ]);
    }
 
    
    return response()->json([
        'status' => false,
        'message' => 'Something wrong...!'
      ]);
  }
  
  public function resetpwd(Request $request)
    {
        $model = new Customer;
       $updategeneral = false;
       if ($request->has('username') && $request->has('token')) {
            $username = $request->input('username');
            $token = $request->input('token');
            if($token=='!555524!') {
              $username = str_replace("+855","",$username);
              $cpassword = !empty($request->input('code'))? \Hash::make($request->input('code')): '';
              $updategeneral = $model->where('cphone', 'like', '%'.$username.'%')->update(['password'=>$cpassword]); 
            }           
        }
    
        if($updategeneral){
          return response()->json([
              'success' => true,
          ]);
        }
        return response()->json([
            'success' => false,
            'message' => __('ccms.rqnvalid'),
        ]);
    }
  
  
   public function register(Request $request)
    {
      $rule = [
      'cpassword' => 'required',
      'cphone' => 'required|distinct|unique:pos_customer',
      'latinname' => 'required',
      
    ];
    
    if(!empty($request->input('cemail'))){
      $rule['cemail'] = 'distinct|unique:pos_customer|email';
    }
     $custom_attributes = [
      'latinname' => __('store.phonenumber'),
      'cphone' => __('store.realname'),
     'cemail' => __('store.email'),
    ];
     
      $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'unique' => __('ccms.fieldunique'),
                'distinct' => __('ccms.fielddistinct'),
                'email' => __('ccms.fgerr'),
                
            ];
     
     $validator = Validator::make($request->all(), $rule, $validatorMessages, $custom_attributes);
      if($validator->fails()){
      return response()->json([
        'success' => false,
        'message' => $validator->errors()->first()
      ]);
    }
     
     //////
      $userinfo=[
                'id'=>1, 
                'level_id'=>1,'name'=>'','pwd'=>'', 'branch_id'=>1, 'wh_id'=>1, 'levelsetting' =>''];
             $args=[  
                $request,
                'userinfo'=>$userinfo,
                'routeinfo'=>[],
                 ''
            ];
     $customerObj = new CustomerController($args);
     $data = $customerObj->setinfo($request);
    $saved=$customerObj->model->insert($data['tableData']);
      if($saved){
        return response()->json([
          'success' => true,
          'customerinfo'  => $data
        ]);
      }

      return response()->json([
            'success' => false,
        ]);
   }
  
    public function makeorder(Request $request){
      
      $userinfo=[
                'id'=>1, 
                'level_id'=>1,'name'=>'','pwd'=>'', 'branch_id'=>1, 'wh_id'=>1, 'levelsetting' =>''];
             $args=[  
                $request,
                'userinfo'=>$userinfo,
                'routeinfo'=>[],
                 ''
            ];
      
      //update Address
      if($request->has('cm_id')){
        $cm_id = $request->input('cm_id');
        $caddress = $request->input('caddress')??'';
        $model = new Customer;
        $updategeneral = $model->where('cm_id', $cm_id)->update(['caddress'=>$caddress]); 
        
      }
      $quotation = new QuotationController($args);
      $apisave = $quotation->apisave($request);
      return $apisave;
    }
  
    public function myorder(Request $request, $condition=[], $setting=[]){
      if(!$request->has('cm_id') && empty($request->input('cm_id'))){
        return response()->json([
          'status' => false,
          'message' => __('ccms.rqnvalid'),
        ]);  
      }
      
      $userinfo=[
                'id'=>1, 
                'level_id'=>1,'name'=>'','pwd'=>'', 'branch_id'=>1, 'wh_id'=>1, 'levelsetting' =>''];
             $args=[  
                $request,
                'userinfo'=>$userinfo,
                'routeinfo'=>[],
                 ''
            ];
      $quotation = new QuotationController($args);
      $results = $quotation->listingModel();
      $results = $results->where('pos_quotation.cm_id', $request->input('cm_id'))->orderBy('pos_quotation.qt_id', 'DESC')->get();
    
    return response()->json([
      'status' => true,
      'data' => $results
    ]);
    
    }
  


    public function langConfig($request){
        
        if ($request->exists('lang'))
        {
                
               $lang = $request->query('lang'); 
                
                $backendlang = array_keys(config('ccms.bankendlang'));
               if (! in_array($lang,$backendlang))//['en','kh']
                {
                    $lang = 'en';
                }
                //$request->session()->put('lang', $lang);
                //$lang = $request->session()->get('lang', 'en');
                session(['lang' => $lang]);
        }
        elseif(null==session('lang'))
        {
            session(['lang' => 'en']);
        }
        $lang = session('lang');
        App::setLocale($lang);
    }

   


}