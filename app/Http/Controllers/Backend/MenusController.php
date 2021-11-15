<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;

use App\Models\Backend\Menus;



class MenusController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='m_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'menus','title'=>'Menus','routing'=>'admin.controller','icon'=>'<i class="fa fa-bars" aria-hidden="true"></i>'];


	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
        $this->model = new Menus;
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
        $js_filemanagersetting=array(   'displaymode' => '1',
                                        'filetype'   =>'',
                                        'givent_txtbox'=>'txt_scrshot',
                                        'calledby'=>'public', 
                                        'numperpage'=>12, 
                                        'ajax_url'=>config('ccms.js_env.ajaxpublic_url'), 
                                        'objtable'=>'cms_articlefile', 
                                        'idvalue'=>0
                                    );

        $js_config = [
            'filemanagerSetting'    => $js_filemanagersetting,
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $categories=$this->model->getcategory($this->dflang[0])->get();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['c_id','parent_id'],0);


        $pcategories=$this->model->getpcategory($this->dflang[0])->get();
        $pcategories = json_decode(json_encode($pcategories), true);
        $pcat_tree=buildArrayTree($pcategories,['c_id','parent_id'],0);
        
        return ['js_config'=>$js_config, 'cat_tree'=>$cat_tree, 'pcat_tree'=>$pcat_tree];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )->where('parent_id',0);
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', 'c_id', 'ordering', 'add_date'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            $results = $results->where('title', 'like', '%'.$qry.'%');
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

        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['act'=>'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with($setting);

    } /*../function..*/


    public function trash(Request $request)
    {
        $obj_info=$this->obj_info;

        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['act'=>'trash'])
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
        $cat_tree = $default['cat_tree'];
        $pcat_tree = $default['pcat_tree'];
        /****** Delet media *****/
        if (!$request->session()->has('input') AND !$request->session()->has('remove')) 
        {
           $destroy = $this->model->where(['mgroup'=>-1,'blongto'=>$this->args['userinfo']['id']])->delete();
        }

        $results = $this->model->select(\DB::raw(   $this->fprimarykey.", parent_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                ))
                    ->where(['mgroup'=>-1,'blongto'=>$this->args['userinfo']['id']])
                    ->orderby('ordering', 'asc')
                    ->orderby($this->fprimarykey, 'asc')
                    ->get();

        $results = json_decode(json_encode($results), true);
        $itemtree=buildArrayTree($results,['m_id','parent_id'],-1);


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'pcat_tree',
                            'itemtree'
                            )


                )->with(
                    [
                        'dflang' => $this->dflang,
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
                    
                    #Update Menu ITEMS#
                    $obj_id=$data['id'];
                    $updatedata = $this->model->where(['mgroup'=>-1, 'blongto'=>$this->args['userinfo']['id']])
                                            ->update([
                                                'mgroup'    => $obj_id
                                            ]);

                    $updatedata = $this->model->where(['parent_id'=>-1, 'blongto'=>$this->args['userinfo']['id']])
                                            ->update([
                                                'parent_id'    => $obj_id
                                            ]);

                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $id = array_column($data['tableData'], $this->fprimarykey);
                    switch ($savetype) {
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
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id'].','.implode(',', $id)
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
                                                        'success' => $success_ms,
                                                        'id' => $data['id'].','.implode(',', $id)
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
                                                        'success' => $success_ms,
                                                        'id' => $data['id'].','.implode(',', $id)
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
                                                        'success' => $success_ms,
                                                        'id' => $data['id'].','.implode(',', $id)
                                                    ]
                                    ];
                            break;

                    }
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
        $cat_tree = $default['cat_tree'];
        $pcat_tree = $default['pcat_tree'];
        $input = null;
        if ($request->session()->has('input')) 
        {
           #No need to retrieve data becoz already set by Form#
            $editid=session('input')[$this->fprimarykey];
            //goto skip;
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

        $input = $this->model->where($this->fprimarykey, (int)$editid)->get(); 
        if($input->isEmpty())
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }

        $input = $input->toArray()[0];
        #extract title#
        $data_title = json_decode($input['title'], TRUE);
        $input['title'] = $data_title[$this->dflang[0]];

        skip:

        $results = $this->model->select(\DB::raw(   $this->fprimarykey.", parent_id,linktype, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"))
                    ->where(['mgroup'=>(int)$editid])
                    ->orderby('ordering', 'asc')
                    ->orderby($this->fprimarykey, 'asc')
                    ->get();

        $results = json_decode(json_encode($results), true);
        $itemtree=buildArrayTree($results,[$this->fprimarykey,'parent_id'],$editid);
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'pcat_tree',
                            'itemtree',
                            'input'
                            )


                )->with(
                    [
                        'dflang' => $this->dflang,
                        'submitto'      => 'update',
                        'fprimarykey'   => $this->fprimarykey,
                        'caption' => __('ccms.edit')
                    ]
                );
    } /*../end fun..*/


    public function change(Request $request){
        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            
            $validator = $this->validation1($request);
            if ($validator->fails()) {
                //$errors = $validator->errors();
                //foreach ($errors->all() as $message) {
                    //echo $message;
                //}

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'editmenu/'.$request->input($this->fprimarykey)]);

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
                $data=$this->setinfo1($request);
                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);

                ############
                $savetype=strtolower($request->input('savetype'));
                $success_ms = __('ccms.suc_edit');
                $title = $request->input('title-'.$this->dflang[0]);
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'afteredit']);
                #when use ajax to SAVE
                   return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'title' => $title,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                            ];
                #end ajax SAVE
                    
               
            }
        } /*../if POST..*/
    }


    public function editmenu(Request $request, $id=0)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }
        
        $obj_info=$this->obj_info;
        $input = null;
        
        #Retrieve Data#
        if (empty($id))
        {
            //$editid = $this->args['routeinfo']['id'];
            $editid = (int)$request->input('ajaxid');
        }
        else
        {
            $editid = $id;
        }

        $input = $this->model->where($this->fprimarykey, (int)$editid)->get(); 
        if($input->isEmpty())
        {

            // $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            // return \Redirect::to($routing)
            // ->with('errors', __('ccms.rqnvalid'));
            return '<div class="alert alert-danger">
                                            
                                            '.__('ccms.rqnvalid').'
                                            
                                        </div>';
        }

        $input = $input->toArray()[0];
        #extract title#
        $data_title = json_decode($input['title'], TRUE);
        $title=[];
        foreach ($data_title as $key => $value) {
            $title['title-'.$key]=$value;
        }

        $input = array_merge($input, $title);


        return view('backend.v'.$this->obj_info['name'].'.edit',
                    compact('obj_info',
                            'input'
                            )


                )->with(
                    [
                        'fprimarykey'   => $this->fprimarykey,
                        'caption' => __('ccms.edit')
                    ]
                );
    } /*../end fun..*/

    public function validation($request, $isupdate=false){
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $update_rules= [ $this->fprimarykey => 'required'];
            $rules = [
                        'title'       => 'required'
                    ];

            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire')
                
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

        $title=[];
        foreach (config('ccms.multilang') as $lang)
        {
            $title[$lang[0]]=$request->input('title');
        }


        $tableData = [

            $this->fprimarykey => $newid,
            'parent_id' => 0, 
            'title' => json_encode($title), 
            'linktype' => '', 
            'p_id' => 0,
            'linkto' => '', 
            'target' => '', 
            'isindex' => '', 
            'tags' => '', 
            'ordering' => 0, 
            'mgroup' => $newid, 
            'trash' => 'no', 
            'blongto' => $this->args['userinfo']['id']                        
        ];

        if($isupdate)
        {
            $tableData = ['title' => json_encode($title)];
        }

        return ['tableData' => $tableData, 'id'=>$newid];
        

    }/*../function..*/


    public function validation1($request){
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $rules = [
                         'title-'.$this->dflang[0]       => 'required',
                         $this->fprimarykey => 'required'
                    ];


            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire')
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
            $validator =Validator::make($request->input(), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

     public function setinfo1($request){

        $newid=$request->input($this->fprimarykey);

        $title=[];
        foreach (config('ccms.multilang') as $lang)
        {
            $title[$lang[0]]=$request->input('title-'.$lang[0]);
        }


        $tableData = [

            'title' => json_encode($title), 
            'linkto' => $request->input('linkto'), 
            'target' => $request->input('target'), 
            'isindex' => $request->input('isindex'), 
            'tags' => $request->input('tags'),                      
        ];

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

        $destroy = $this->model->where('mgroup', (int)$editid)->delete(); 

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


    public function remove(Request $request, $id=0)
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
        $parent_id = $this->model->where($this->fprimarykey, (int)$editid)->value('parent_id');

        $update_child = $this->model->where('parent_id', (int)$editid)->update(['parent_id'=>$parent_id]); 
        $delete = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 

        return [
                    'act' => $delete,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_delete'),
                                    'remove' => true,
                                    'submitto' => 'create',
                                    'id' => $editid
                                ]
                ];
        

    } /*../function..*/


    /*public function duplicate(Request $request, $id=0)
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
                                    'success' => __('ccms.suc_clone')
                                ]
                ];

    } /*../function..*/


    /********************************/
    public function editlist(Request $request){
        
        if ($request->has('listdata') && $request->has('parent_id'))
        {
            $listdata=$request->input('listdata');
            $listdata = html_entity_decode($listdata);
            $json = json_decode($listdata, true);

            $parent_id = $request->input('parent_id');
            if($parent_id==-1){
                $update_child = $this->model->where(['mgroup'=>-1, 'blongto'=>$this->args['userinfo']['id']])->update(['parent_id'=>-1]);
            }
            else
            {
                $update_child = $this->model->where([
                    ['mgroup','=',$parent_id],['m_id','<>',$parent_id]
                ])->update(['parent_id'=>$parent_id]);
            }

            
            $this->updatelist($json);
            $level = 1;
            foreach ($json as $element)
            {
                $update_child = $this->model->where('m_id', $element['id'])->update(['ordering'=>$level]);
                $level+= 1;
            }
            
        }

    }/*../function..*/

    function updatelist(array $elements,$level=1, $defaultField=['m_id','parent_id']) {
        $parent_id=0;
        foreach ($elements as $element) {
            
            if(isset($element['children'])) {
                $parent_id = $element['id'];
                $children = $element['children'];
                foreach ($children as $child) 
                {
                    
                    $update_child = $this->model->where('m_id', (int)$child['id'])->update(['parent_id'=>$parent_id, 'ordering'=> $level]);
                    $level+=1;

                    $this->updatelist($children, $level, $defaultField); 
                    
                }
                
            }
        }

    }/**@endfun**/


    public function addtomenu(Request $request){
        $obj_info=$this->obj_info;
        $return = [];
        $tableData = [];
        $menulist = '';
        if ($request->has('objinfo')) 
        {
            $objinfo = $request->input('objinfo');
            $objinfo = html_entity_decode($objinfo);
            $objinfo = json_decode($objinfo, true);
                        
            if ($request->has('menusitems')) 
            {
                $menusitems = $request->input('menusitems');
                $menusitems = html_entity_decode($menusitems);
                $menusitems = json_decode($menusitems, true);
                if($menusitems OR $objinfo['obj']=='custom')
                {
                    if($objinfo['obj']=='custom')
                    {
                        $title=[];
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $title[$lang[0]]='Untitle';
                        }
                        $results=[['id' =>'', 'title' => json_encode($title)]];
                    }
                    else
                    {
                        
                        $model = \App::make('App\Models\Backend\\'.ucfirst($objinfo['obj']));
                
                        if (\Schema::hasColumn($model->getTable(), 'p_id')){
                            $results = $model->select($objinfo['modelid']." AS id", "p_id","title");
                        }
                        else
                        {
                            $results = $model->select($objinfo['modelid']." AS id","title");
                        }
                        
                        $results = $results ->whereIn($objinfo['modelid'],$menusitems)
                        //->orderby('ordering', 'asc')
                        ->get()->toArray();
                    }

                    


                    $newid = $this->model->max($this->fprimarykey);
                    $parent_id = empty($objinfo['parent_id']) ? -1 : (int)$objinfo['parent_id'];

                    foreach ($results as $item) {
                        $title = json_decode($item['title'], true);
                        $id = ++$newid;
                        $menulist.= "<li class='dd-item dd2-item' data-id='".$id."'>
                            <div class='dd-handle dd2-handle'>
                                <i class='normal-icon ace-icon fa fa-arrows blue bigger-120'></i>
                                <i class='drag-icon ace-icon fa fa-arrows bigger-125'></i>
                            </div>
                            <div class='dd2-content'>
                                <span id='content".$id."'>".$title[$this->dflang[0]]." (".$objinfo['obj'].")</span>
                                <div class='pull-right action-buttons'>
                                    <a class='blue editmenu' href='#' data-menu='".$id."'>
                                        <i class='ace-icon fa fa-pencil bigger-130'></i>
                                    </a>

                                    <a class='red' href='".url_builder($obj_info['routing'],
                                            [$obj_info['name'],'remove',$id],
                                            []
                                        )."' data-menu='".$id."'>
                                        <i class='ace-icon fa fa-times-circle bigger-130'></i>
                                    </a>
                                </div>
                            </div>

                        </li>";

                        #insert data to table temporarayly#

                        $record = [
            
                            $this->fprimarykey => $id,
                            'parent_id' => $parent_id, 
                            'title' => $item['title'], 
                            'linktype' => $objinfo['obj'], 
                            'p_id'  => isset($item['p_id']) ? $item['p_id'] : 0,
                            'linkto' => $item['id'], 
                            'target' => '_self', 
                            'isindex' => 'no', 
                            'tags' => '', 
                            'ordering' => 100, 
                            'mgroup' => $parent_id, 
                            'trash' => 'no', 
                            'blongto' => $this->args['userinfo']['id']
                        
                        ];

                        array_push($tableData, $record);


                    } #@endforeach#

                     $savedata = $this->model->insert($tableData);
                }
                
            } /*../if*/
        }


        $return = [
                    'callback' => 'addtomenu',
                    'container' => '.dd ol:first',
                    'data' => $menulist,
                    'message' => ''
                ];

        return json_encode($return);
    }/*../function..*/


    public function afteredit(Request $request)
    {
        $id = 0;
        $title = 'Untitle';
        if ($request->session()->has($this->fprimarykey)) {
            $id = $request->session()->get($this->fprimarykey);
        }

        if ($request->session()->has('title')) {
            $title = $request->session()->get('title');
        }

        $return = [
                    'callback' => 'greeting',
                    'container' => '.dd2-content #content'.$id,
                    'data' => $title,
                    'message' => ''
                ];

        return json_encode($return);
    }/*../function..*/


    public function menufilter(Request $request)
    {
        $obj_info=$this->obj_info;
        $return = [];

        if ($request->has('objinfo')) 
        {
            $objinfo = $request->input('objinfo');
            $objinfo = html_entity_decode($objinfo);
            $objinfo = json_decode($objinfo, true);
              
            $searchtext = $objinfo['searchtext'];
                
                if($searchtext)
                {
                    $model = \App::make('App\Models\Backend\\'.ucfirst($objinfo['obj']));
                    $results = $model->select(
                            \DB::raw($objinfo['modelid']." AS id, 
                                        JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                    )
                        )
                        ->where('title',$searchtext)
                        ->orWhere('title', 'like', '%' . $searchtext . '%')
                        //->orderby('ordering', 'asc')
                        ->get()->toArray();

                    $listing = checkbox_select($objinfo['obj'].'[]',$results,[],'');
                    if($listing)
                    {
                        $return = [
                        'callback' => 'greeting',
                        'container' => '#'.$objinfo['obj'].'-menu',
                        'data' => $listing,
                        'message' => ''
                        ];
                    } #if listiing#

                }#if searchtext#
                
        } #objinfo#     

        return json_encode($return);

    }/*../function..*/
	
    
}