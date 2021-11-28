<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
#use Illuminate\Validation\Rule;


use App\Models\Backend\Userlevel;

class UserlevelController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='level_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'userlevel','title'=>'User Permission','routing'=>'admin.controller','icon'=>'<i class="fa fa-user-shield" aria-hidden="true"></i>'];

    private $protectme;

    private $definelevel;


	public function __construct(array $args){ //public function __construct(Array args){
    
    $this->obj_info['title'] = __('label.lb40');
        $this->protectme = [  
                        config('ccms.protectact.index'),
                        config('ccms.protectact.create'),
                        config('ccms.protectact.duplicate'),
                        config('ccms.protectact.store'),
                        config('ccms.protectact.edit'),
                        config('ccms.protectact.update'),
                        config('ccms.protectact.delete'),
                        config('ccms.protectact.restore'),
                        config('ccms.protectact.destroy'),

                        ];

        $this->args = $args;
		$this->model = new Userlevel;
        $this->dflang = config('ccms.multilang')[0];

        $obj_info=$this->obj_info;

       
//$levelsetting = $this->args['userinfo']['levelsetting'];
//dd(in_array("pos-abrp", $levelsetting));
        /**/
        $controllers = [];
        //dd(glob(app_path() . '/Http/Controllers/Backend/*Controller.php'));
        foreach (glob(app_path() . '/Http/Controllers/Backend/*Controller.php') as $controller) {
            $classname = basename($controller, '.php');
            //echo $classname.'<br>';
            $classPath = 'App\Http\Controllers\Backend\\' .$classname;

            if($classname!='AdminLoginController' && $classname!='AdminForgetController' && $classname!='UserlevelController')
            {
                $reflection = new \ReflectionClass($classPath);
             
                $props   = $reflection->getProperties();
                foreach ($props as $prop) {
                       
                        if($prop->getName()=='protectme')
                        {
                              //echo $classname.'<br>';
                            $getclass = new $classPath($args);
                            $class_info = $getclass->obj_info;
                            $classname = $class_info['name'];
                            $class_info ['protectme'] = $getclass->protectme;

                            $this->definelevel[$classname] = $class_info;
                            
                        }
                }
                
                
            }
          
        }
        //dd('aaa');
        /*0000*/

        $classname = $obj_info['name'];
        $obj_info['protectme'] =$this->protectme;
        $this->definelevel [$classname]= $obj_info;

    
        /**/

	} /*../function..*/




    public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function default()
    {
        

        return [];


    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, title, levelsetting, level_status"
                                                )
                                        );
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', $this->fprimarykey); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('txt')) 
        {
            $qry=$request->input('txt');
            //$results = $results->where('title', 'like', '%'.$qry.'%');
            $results = $results->whereRaw("(lower(JSON_UNQUOTE(title)) like '%".strtolower($qry)."%' or tnote like '%".strtolower($qry)."%')");
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }

       
        

        #no need to send default sort and order to Blade#
        // if($sort==$this->fprimarykey && $order=='desc')
        // {
        //     $sort = '';
        //     $order = '';
        // }
        

        // PAGINATION and PERPAGE
        $perpage=null;
        $perpage_query=[];
        if ($request->has('perpage')) 
        {
            $perpage = $request->input('perpage');
            $perpage_query = ['perpage='.$perpage];
            $appends = array_merge ($appends,['perpage'=>$perpage]);
        }
        else
        {
            $perpage = $this->rcdperpage<0 ? config('ccms.rpp') : $this->rcdperpage;
        }
        $results = $results->paginate($perpage);


        $appends = array_merge ($appends,
                        [
                        'sort'      => $request->input('sort'), 
                        'order'     => $request->input('order')
                        ]
                    );

        $pagination = $results->appends(
                $appends
            );

       // dd($pagination);
        $recordinfo = recordInfo($pagination->currentPage(), $pagination->perPage(), $pagination->total());

        return [
                        'results'           => $results,
                        'paginationlinks'    => $pagination->links(),
                        'recordinfo'    => $recordinfo,
                        'sort'          => $sort,
                        'order'         => $order,
                        'querystr'      => $querystr,
                        'perpage_query' => $perpage_query
                    ];
    } /*../function..*/

	public function index(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;

        $default=$this->default();

        #DEFIND MODEL#
        $results = $this->listingmodel();

        $sfp = $this->sfp($request, $results);

        $definelevel = $this->definelevel;

    	return view('backend.v'.$this->obj_info['name'].'.index',
                    compact('obj_info', 'definelevel'
                            )
                )
                ->with(['act' => 'index'])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with($setting);

    } /*../function..*/


    /*public function trash(Request $request)
    {
        $obj_info=$this->obj_info;

        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['act' => 'trash'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['trash'=>true])
                ->with(['caption' => __('ccms.bin')]);

    } /*../function..*/

    public function create(Request $request)
    {

        $obj_info=$this->obj_info;
        $definelevel = $this->definelevel;
        //dd($definelevel);
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact(
                            'obj_info',
                            'definelevel'

                            )


                )->with(
                    [
                        'submitto'  => 'store',
                        'fprimarykey'     => $this->fprimarykey,
                        'caption' => __('ccms.new')
                    ]
                );
    } /*../function..*/


    public function store(Request $request)
    {
        //https://scotch.io/tutorials/simple-laravel-crud-with-resource-controllers
        //return redirect()->back();

        $obj_info=$this->obj_info;
       
            if ($request->isMethod('post'))
            {
                $validator = $this->validation($request);
                if ($validator->fails()) {

                    $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);

                    return [
                        'act' => false,
                        'url' => $routing,
                        'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];

            } 
            else 
            {

                $data=$this->setinfo($request);
                $savedata = $this->model->insert($data['tableData']);
                

                if($savedata)
                {
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $id = $data['id'];

                    #when use ajax to SAVE
                    if ($request->session()->has('ajax_access')) {
                        $routing=url_builder($obj_info['routing'],[$obj_info['name'],$request->input('ajaxnext')]);
                        return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'input' => $request->input(),
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $id
                                                    ]
                                    ];
                    }
                    #end ajax SAVE



                    $arr_savetype=[
                        "save"=>"index", 
                        "new"=>"create", 
                        "apply"=> 'edit/'.$data['id']
                    ];

                    $action = empty($arr_savetype[$savetype])? 'index' : $arr_savetype[$savetype];

                    $routing=url_builder(
                        $obj_info['routing'],
                        [$obj_info['name'], $action]
                    );
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms)
                            #->with($this->fprimarykey , $data['id']);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        //'input' => $request->input(),
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];

                }/*../if savedata==true..*/
            }
        } /*../if POST..*/

        
    } /*../function..*/

    public function update(Request $request)
    {

        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            
            $validator = $this->validation($request, true);
            if ($validator->fails()) {
                //$errors = $validator->errors();
                //foreach ($errors->all() as $message) {
                    //echo $message;
                //}

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$request->input($this->fprimarykey)]);
                #return \Redirect::to($routing)
                #->with('errors', $validator->errors()->first())
                #->with('input' , $request->input());

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input()
                                ]
                ];

            } else {
                $data=$this->setinfo($request, true);

                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);


                
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_edit');

                    #when use ajax to SAVE
                    if ($request->session()->has('ajax_access')) {
                        $routing=url_builder($obj_info['routing'],[$obj_info['name'],$request->input('ajaxnext')]);
                        return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'input' => $request->input(),
                                                        $this->fprimarykey => $data['id'],
                                                        'title' => $data['tableData']['title'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                    }
                    #end ajax SAVE



                    switch ($savetype) {
                        case 'save':
                            # code...
                            if ($request->session()->has('backurl')) 
                            {
                                $routing = $request->session()->get('backurl');
                                $request->session()->forget('backurl');
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                if(stripos($routing, $obj_info['name'])===false)
                                {
                                    $routing=url_builder($obj_info['routing'],[$obj_info['name']]);
                                }

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            }
                            else
                            {
                                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            }
                            
                            break;

                        case 'new':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id']
                                                        
                                                    ]
                                    ];
                            break;

                        case 'apply':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$data['id']]);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id']
                                                        
                                                    ]
                                    ];
                            break;

                        default:
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id']
                                                        
                                                    ]
                                    ];
                            break;

                    }
                    /*Switch*/
                    /*//////*/
                


               
            }
        } /*../if POST..*/

    } /*../end fun..*/

    public function edit(Request $request, $id=0)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info=$this->obj_info;

        $default=$this->default();
        $definelevel = $this->definelevel;


        $input = null;
        if ($request->session()->has('input')) 
        {
           #No need to retrieve data becoz already set by Form#
            $editid=session('input')[$this->fprimarykey];
            goto skip;
        }
        
        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        if($request->has('level_id')){
            $editid = $request->input('level_id');
        }

        $input = $this->model->where($this->fprimarykey, (int)$editid)->where('trash','<>','yes')->get(); 
        if($input->isEmpty())
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }

        $input = $input->toArray()[0];
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key]= $value;
        }
        $x['levelsetting'] = json_decode($x['levelsetting'],true);
        $input=$x;
        
        skip:

        return view('backend.v'.$this->obj_info['name'].'.create',
                   
                    compact('obj_info',
                            'definelevel',
                            'input'
                            )


                )->with(
                    [
                        'submitto'      => 'update',
                        'fprimarykey'   => $this->fprimarykey,
                        'caption' => __('ccms.edit')
                    ]
                );
    } /*../end fun..*/

    public function validation($request, $isupdate=false){
        
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $update_rules= [ $this->fprimarykey => 'required'];
            $request->request->add(['branch_id' => $this->args['userinfo']['branch_id']??0]); 
            

            $rules = [
                        'title'      => 'required'
                        
                    ];


            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'unique' => __('ccms.fieldunique'),
                'distinct' => __('ccms.fielddistinct'),
                'gt' =>  __('ccms.fieldreqire'),//__('ccms.gt')
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
            $validator =Validator::make($request->input(), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){

        $newid=($isupdate)? $request->input($this->fprimarykey)  : $this->model->max($this->fprimarykey)+1;
        if($newid==1)$newid=2;

        $tableData=[];
        $levelsetting = !empty($request->input('levelsetting'))?$request->input('levelsetting'):[];
        $tableData = [
            
                $this->fprimarykey => $newid,
                'title'     => !empty($request->input('title'))?$request->input('title'):'',
                'levelsetting'    => json_encode($levelsetting),
                'level_status' =>  'yes',
                'level_type'  => '',
                'tag'       => '',
                'add_date'  => date("Y-m-d"),
                'trash'     => 'no',
                'blongto'   => $this->args['userinfo']['id']
            
            ];


        if($isupdate)
        {
            $tableData = array_except($tableData, [$this->fprimarykey,'add_date', 'blongto','trash']);
            //function removes the given key / value pairs from an array:
        }

        return ['tableData' => $tableData, 'id'=>$newid];
        

    }/*../function..*/



   public function delete(Request $request, $id=0)
    {
        $obj_info=$this->obj_info;

        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $destroy = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 

        //return redirect()->back();
        return [
                    'act' => $destroy,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_delete'),
                                    'id' => $editid
                                ]
                ];

    } /*../function..*/

    /*public function restore(Request $request, $id=0)
    {
        $obj_info=$this->obj_info;

        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $restore = $this->model->where($this->fprimarykey, (int)$editid)->update(['trash'=>'no']); 
        //return redirect()->back();

        return [
                    'act' => $restore,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' =>  __('ccms.suc_restore'),
                                    'id' => $editid
                                ]
                ];

    } /*../function..*/


    /*public function destroy(Request $request, $id=0)
    {
        $obj_info=$this->obj_info;

        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $destroy = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 

        //return redirect()->back();
        return [
                    'act' => $destroy,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_destroy'),
                                    'id' => $editid
                                ]
                ];

    } /*../function..*/


    public function duplicate(Request $request, $id=0)
    {
        return null;

    } /*../function..*/


    public function ajaxreturn(Request $request){

        // $default=$this->default();
        // $cat_tree = $default['cat_tree'];
        // $catlist= CategoryCheckboxTree($cat_tree,"","c_id[]",[]);

        $userlevel=$this->model->orderBy($this->fprimarykey, 'desc')->pluck('title', 'level_id');
        $userlevel = json_decode(json_encode($userlevel), true);

        $success='';
        $errors = '';
        if ($request->session()->has('success')) {
            
            $success = $request->session()->get('success');
        }

        if ($request->session()->has('errors')) {
            
            $errors = $request->session()->get('errors');
        }

        $return = [
                    'callback' => 'reloadSelect',
                    'container' => '#level_id',
                    'data' => $userlevel,
                    'close' => true,
                    'message' => '',
                    'success' => $success,
                    'errors' => $errors
                ];

        return json_encode($return);
    }/*../function..*/


    public function afteredit(Request $request){

        $newcustomer = [];
        if ($request->session()->has('title')) {
            $title = $request->session()->get('title');
            $info = [
                'lavelname-'.$request->session()->get('id') => $title 
            ];
            $success = $request->session()->get('success');
        }

        
        
        $return = [
                    'callback' => 'gettingClassTitle',
                    'container' => '',
                    'data' => $info,
                    'message' => $success
                ];

        return json_encode($return);
    }/*../function..*/




   

    
}