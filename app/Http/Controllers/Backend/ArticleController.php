<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Image;
use Validator;

use App\Models\Backend\Article;
use App\Http\Controllers\Backend\ModulesController;


class ArticleController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='a_id';
    private $tbltranslate='cms_articledetail';
    private $tblfile='cms_articlefile';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set zero to get all record#
    private $obj_info=['name'=>'article','title'=>'Article','routing'=>'admin.controller','icon'=>'<i class="fa fa-file-text-o" aria-hidden="true"></i>'];

    private $modulescontroller;

	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
		$this->model = new Article;
        $this->dflang = config('ccms.multilang')[0];

        $this->modulescontroller = new ModulesController($args);

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
                                        'objtable'=>$this->tblfile, 
                                        'fathertable'=>'cms_article', 
                                        'fatherid'=>$this->fprimarykey, 
                                        'idvalue'=>0
                                    );

        $js_config = [
            'filemanagerSetting'    => $js_filemanagersetting,
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $categories=$this->model->getcategory($this->dflang[0])->get();
        $category_list = $categories->pluck('title','c_id');
        $category_attr = $categories->pluck('ab_id','c_id');
        //dd($category_attr);
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['c_id','parent_id'],0);

        $attributes=$this->model->getmodule($this->dflang[0])->pluck('title', 'md_id');
        
        return ['js_config'=>$js_config, 'cat_tree'=>$cat_tree, 'category_list' => $category_list, 'attributes'=>$attributes, 'category_attr'=>$category_attr];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, c_id, status, ordering,
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )->where('md_id',0);
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

        if (!empty($request->input('title'))) 
        {
            $qry=$request->input('title');
            $results = $results->where('title', 'like', '%'.$qry.'%');
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }


        if ($request->input('c_id')) 
        {
            $qry=$request->input('c_id');
            $results = $results->whereRaw("FIND_IN_SET('$qry',c_id)");
            array_push($querystr, 'c_id='.$qry);
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
        $cat_tree = $default['cat_tree'];
        $category_list = $default['category_list'];

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

    	return view('backend.v'.$this->obj_info['name'].'.index', compact('cat_tree', 'category_list'))
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
        $cat_tree = $default['cat_tree'];
        $category_list = $default['category_list'];

        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index', compact('cat_tree', 'category_list'))
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
        $cat_tree = $default['cat_tree'];

        $pages=$this->model->getpages($this->dflang[0])->pluck('p_name', 'p_id');
        $attributes = $default['attributes'];
        $category_attr = $default['category_attr'];
        /****** Delet media *****/
        if (!$request->session()->has('input')) 
        {
           deleteDataTable($this->tblfile,['obj_id'=>0,'blongto'=>$this->args['userinfo']['id']]);
        }


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'pages',
                            'attributes',
                            'category_attr'
                            )


                )->with(
                    [
                        'submitto'  => 'store',
                        'fprimarykey'     => $this->fprimarykey,
                        'caption' => __('ccms.new')
                    ]
                );
    } /*../function..*/

    public function insertintotable($data)
    {
                

                $savedata = $this->model->insert($data['tableData']);
                
                if($savedata)
                {
                    $savetranslate = insertDataTable($this->tbltranslate, $data['translateData']);

                    #update Table Media
                    $obj_id=$data['id'];
                    updateDataTable(    $this->tblfile,
                                        ['obj_id'=>0,'blongto'=>$this->args['userinfo']['id']], 
                                        ['obj_id'=>$obj_id]
                                    );
                    $cover = selectDataTable($this->tblfile,['obj_id'=>$obj_id, 'as_cover'=>'yes'])->get()->toArray();
                    $cover = empty($cover)?'':json_encode($cover[0]);
                    $updatecover = $this->model->where($this->fprimarykey,$obj_id)
                                            ->update(['imginfo'=>$cover]);
                    #update Table File

                }/*../if savedata==true..*/

                return $savedata;

    }/*../function..*/


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

                $savedata = $this->insertintotable($data);
                
                if($savedata)
                {
                    
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');

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
                                                        'input' => null,
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
                                                    ]
                                    ];

                }/*../if savedata==true..*/
            }
        } /*../if POST..*/

        
    } /*../function..*/

    public function updatedata($data)
    {
        
                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);
                
                #Update relative tabble#
                    #update Table Translate
                    foreach ($data['translateData'] as $key => $value) {

                        //check new language
                        $isnewlang = selectDataTable($this->tbltranslate,[$this->fprimarykey=>$data['id'],'lg_code'=>$value['lg_code']])->pluck('lg_code')->toArray();

                
                        
                        if(empty($isnewlang)){
                            //dd($isnewlang);
                            $translate_id=getmaxid($this->tbltranslate,'at_id')+1;
                            $value['at_id'] = $translate_id;
                            $savetranslate = insertDataTable($this->tbltranslate, $value);
                        }
                        else
                        {
                            $value = array_except($value, [$this->fprimarykey, 'at_id']);
                            $updatetranslate = updateDataTable(    $this->tbltranslate,
                                        [$this->fprimarykey=>$data['id'],'lg_code'=>$value['lg_code']], 
                                        $value
                                    );
                        }
                        
                    }
                    
                    #update Table Media
                    $obj_id=$data['id'];
                    updateDataTable(    $this->tblfile,
                                        ['obj_id'=>0,'blongto'=>$this->args['userinfo']['id']], 
                                        ['obj_id'=>$obj_id]
                                    );
                    $cover = selectDataTable($this->tblfile,['obj_id'=>$obj_id, 'as_cover'=>'yes'])->get()->toArray();
                    $cover = empty($cover)?'':json_encode($cover[0]);
                    $updatecover = $this->model->where($this->fprimarykey,$obj_id)
                                            ->update(['imginfo'=>$cover]);
                    #update Table File

        return $updatedata;
    }

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
                $updatedata = $this->updatedata($data);

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

                                if(stripos($routing, $obj_info['name'])===false)
                                {
                                    $routing=url_builder($obj_info['routing'],[$obj_info['name']]);
                                }
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
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
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
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
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
                                                        
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

    public function dobeforedit($request, $id){

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info=$this->obj_info;
        $default=$this->default();
        $js_config = $default['js_config'];
        $cat_tree = $default['cat_tree'];
        $attributes = $default['attributes'];
        $category_attr = $default['category_attr'];

        $pages=$this->model->getpages($this->dflang[0])->pluck('p_name', 'p_id');
        

        $input = null;

        # Delet media #
        if (!$request->session()->has('input')) 
        {
           deleteDataTable($this->tblfile,['obj_id'=>0,'blongto'=>$this->args['userinfo']['id']]);
           
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
        $title=[];
        foreach ($data_title as $key => $value) {
            $title['title-'.$key]=$value;
        }

        #extract tag#
        $data_tag = json_decode($input['tag'], TRUE);
        $tag=[];
        foreach ($data_tag as $key => $value) {
            $tag[$key]=$value;
        }

        #attribute
        if(!empty($input['ab_id'])){
            $moduleid=$input['ab_id'];
        }
        elseif(!empty($input['md_id'])){
            $moduleid=$input['md_id'];
        }
        else{
            $moduleid=-1;
        }
        $att_ele  = json_decode($input['att_ele'], TRUE);
        $att_ele = $this->modulescontroller->extractformdata($att_ele, $moduleid);
        #Retrieve from Translate#
        $translate=selectDataTable($this->tbltranslate,[$this->fprimarykey=>$editid])->get()->toArray();
        $tranrecord=[];
        foreach ($translate as $key => $value) {
            $lang = $value->lg_code;
            $tran = $value->translate;
            $tran_decode = json_decode($tran, TRUE);
            
            foreach ($tran_decode as $key => $value) {
                $tranrecord[$key.'-'.$lang]=$value;
            }
        }


        $input = array_merge($input, $title);
        $input = array_merge($input, $tag);
        $input = array_merge($input, $att_ele);
        $input = array_merge($input, $tranrecord);
        $input['c_id'] = explode(',', $input['c_id']);
        $input['add_date'] = date("d-m-Y", strtotime($input['add_date'])); 
        $input['exp_date'] = date("d-m-Y", $input['exp_date']);

        return ['obj_info'=>$obj_info,
                            'js_config'=>$js_config,
                            'cat_tree'=>$cat_tree,
                            'attributes'=>$attributes,
                            'category_attr'=>$category_attr,
                            'pages'=>$pages,
                            'input'=>$input];
    }

    public function edit(Request $request, $id=0)
    {
        $beforedit=$this->dobeforedit($request, $id);
        if(!is_array($beforedit)) return $beforedit;
        foreach($beforedit as $key => $value) {
           $$key = $value;
        }
        
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'attributes',
                            'category_attr',
                            'pages',
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
            $rules = [
                        'title-'.$this->dflang[0]       => 'required'
                    ];
            //dd($request->input());
            if($request->has('md_id')){
                $attr_rules = $this->modulescontroller->validationrule($request,$request->input('md_id'));

                    $rules=array_merge($rules, $attr_rules);
                
                
            }

            if($request->has('ab_id')){
                $attr_rules = $this->modulescontroller->validationrule($request,$request->input('ab_id'));

                    $rules=array_merge($rules, $attr_rules);
                
                
            }
            
            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            //dd($rules);
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

        /*For translate*/
        $translate=['des','metatitle', 'metakeyword', 'metades'];
        $title=[];
        $translateData=[];
        $translate_id=getmaxid($this->tbltranslate,'at_id')+1;

        $first = true;
        $df_title=[];
        $df_translate=[];
        foreach (config('ccms.multilang') as $lang)
        {
            $title[$lang[0]]=$request->input('title-'.$lang[0]);

            foreach ($translate as $field) 
            {
                $content[$field]=$request->input($field.'-'.$lang[0]); 
                
            }

            

            if($first){
                $df_title = $title[$lang[0]];
                $df_translate = $content; 
            }else{
                if(strtolower($request->input('synlang'))=='yes'){
                    $title[$lang[0]]=$df_title;
                    $content=$df_translate;
                }
            }
            

            $translateRecord = [
                    'at_id' => $translate_id,
                    $this->fprimarykey => $newid,
                    'lg_code' => $lang[0],
                    'translate' => json_encode($content)
            
                ];


            $first = false;
            $translate_id++;

            array_push($translateData,$translateRecord);
        }

        /*setup table data*/
        
        $tags=['frontpage', 'searchable', 'seoindex'];
        $tag=[];
        foreach ($tags as $field) 
        {
            $tag[$field]=$request->input($field);
                
        }

        #attribute
        $attribute = !empty($request->input('ab_id'))?(int)$request->input('ab_id'):0;
        $md_id = !empty($request->input('md_id'))?(int)$request->input('md_id'):0;

        $mdp_id=0;
        $att_ele='';
        if(!empty($md_id)){
            $mdp_id=($isupdate)? $request->input('mdp_id')  : $this->model->where('md_id',$md_id)->max('mdp_id')+1;;
            $att_ele = $this->modulescontroller->generateformdata($request, $md_id);
        }
        elseif(!empty($attribute)){
            $att_ele = $this->modulescontroller->generateformdata($request, $attribute);
        }
        

        $add_date=!empty($request->input('add_date'))?date("Y-m-d H:i:s", strtotime($request->input('add_date'))):date("Y-m-d H:i:s");

        $exp_date=!empty($request->input('exp_date'))?strtotime($request->input('exp_date')):0;
        $c_id= $request->input('c_id') ?? [0];

        $tableData = [
            
                $this->fprimarykey => $newid,
                'title' => json_encode($title),
                'imginfo' => '',

                'c_id' => implode(',', $c_id),
                'status' => !empty($request->input('status')) ? $request->input('status') : 'publish',
                'pm_id' => !empty($request->input('pm_id')) ? $request->input('pm_id') : 0,
                'pm_pwd' => !empty($request->input('pm_pwd')) ? $request->input('pm_pwd') : 0,
                'parent_id' => 0,

                'tag' => json_encode($tag),
                'v_counter' => 0,

                'ordering' => (int)$request->input('ordering'),
                'trash' => 'no',

                'add_date' => $add_date,
                'exp_date' => $exp_date,
                'p_id'  =>  !empty($request->input('p_id')) ? $request->input('p_id') : 0,
                'ab_id' => $attribute,
                'md_id' => $md_id,
                'mdp_id' => $mdp_id,
                'att_ele' => json_encode($att_ele),
                'blongto' => $this->args['userinfo']['id'],
            
        ];

        if($isupdate)
        {
            $tableData = array_except($tableData, [$this->fprimarykey, 'v_counter', 'md_id', 'mdp_id', 'blongto']);
            $translateData = array_except($translateData, [$this->fprimarykey, 'at_id']);
        }

        return ['tableData' => $tableData, 'translateData' => $translateData, 'id'=>$newid];
        

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
        if($destroy)
        {
            deleteDataTable($this->tblfile,['obj_id'=>(int)$editid]);
            deleteDataTable($this->tbltranslate,[$this->fprimarykey=>(int)$editid]);
        }

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
	
    
}