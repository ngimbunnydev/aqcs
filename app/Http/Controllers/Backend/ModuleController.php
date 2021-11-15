<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;

use App\Models\Backend\Module;
use App\Models\Backend\Datalist;
use App\Http\Controllers\Backend\ModulesController;



class ModuleController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='md_id';
    private $tblsub='cms_modules';
    private $foreignkey='mds_id';
    private $modules;
    private $dflang;
    private $request;
    private $rcdperpage=0; #record per page#
    private $obj_info=['name'=>'module','title'=>'Module','routing'=>'admin.controller','icon'=>'<i class="fa fa-folder" aria-hidden="true"></i>'];


	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
		$this->model = new Module;
        $this->dflang = config('ccms.multilang')[0];
        $this->modules = new ModulesController($args);
        

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
        $datalists=Datalist::getparent($this->dflang[0])->pluck('title', 'dl_id');
        //$datalists = json_decode(json_encode($datalists), true);
        
        return ['datalists'=>$datalists];
    } /*../function..*/

     public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, moduletype,
                                                    JSON_UNQUOTE(moduletitle->'$.".$this->dflang[0]."') AS title"
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
        $module = $sfp['results']->items();
        $module = json_decode(json_encode($module), true);
        //dd($categories);
        $count = count($module);
        for ($i=0; $i < $count; $i++) { 
           $md_id = $module[$i]['id'];
           $modules = $this->modules->listingModel($md_id)->orderby('ordering')->get()->toArray();
           if(!empty($modules))
            $module[$i]['children'] = $modules ;
    
        }

        //dd($module);
        
        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with(['cat_tree' => $module])
                ->with($setting);

    } /*../function..*/


    /*public function trash(Request $request)
    {
        

    } /*../function..*/

    public function create(Request $request)
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $datalists = $default['datalists'];
        


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'datalists'
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
            $validator_sub = $this->modules->validation($request);
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
            elseif ($validator_sub->fails()) {
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator_sub->errors()->first(),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];
            }


            else {
                $data=$this->setinfo($request);
                $savedata = $this->model->insert($data['tableData']);

                if($savedata){
                    $data_sub=$this->modules->setinfo($request,$data['id']);
                    $savedata_sub = $this->modules->model->insert($data_sub['tableData']);
                }
                
                

                if($savedata && $savedata_sub)
                {
                    
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $id = array_column($data['tableData'], $this->fprimarykey);
                    //$id = array_push($id, $data['id']);
                    
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
            $validator_sub = $this->modules->validation($request, true);
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$request->input($this->fprimarykey)[0]]);
            if ($validator->fails()) {
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input()
                                ]
                ];

            } 
            elseif($validator_sub->fails()){
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator_sub->errors()->first(),
                                    'input' => $request->input()
                                ]
                ];
            }

            else {
                $data=$this->setinfo($request, true);

                $tableData = array_except($data['tableData'], ['blongto']);
                $updatedata = $this->model->where($this->fprimarykey,$data['tableData'][$this->fprimarykey])
                                            ->update($tableData);

                /*$updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);*/
                $parent_id = $data['tableData'][$this->fprimarykey];
                $data_sub = $this->modules->setinfo($request,$parent_id, true);
                $modules_fprk = $this->modules->fprimarykey;
               
                foreach ($data_sub['tableData']as $row) {

                    if(empty($row[$modules_fprk]))
                    {
                        $newid = $this->modules->model->max($modules_fprk)+1;
                        $row[$modules_fprk] = $newid;

                        $savedata = $this->modules->model->insert($row);
                    }
                    else
                    {
                        $editid = $row[$modules_fprk];
                        $tableData = array_except($row, [$modules_fprk, $this->fprimarykey, 'ordering', 'blongto']);
                        $updatedata = $this->modules->model->where($modules_fprk,$editid)
                                            ->update($tableData);
                    }
                    

                }
                
                

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
        $datalists = $default['datalists'];

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

        $parent = $this->model->where(
                    [
                        $this->fprimarykey => (int)$editid,
                    ]
                )->get(); 

        if($parent->isEmpty())
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));

        }

        $parent = $parent->toArray()[0];

        $input = $this->modules->model->where('md_id', (int)$parent[$this->fprimarykey])->orderby('ordering')->get(); 

        $input = $input->toArray();

        $input_tmp=[];
        foreach ($input as $row) {
            #extract title#
            $data_title = json_decode($row['title'], TRUE);
            $title=[];
            foreach ($data_title as $key => $value) {
                $input_tmp['title-'.$key][]=$value;
            }

            foreach ($row as $key => $value) {
                $input_tmp[$key][]=$value;
            }
        }

        //dd($input_tmp);
        
        $input=$input_tmp;

        $data_title = json_decode($parent['moduletitle'], TRUE);
        $title=[];
        foreach ($data_title as $key => $value) {
            $title['moduletitle-'.$key]=$value;
        }

        $input = array_merge($input, $parent);
        $input = array_merge($input, $title);
        skip:


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'datalists',
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
                        'title-'.$this->dflang[0]       => 'required',
                        'modulename'      => 'required|regex:/([A-Za-z0-9 ])+/'
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
        $moduletitle=[];
        foreach (config('ccms.multilang') as $lang)
            {
                $moduletitle[$lang[0]]=$request->input('moduletitle-'.$lang[0]);

            } #./foreach#
        $tableData = [
            
                $this->fprimarykey => $newid,
                'modulename' => $request->input('modulename'),
                'moduletitle' => json_encode($moduletitle),
                'moduletype' => $request->input('moduletype'),
                'icon' => $request->input('icon'),
                'description' => !empty($request->input('description'))? $request->input('description') : '',
                'meta' => !empty($request->input('meta'))? $request->input('meta') : '',
                'setting' => !empty($request->input('setting'))? $request->input('setting') : '',
                'acategory' => !empty($request->input('acategory'))? $request->input('acategory') : '',
                'media' => !empty($request->input('media'))? $request->input('media') : '',
                'display' => '',
                'tag' => '',
                'ordering' =>0,
                'trash' => 'no',
                'blongto' => $this->args['userinfo']['id']
            
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
        

        if($editid>0)
        {
            $delete = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 
            $delete_sub = $this->modules->model->where($this->fprimarykey, (int)$editid)->delete(); 
        }else{
            $editid = abs((int)$editid);
            $delete = $this->modules->model->where($this->modules->fprimarykey, $editid)->delete(); 
        }
        
        //$delete = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 
        //return redirect()->back()->with('success', 'delete oK');
        return [
                    'act' => $delete,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_delete'),
                                    'id' => abs((int)$editid)
                                ]
                ]; 

    } /*../function..*/

    public function restore(Request $request, $id=0)
    {
        return null;

    } /*../function..*/


    public function destroy(Request $request, $id=0)
    {
        return null;

    } /*../function..*/


    public function duplicate(Request $request, $id=0)
    {
        return null;
    } /*../function..*/

    
}