<?php

namespace App\Http\Controllers\Backend;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App,Session;
class AdminForgetController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    private $request;
    private $obj;
    private $act;
    private $id;
    private $title;

    private $path="App\Http\Controllers\Backend\\";
    private $ajax_paths;//=config('ccms.linktype');//"App\Plugins\\";

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
        //$this->middleware('guest', ['except' => 'logout']);

    }

    public function getAdminLogin(Request $request)
    {
        $this->langConfig($request);
        if (auth()->guard('admin')->user()) return redirect()->route('admin.controller1');
        return view('backend.adminLogin');
    }

    public function getAdminLogout(Request $request)
    {
        $lang = session('lang', 'en');
        auth()->guard('admin')->logout();
        $request->session()->flush();
        session(['lang' => $lang]);
        return redirect()->route('admin.login');
        
    }

    public function adminAuth(Request $request)
    {
        $this->validate($request, 
            [
            'username' => 'required',
            'password' => 'required',
            ]/*,
            [
                'username.required' => 'Sample message',
                'password.required' => 'Another Sample Message'
            ]*/
        );


        $remember = $request->input('remember');
        if (auth()->guard('admin')->attempt(['name' => $request->input('username'), 'password' => $request->input('password')],$remember))
        {
            return redirect()->route('admin.controller1');
        }else{
            $this->langConfig($request);
            return view('backend.adminLogin')
                ->with(
                        [
                        'lgerr'=>__('ccms.lgerr'),
                        'username'=>$request->input('username')
                        ]
                    );
        }
    }

    public function index(Request $request,$obj='home',$act='index',$id=0, $title='')
    {
        config(['ccms.js_env.token' => csrf_token()]);

        $this->langConfig($request);

        $users=auth()->guard('admin')->user();
    
        
        if('ajax_access'==strtolower($obj)){
            session(['ajax_access' => true]);

            $get_ajaxpath=$request->input('ajaxpath');
            $obj_path=$request->input('objpath');
            $obj= $request->input('ajaxobj');
            $act= $request->input('ajaxact');
            $id = $request->input('ajaxid');

            $ajax_path=empty($get_ajaxpath)?'ajax_plugin':$get_ajaxpath;

            if(empty($obj_path))
              $a_path=$this->ajax_paths[$ajax_path]."\\".ucfirst($obj)."Controller";
            else
              $a_path=$this->ajax_paths[$ajax_path]."\\".ucfirst($obj_path)."\\".ucfirst($obj)."Controller";

        }else{
            session(['ajax_access' => false]);
            if(session('stillajax')) session(['ajax_access' => true]);
            session(['stillajax' => false]);

            $d_path=$this->path."DashboardController";
            $a_path=$this->path.ucfirst($obj)."Controller";
            
        }

        $userinfo=['id'=>$users->id,'name'=>$users->name,'pwd'=>$users->password];
        $routeinfo=['obj' => $obj, 'act' => $act, 'id' => $id, 'title' => $title];
        $parent_blade=['index'=>'backend.index', 'layout'=>'backend.layout'];

        $args=[
                  $request,
                  'userinfo'=>$userinfo,
                  'routeinfo'=>$routeinfo,
                  $parent_blade
                ];
            if(class_exists($a_path)){

                $a_class=new $a_path($args);/*acees class*/

                if(method_exists($a_class, $act)){
                    $getact= $a_class->$act($request);

                    if(in_array($act, ['store','update','duplicate','delete','restore','destroy','remove','change']))
                    {
                      if(session('ajax_access'))
                      {
                          session(['ajax_access' => false]);

                          return \Redirect::to($getact['url'])
                            ->with($getact['passdata'])
                            ->with(['stillajax'=>true]);
                      }
                      else
                      {

                          return \Redirect::to($getact['url'])
                            ->with($getact['passdata']);
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



    public function langConfig($request){
        
        if ($request->exists('lang'))
        {
                
               $lang = $request->query('lang'); 
               if (! in_array($lang,config('ccms.bankendlang')))//['en','kh']
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