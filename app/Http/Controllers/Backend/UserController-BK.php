<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Image;
use Validator;

use App\Models\Backend\Users;
use App\Http\Controllers\Backend\ModulesController;



class UserControllerBK extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='id';
    private $tbltranslate='';
    private $tblfile='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'user','title'=>'User','routing'=>'admin.controller','icon'=>'<i class="fa fa-user" aria-hidden="true"></i>'];

    private $attribute;


	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
		$this->model = new Users;
        $this->dflang = config('ccms.multilang')[0];

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
        $js_filemanagersetting=[];
        $js_config = [];
        return ['js_config'=>$js_config];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, name"
                                                )
                                        );
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('latinname'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            $results = $results->where('latinname', 'like', '%'.$qry.'%');
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }

        if ($request->input('ct_id')) 
        {
            $qry=$request->input('ct_id');
            $results = $results->whereRaw("FIND_IN_SET('$qry',ct_id)");
            array_push($querystr, 'ct_id='.$qry);
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

        $ctype = $default['ctype'];

        #DEFIND MODEL#
        $results = $this->listingmodel();
        if(empty($condition))
        {
            $results = $results->where('trash', '!=', 'yes');
        }
        else
        {
            //
        }

        $sfp = $this->sfp($request, $results);

    	return view('backend.v'.$this->obj_info['name'].'.index', compact('ctype'))
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with($setting);


    } /*../function..*/


    public function trash(Request $request)
    {
        $obj_info=$this->obj_info;

        $default=$this->default();
        $ctype = $default['ctype'];

        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index', compact('ctype'))
                ->with(['act' => 'trash'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['trash'=>true])
                ->with(['caption' => __('ccms.bin')]);

    } /*../function..*/

    public function create(Request $request)
    {
        $obj_info=$this->obj_info;

        $default=$this->default();
        $js_config = $default['js_config'];
        
        $ctype = $default['ctype'];
        
        $suggestcode = $this->model->max($this->fprimarykey)+1;

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'ctype',
                            'suggestcode'
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

        //dd($request->all());

        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            $validator = $this->validation($request);

            if ($validator->fails()) {
                //$errors = $validator->errors();
                //foreach ($errors->all() as $message) {
                    //echo $message;
                //}

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                #return \Redirect::to($routing)
                #->with('errors', $validator->errors()->first())
                #->with('input' , $request->input())
                #->with('submitto', 'create');

               

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];

            } else {

                $data=$this->setinfo($request);

                $savedata = $this->model->insert($data['tableData']);
                

                if($savedata)
                {

                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');

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
                                                        'latinname' => $data['tableData']['latinname'],
                                                        'id' => $data['id']
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


                    /*switch ($savetype) {
                        case 'save':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms)
                            #->with($this->fprimarykey , $data['id']);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'input' => $request->input(),
                                                        $this->fprimarykey => $data['id']
                                                    ]
                                    ];
                            break;

                        case 'new':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms
                                                    ]
                                    ];
                            break;

                        case 'apply':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$data['id']]);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms
                                                    ]
                                    ];
                            break;

                        default:
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms
                                                    ]
                                    ];
                            break;

                    }*/
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
                
                

                ############
                $savetype=strtolower($request->input('savetype'));
                $success_ms = __('ccms.suc_edit');
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
        $js_config = $default['js_config'];
        $ctype = $default['ctype'];

        $input = null;
        
        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $input = $this->model->where($this->fprimarykey, (int)$editid)->get(); 
        if($input->isEmpty())
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }

        $input = $input->toArray()[0];
        #extract tag#
        $data_tag = json_decode($input['tag'], TRUE);
        $tag=[];
        foreach ($data_tag as $key => $value) {
            $tag[$key]=$value;
        }



        $input = array_merge($input, $tag);

        

        //dd($input);

        skip:
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'ctype',
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
            // $rules = [
            //             'cm_code'      => 'required'
            //         ];

            if($isupdate)
                {
                    $rules['cm_code']       = 'required|unique:'.$this->model->gettable().',cm_code,'.$request->input($this->fprimarykey)[0].','.$this->fprimarykey;
                    
                    $rules['latinname']       = 'required|unique:'.$this->model->gettable().',latinname,'.$request->input($this->fprimarykey)[0].','.$this->fprimarykey;

                }

            else
                {
                    $rules['cm_code']       = 'required|distinct|unique:'.$this->model->gettable().',cm_code';
                    $rules['latinname']       = 'required|distinct|unique:'.$this->model->gettable().',latinname';
                }

            $rules['ct_id'] ='required';


            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'unique' => __('ccms.fieldunique'),
                'distinct' => __('ccms.fielddistinct')
                
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


        #setup table data
        
        $tags=['cfacebook', 'cline', 'ctelegram', 'cwechat'];
        $tag=[];
        foreach ($tags as $field) 
        {
            $tag[$field]=$request->input($field);
                
        }

        $add_date=!empty($request->input('add_date'))?date("Y-m-d H:i:s", strtotime($request->input('add_date'))):date("Y-m-d H:i:s");
        $tableData = [
            
                $this->fprimarykey => $newid,
                'ul_id' => 0,
                'branch_id' => !empty($this->args['userinfo']['branch_id'])?$this->args['userinfo']['branch_id']:1,
                'cm_code' => $request->input('cm_code') ?? '',
                'latinname' => $request->input('latinname') ?? '',
                'nativename' => $request->input('nativename') ?? '',
                'ct_id' => $request->input('ct_id') ?? '',
                'vat' => $request->input('vat') ?? '',
                'cphone' => $request->input('cphone') ?? '',
                'cemail' => $request->input('cemail') ?? '',
                'caddress' => $request->input('caddress') ?? '',
                'cnote' => $request->input('cnote') ?? '',
                'personincharge' => $request->input('personincharge') ?? '',
                'cposition' => $request->input('cposition') ?? '',
                'pic' => $request->input('pic') ?? '',
                'cpassword' => !empty($request->input('cpassword'))? \Hash::make($request->input('cpassword')): '',
                'status' => $request->input('status') ?? '',
                'tag' => json_encode($tag),
                'trash' => 'no', 
                'add_date' => $add_date,
                'blongto' => $this->args['userinfo']['id'],
            
        ];

        if($isupdate)
        {
            $tableData = array_except($tableData, [$this->fprimarykey, 'blongto']);
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

        $delete = $this->model->where($this->fprimarykey, (int)$editid)->update(['trash'=>'yes']); 
        //return redirect()->back()->with('success', 'delete oK');
        return [
                    'act' => $delete,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_delete'),
                                    'id' => $editid
                                ]
                ]; 

    } /*../function..*/

    public function restore(Request $request, $id=0)
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
                                    'success' => __('ccms.suc_restore'),
                                    'id' => $editid
                                ]
                ];

    } /*../function..*/


    public function destroy(Request $request, $id=0)
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

        $replicate = $this->model->where($this->fprimarykey, (int)$editid)->get()->toArray()[0];

        #extract title#
        $data_title = json_decode($replicate['title'], TRUE);
        $title=[];
        foreach ($data_title as $key => $value) {
            $title[$key]=$value;
        }
        $newtitle=copyTitle($title[$this->dflang[0]],'',$this->model,"JSON_UNQUOTE(title->'$.".$this->dflang[0]."')");
        $title[$this->dflang[0]]=$newtitle;
        $newid = $this->model->max($this->fprimarykey)+1;
        $replicate[$this->fprimarykey] = $newid;
        $replicate['title']= json_encode($title);
        $replicate['v_counter'] = 0;
        $replicate['blongto'] = $this->args['userinfo']['id'];
        $savedata = $this->model->insert($replicate);
        
        //return redirect()->back();
        return [
                    'act' => $savedata,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_clone'),
                                    'id' => $newid
                                ]
                ];

    } /*../function..*/


    public function edit_field(Request $request)
    {
        $obj_info=$this->obj_info;
        if ($request->has('datainfo'))
        {
            $datainfo = $request->input('datainfo');
            $datainfo = html_entity_decode($datainfo);
            $datainfo = json_decode($datainfo, true);

            $field = $datainfo['field'];
            $id = $datainfo['id'];
            $newvalue = $datainfo['newdata'];
            $datainfo = [$field => $newvalue];
            $updatedata = $this->model->where($this->fprimarykey,$id)
                                            ->update($datainfo);

            return [
                    'act' => $updatedata,
                    'url' => '',
                    'passdata' => [
                                    
                                    'id' => $id
                                ]
                ];
        }
        
    }/*../function..*/


    public function ajaxreturn(Request $request){

        $newcustomer = [];
        if ($request->session()->has('cm_id')) {
            $id = $request->session()->get('cm_id');
            $name = $request->session()->get('latinname');
            $newcustomer = ['cm_id'=>$id, 'customer'=>$name];
        }

        
        
        $return = [
                    'callback' => 'gettingIdTitle',
                    'container' => '',
                    'data' => $newcustomer,
                    'message' => ''
                ];

        return json_encode($return);
    }/*../function..*/


    public function autocomplet(Request $request){
        $results = $this->listingmodel();
        $qry = $request->input('query');
        $onlyparent = $request->input('onlyparent');
        $madewith = $request->input('madewith');
        $suggestions=[];
        if(!empty($qry))
        {
            

            $results = $results->where('trash', '!=', 'yes')
            //->where('title', 'like', '%'.$qry.'%')
            ->where(function($query) use ($qry){
                 $query->whereRaw("lower(JSON_UNQUOTE(latinname)) like '%".strtolower($qry)."%'");
                 //$query->orWhereRaw("JSON_UNQUOTE(title->'$.*') like '%".$qry."%'");
                 $query->orWhere('nativename', 'like', $qry);
                 $query->orWhere($this->fprimarykey, '=', $qry);
                 //
                 
             });
            //dd($results->toSql());



            //dd($results->toSql() );




            

            $results = $results->get();
            foreach ($results as $row) {
                array_push($suggestions, ['value'=>$row->latinname,'data'=>$row->id]);
                
            }
        }

        return json_encode($suggestions);

    }/*../function..*/
	
    
}