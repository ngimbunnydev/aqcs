<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
#use Illuminate\Validation\Rule;

use App\Models\Backend\Branch;
use App\Models\Backend\Users;
use App\Models\Backend\Stock;



class BranchController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='branch_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'branch','title'=>'Branch','routing'=>'admin.controller','icon'=>'<i class="fa fa-building" aria-hidden="true"></i>'];

    private $protectme;


	public function __construct(array $args){ //public function __construct(Array args){
    $this->obj_info['title'] = __('label.lb13');
        $this->protectme = [  config('ccms.protectact.create'),
                        config('ccms.protectact.duplicate'),
                        config('ccms.protectact.store'),
                        config('ccms.protectact.edit'),
                        config('ccms.protectact.update'),
                        config('ccms.protectact.delete'),
                        config('ccms.protectact.restore'),
                        config('ccms.protectact.destroy'),

                        ];

        $this->args = $args;
		$this->model = new Branch;
        $this->dflang = config('ccms.multilang')[0];

        $obj_info=$this->obj_info;

        $input = $this->model->get(); 
        if($input->isEmpty())
        {
            $this->autofirstsave();

            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing);
        }


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

    public function autofirstsave(){
        $this->model->truncate();
            /*$savedata = $this->model->insert([
                $this->fprimarykey=>1,
                'countryname' => 'United State',
                'currency' => 'USD',
                'symbol' => '$',
                'ratein' => abs((float)1),
                'rateout' => abs((float)1),
                'ordering' => 0,
                'tag' => '',
                'trash' => 'no',
                'blongto' => 1
            ]);*/
            $myRequest = new Request();
            $data=$this->setinfo($myRequest);
            $savedata = $this->model->insert($data['tableData']);
    }

    public function default()
    {
        $js_filemanagersetting=array(   'displaymode' => '2',
                                        'filetype'   =>'image',
                                        'givent_txtbox'=>'pic',
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
        
        return ['js_config'=>$js_config];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title, address, phone, pic"
                                                )
                                        );
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', 'branch_id'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'branch_id'; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            //$results = $results->where('title', 'like', '%'.$qry.'%');
            $results = $results->whereRaw("lower(JSON_UNQUOTE(title->'$.*')) like '%".strtolower($qry)."%'");
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
                ->with(['act' => 'index'])
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
        


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config'
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
            $count_existing = $this->model->count();
            $allow_num = (int)config('sysconfig.branchnum');
            if(empty($allow_num) || $allow_num==null){
              $allow_num= 1;
            }
            elseif($allow_num==-1){
              $allow_num= $count_existing+1;
            }
          
            if ($validator->fails()) {
                //if ($validator->fails() || $count_existing>=$allow_num) {

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                $errors_msg = $validator->errors()->first();
                if($count_existing>=$allow_num){
                  $errors_msg = "You subcribe for ". $allow_num. " ".$obj_info['title']."  only, Cannot Add More..!";
                }
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $errors_msg,
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

        if((int)$input['branch_id']!=$this->args['userinfo']['branch_id'])
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.nbltbranch'));
        }

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

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            //'parents',
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

            for($i=0; $i<$numrecord; $i++)
            {
               

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
        $numrecord=1;
        if ($request->has($this->fprimarykey))
        {
            $numrecord=count($request->input($this->fprimarykey));
        }

        for($i=0; $i<$numrecord; $i++)
        {
            foreach (config('ccms.multilang') as $lang)
            {
                $title[$lang[0]]=!empty($request->input('title-'.$lang[0])[$i])?$request->input('title-'.$lang[0])[$i]:'General Branch';

            } #./foreach#

            $record = [
            
                $this->fprimarykey => $newid+$i,
                'title' =>json_encode($title),
                'address' => !empty($request->input('address')[$i])?$request->input('address')[$i]:'',
                'phone' => !empty($request->input('phone')[$i])?$request->input('phone')[$i]:'',
                'pic' => !empty($request->input('pic')[$i])?$request->input('pic')[$i]:'',
                'ordering' => 0,
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

        $delete = $this->model->where($this->fprimarykey, (int)$editid)->where('branch_id', $this->args['userinfo']['branch_id'])->update(['trash'=>'yes']); 
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

        $restore = $this->model->where($this->fprimarykey, (int)$editid)->where('branch_id', $this->args['userinfo']['branch_id'])->update(['trash'=>'no']); 

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

        $atstock = Stock::where('wh_id',$editid)->get()->toArray();
        if(count($atstock)>0)
        {
           return [
                    'act' => '',
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid'),
                                    'id' => $editid
                                ]
                ];  
        }

        $destroy = $this->model->where($this->fprimarykey, (int)$editid)->where('branch_id', $this->args['userinfo']['branch_id'])->delete(); 

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

        $updatebranch = Users::where('id',$this->args['userinfo']['id'])
                                            ->update(['branch_id'=>0]);
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

        $replicate = $this->model->where($this->fprimarykey, (int)$editid)->where('branch_id', $this->args['userinfo']['branch_id'])->get();

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

    public function change(Request $request){
        $obj_info=$this->obj_info;
        $routing = redirect()->back()->getTargetUrl();
        if($this->args['userinfo']['level_id']!=1)
        {
           
            return [
                                        'act' => '',
                                        'url' => $routing,
                                        'passdata' => []
                                    ]; 
        }


        $editid = $this->args['routeinfo']['id'];
        $updatebranch = Users::where('id',$this->args['userinfo']['id'])
                                            ->update(['branch_id'=>$editid]);
        
        /*$updatewh = Users::where('id',$this->args['userinfo']['id'])
                                            ->update(['wh_id'=>0]);*/
        

        return [
                                        'act' => '',
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => __('ccms.suc_switch')
                                                    ]
                                    ];

    }/*../function..*/


    public function ajaxreturn(Request $request){

        // $default=$this->default();
        // $cat_tree = $default['cat_tree'];
        // $catlist= CategoryCheckboxTree($cat_tree,"","c_id[]",[]);

        $branch=$this->listingModel()->orderBy($this->fprimarykey, 'desc')->pluck('title', 'id');
        $branch = json_decode(json_encode($branch), true);
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
                    'container' => '#branch_id',
                    'data' => $branch,
                    'close' => true,
                    'message' => '',
                    'success' => $success,
                    'errors' => $errors
                ];

        return json_encode($return);
    }/*../function..*/



   

    
}