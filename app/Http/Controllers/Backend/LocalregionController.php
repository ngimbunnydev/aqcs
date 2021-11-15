<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;

use App\Models\Backend\Region;



class LocalregionController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='location_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=200; #record per page#
    private $obj_info=['name'=>'localregion','title'=>'Local Region','routing'=>'admin.controller','icon'=>'<i class="fa fa-map-marker-alt" aria-hidden="true"></i>'];

    private $protectme;


	public function __construct(array $args){ //public function __construct(Array args){
    $this->obj_info['title'] = __('label.lb192');
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
		$this->model = new Region;
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
        $js_filemanagersetting=array(   'displaymode' => '2',
                                        'filetype'   =>'image',
                                        'givent_txtbox'=>'txt_scrshot',
                                        'calledby'=>'public', 
                                        'numperpage'=>12, 
                                        'ajax_url'=>config('ccms.js_env.ajaxpublic_url'), 
                                        'objtable'=>'', 
                                        'idvalue'=>0
                                    );

        $js_config = [
            'filemanagerSetting'    => $js_filemanagersetting,
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $categories=$this->model->getcategory($this->dflang[0])->get();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['location_id','parent_id'],0);

        $attributes=$this->model->getmodule($this->dflang[0])->pluck('title', 'md_id');
        
        return ['js_config'=>$js_config, 'cat_tree'=>$cat_tree, 'attributes'=>$attributes];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, parent_id, display,code,
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        );
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', 'c_id', 'ordering', 'add_date'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'ordering'; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'desc' : 'asc'; // default desc
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
        if($sort==$this->fprimarykey && $order=='desc')
        {
            $sort = '';
            $order = '';
        }
        

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

        //dd($sfp['results']->items());
        //$categories=$results->get();
        $categories = $sfp['results']->items();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['id','parent_id'],0);
    	return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with(['cat_tree' => $cat_tree])
                ->with($setting);

    } /*../function..*/


    /*public function trash(Request $request)
    {
        

    } /*../function..*/

    public function create(Request $request)
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $js_config = $default['js_config'];
        $cat_tree = $default['cat_tree'];
        $attributes = $default['attributes'];


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'attributes'
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

            } else {
                $data=$this->setinfo($request);
                $savedata = $this->model->insert($data['tableData']);
                

                if($savedata)
                {
                    
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $id = array_column($data['tableData'], $this->fprimarykey);


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
                                                        'id' => implode(',', $id)
                                                    ]
                                    ];
                    }
                    #end ajax SAVE

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
                                                        'id' => implode(',', $id)
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
                                                        'id' => implode(',', $id)
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
                                                        'id' => implode(',', $id)
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
                                                        'id' => implode(',', $id)
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

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$request->input($this->fprimarykey)[0]]);
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





        $input = null;
        if ($request->session()->has('input')) 
        {
           #No need to retrieve data becoz already set by Form#
            $editid=session('input')[$this->fprimarykey][0];
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
        $title=[];
        foreach ($data_title as $key => $value) {
            $title['title-'.$key]=$value;
        }



        $input = array_merge($input, $title);
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key]= [$value];
        }
        $input=$x;
        skip:


        $cat_tree = $default['cat_tree'];
        $categories=$this->model->getcategory($this->dflang[0], $editid)->get();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['location_id','parent_id'],0);

        $attributes = $default['attributes'];

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'attributes',
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

            $numrecord=count($request->input($this->fprimarykey));

            $rules['title-'.$this->dflang[0].'.*'] = "distinct";

            for($i=0; $i<$numrecord; $i++)
            {
               #$rules['title-'.$this->dflang[0].'.'.$i]       = 'required';
               #need to check all record

               if($isupdate)
                {
                    $rules['title-'.$this->dflang[0].'.'.$i]      = 'required|distinct|unique:'.$this->model->gettable().',title->>"$.'.$this->dflang[0].'"'.','.$request->input($this->fprimarykey)[0].','.$this->fprimarykey;

                    //JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title
                    
                }

                else
                {
                    $rules['title-'.$this->dflang[0].'.'.$i]       = 'required|distinct|unique:'.$this->model->gettable().',title->>"$.'.$this->dflang[0].'"';

                    

                }

            }


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
        $newid=($isupdate)? $request->input($this->fprimarykey)[0]  : $this->model->max($this->fprimarykey)+1;

        /*For translate*/
        $title=[];
        $tableData=[];
        $numrecord=count($request->input($this->fprimarykey));

        for($i=0; $i<$numrecord; $i++)
        {
            foreach (config('ccms.multilang') as $lang)
            {
                $title[$lang[0]]=$request->input('title-'.$lang[0])[$i];

            } #./foreach#

            $newid = $newid+$i;
            $parent_id = (int)$request->input('parent_id')[$i];
            if($newid==$parent_id) $parent_id=0;

            $record = [
            
                $this->fprimarykey => $newid,
                'parent_id' => $parent_id,
                'title' => json_encode($title),
                'code' => $request->input('code')[$i]??'',
                'image' => $request->input('image')[$i],
                'ordering' => (int)$request->input('ordering')[$i],
                'display' => $request->input('display')[$i],
                'ab_id' => (int)$request->input('ab_id')[$i]??0,
                'tag' => '',
                'trash' => 'no',
                'blongto' => $this->args['userinfo']['id']
            
            ];

            array_push($tableData, $record);

        }

        if($isupdate)
        {
            
            $tableData=$tableData[0];
            $tableData = array_except($tableData, [$this->fprimarykey, 'blongto']);
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
        $parent_id = $this->model->where($this->fprimarykey, (int)$editid)->value('parent_id');

        $update_child = $this->model->where('parent_id', (int)$editid)->update(['parent_id'=>$parent_id]); 
        $delete = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 
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
                                    'success' => 'restore ok'
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
                                    'success' => 'destroy ok'
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


    /********************************/
    public function editlist(Request $request){
        $listdata=$request->input('listdata');
        $listdata = html_entity_decode($listdata);
        
        if(!empty($listdata))
        {
            $json = json_decode($listdata, true);
            $update_child = $this->model->where('c_id', '<>', 0)->update(['parent_id'=>0]);
            $this->updatelist($json);
            $level = 1;
            foreach ($json as $element)
            {
                $update_child = $this->model->where('c_id', $element['id'])->update(['ordering'=>$level]);
                $level+= 1;
            }
            
        }

    }/*../function..*/

    function updatelist(array $elements,$level=1, $defaultField=['c_id','parent_id']) {
        $parent_id=0;
        foreach ($elements as $element) {
            
            if(isset($element['children'])) {
                $parent_id = $element['id'];
                $children = $element['children'];
                foreach ($children as $child) 
                {
                    
                    $update_child = $this->model->where('c_id', (int)$child['id'])->update(['parent_id'=>$parent_id, 'ordering'=> $level]);
                    $level+=1;

                    $this->updatelist($children, $level, $defaultField); 
                    
                }
                
            }
        }

    }/**@endfun**/


    public function ajaxreturn(){

        $default=$this->default();
        $cat_tree = $default['cat_tree'];
        $catlist= CategoryCheckboxTree($cat_tree,"","c_id[]",[]);
        $return = [
                    'callback' => 'greeting',
                    'container' => '#getcategory',
                    'data' => $catlist,
                    'message' => ''
                ];

        return json_encode($return);
    }/*../function..*/

    
}