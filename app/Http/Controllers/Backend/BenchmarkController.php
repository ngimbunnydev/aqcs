<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
#use Illuminate\Validation\Rule;

use App\Models\Backend\Benchmark;
use App\Models\Backend\Color;
use App\Models\Backend\Evaluation;
use App\Models\Backend\Airtype;



class BenchmarkController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='benchmark_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'benchmark','title'=>'Benchmark Quality','routing'=>'admin.controller','icon'=>'<i class="fa fa-book-open" aria-hidden="true"></i>'];
    
    private $protectme;

	public function __construct(array $args){ //public function __construct(Array args){
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
    
    $this->obj_info['title'] = __('label.lb18');
        $this->args = $args;
		$this->model = new Benchmark;
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
        

        $js_config = [
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $airtype = Airtype::where('trash', '!=', 'yes')->select(\DB::raw("airtype_id, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->pluck('title', 'airtype_id')->toArray();
      
        $evaluation = Evaluation::where('trash', '!=', 'yes')->select(\DB::raw("evaluation_id, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->pluck('title', 'evaluation_id')->toArray();

        $colors=Color::getcolor($this->dflang[0])->pluck('title', 'cl_id');
        
        return ['js_config'=>$js_config, 'evaluation' => $evaluation, 'colors' => $colors, 'airtype' => $airtype];
    } /*../function..*/

    public function listingModel()
    {
        $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}
      
        #DEFIND MODEL#
        return $this->model
        ->leftJoin('aqcs_airtype','aqcs_benchmark.airtype_id', '=', 'aqcs_airtype.airtype_id')
        ->select(\DB::raw(   $this->fprimarykey." AS id, evaluation_id,rangfrom,rangto,indexfrom,indexto, cl_id,
        JSON_UNQUOTE(aqcs_airtype.title->'$.".$this->dflang[0]."') AS airtype,
        JSON_UNQUOTE(aqcs_benchmark.description->'$.".$this->dflang[0]."') AS description"
                                                )
    );
          
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', 'benchmark_id'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'benchmark_id'; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            $results = $results
            ->where('aqcs_benchmark.title', 'like', '%'.$qry.'%')
            ->whereOr('aqcs_benchmark.code', 'like', '%'.$qry.'%')
            ->whereOr('aqcs_benchmark.model', 'like', '%'.$qry.'%')
            ->whereOr('aqcs_benchmark.device_index', 'like', '%'.$qry.'%');
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }

        if ($request->input('status') && !empty($request->input('status'))) 
        {
            $qry=$request->input('status');
           
            $results = $results->where("aqcs_benchmark.status",$qry);
            array_push($querystr, 'status='.$qry);
            $appends = array_merge ($appends,['status'=>$qry]);
        }

        if ($request->input('location_id') && !empty($request->input('location_id'))) 
        {
            
            $qry=$request->input('location_id');
           
            $results = $results->WhereIn("aqcs_benchmark.location_id",$qry);
            foreach($qry as $ind){
              array_push($querystr, 'location_id='.$ind);
            }
            
            $appends = array_merge ($appends,['location_id'=>$qry]);
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
        $evaluation = $default['evaluation'];
        $airtype = $default['airtype'];
        $colors = $default['colors'];
        #DEFIND MODEL#
        $results = $this->listingmodel();
        if(empty($condition))
        {
            $results = $results->where('aqcs_benchmark.trash', '!=', 'yes');
        }
        else
        {
            //
        }

        $sfp = $this->sfp($request, $results);
    	return view('backend.v'.$this->obj_info['name'].'.index', compact('evaluation', 'airtype','colors'))
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
        $evaluation = $default['evaluation'];
        $airtype = $default['airtype'];
        $colors = $default['colors'];
        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('aqcs_device.trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index', 
                    compact(
                        'evaluation', 
                        'airtype',
                        'colors',
                    )
                )
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
        $evaluation = $default['evaluation'];
        $colors = $default['colors'];
        $airtype = $default['airtype'];


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'evaluation',
                            'colors',
                            'airtype'
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
        $evaluation = $default['evaluation'];
        $colors = $default['colors'];
        $airtype = $default['airtype'];


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
        $data_title = json_decode($input['description'], TRUE);
        $title=[];
        foreach ($data_title as $key => $value) {
            $title['description-'.$key]=$value;
        }
        $input = array_merge($input, $title);
      
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key]= [$value];
        }
        $input=$x;
        $input['airtype_id'] = $input['airtype_id'][0];
        skip:

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'evaluation',
                            'colors',
                            'airtype',
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
            $rules=[];
            $numrecord=count($request->input($this->fprimarykey));


            $rules['title-'.$this->dflang[0].'.*'] = "distinct";
            $rules['airtype_id']       = 'required|numeric|gt:0';
            for($i=0; $i<$numrecord; $i++)
            {
               $rules['evaluation_id.'.$i]       = 'required|numeric|gt:0';  
            }




            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire').'The :attribute field can not be blank.',
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
        $numrecord=1;
        if ($request->has($this->fprimarykey))
        {
            $numrecord=count($request->input($this->fprimarykey));
        }

        for($i=0; $i<$numrecord; $i++)
        {
            foreach (config('ccms.multilang') as $lang)
            {
                $title[$lang[0]]=$request->input('description-'.$lang[0])[$i];

            } #./foreach#

            $record = [
            
                $this->fprimarykey => $newid+$i,
                'airtype_id' => !empty($request->input('airtype_id'))?$request->input('airtype_id'):0,
                'evaluation_id'   => !empty($request->input('evaluation_id')[$i])?$request->input('evaluation_id')[$i]:0,
                'title' => '',
                'rangfrom' => !empty($request->input('rangfrom')[$i])?$request->input('rangfrom')[$i]:'',
                'rangto' => !empty($request->input('rangto')[$i])?$request->input('rangto')[$i]:'',
                'indexfrom' => !empty($request->input('indexfrom')[$i])?$request->input('indexfrom')[$i]:'',
                'indexto' => !empty($request->input('indexto')[$i])?$request->input('indexto')[$i]:'',
                'cl_id'   => !empty($request->input('cl_id')[$i])?$request->input('cl_id')[$i]:0,
                'description' => json_encode($title),
                'ordering' => !empty($request->input('ordering')[$i])?$request->input('ordering')[$i]:0,
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

        $delete = $this->model->where($this->fprimarykey, (int)$editid)->update(['trash'=>'yes']); 
        if(!$delete)
        {
           
            return [
                    'act' => $delete,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid'),
                                    'id' => $editid
                                ]
                ]; 


        }
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

        if(!$restore)
        {
           
            return [
                    'act' => $restore,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid'),
                                    'id' => $editid
                                ]
                ]; 


        }
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

        if(!$destroy)
        {
           
            return [
                    'act' => $destroy,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid'),
                                    'id' => $editid
                                ]
                ]; 


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

        $replicate = $this->model->where($this->fprimarykey, (int)$editid)->get();

        if($replicate->isEmpty()){
           return [
                    'act' => '',
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid')
                                    
                                ]
                ]; 
        }
        $replicate = $replicate->toArray()[0];
        #extract title#
        $title = $replicate['title'];
        
        $newtitle=copyTitle($title,'',$this->model,"title");
        
        $newid = $this->model->max($this->fprimarykey)+1;
        $replicate[$this->fprimarykey] = $newid;
        $replicate['title']= $newtitle;
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


    public function ajaxreturn(Request $request){

        // $default=$this->default();
        // $cat_tree = $default['cat_tree'];
        // $catlist= CategoryCheckboxTree($cat_tree,"","c_id[]",[]);

        $paymentmethod=$this->model->orderBy($this->fprimarykey, 'desc')->pluck('title', 'location_id');
        $paymentmethod = json_decode(json_encode($paymentmethod), true);

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
                    'container' => '#location_id',
                    'data' => $paymentmethod,
                    'close' => true,
                    'message' => '',
                    'success' => $success,
                    'errors' => $errors
                ];

        return json_encode($return);
    }/*../function..*/
  
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