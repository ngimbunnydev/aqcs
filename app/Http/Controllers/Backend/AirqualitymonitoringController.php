<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
#use Illuminate\Validation\Rule;

use App\Models\Backend\Airqualitymonitoring;
use App\Models\Backend\Airqualitydetail;
use App\Models\Backend\Device;
use App\Models\Backend\Location;
use App\Models\Backend\Airtype;




class AirqualitymonitoringController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='aqm_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'airqualitymonitoring','title'=>'Air Quality','routing'=>'admin.controller','icon'=>'<i class="fa fa-clipboard-list" aria-hidden="true"></i>'];
    
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
    
    $this->obj_info['title'] = __('label.lb19');
        $this->args = $args;
		$this->model = new Airqualitymonitoring;
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

        $airtype = Airtype::where('trash', '!=', 'yes')
        ->select(\DB::raw("airtype_id, code, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->get()->keyBy('airtype_id')->toArray();
      
        $device_qry = Device::where('trash', '!=', 'yes')
        ->select(\DB::raw("device_id, code, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
    ));
        $device = $device_qry->pluck('title', 'device_id')->toArray();
        $devicewithcode = $device_qry->get()->keyBy('device_id')->toArray();
        $location = Location::where('trash', '!=', 'yes')->select(\DB::raw("location_id, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->pluck('title', 'location_id')->toArray();
        
        return ['js_config'=>$js_config, 
        'airtype' => $airtype, 'device' => $device, 
        'location' => $location,
        'devicewithcode' => $devicewithcode
        ];
    } /*../function..*/

    public function listingModel()
    {
        $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}
      
        #DEFIND MODEL#
        return $this->model
        ->join('aqcs_device','aqcs_airqm.device_id', '=', 'aqcs_device.device_id')
        ->leftJoin('aqcs_location','aqcs_device.location_id', '=', 'aqcs_location.location_id')
        ->select(\DB::raw(   $this->fprimarykey." AS id, record_datetime,
        JSON_UNQUOTE(aqcs_location.title->'$.".$this->dflang[0]."') AS location,
        JSON_UNQUOTE(aqcs_device.title->'$.".$this->dflang[0]."') AS title"
                                                )
        )
          ->where('branch_id', $branchcondition , $this->args['userinfo']['branch_id']??0);
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('record_datetime', 'aqm_id'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'aqm_id'; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            $results = $results
            ->where('aqcs_device.title', 'like', '%'.$qry.'%')
            ->whereOr('aqcs_device.code', 'like', '%'.$qry.'%')
            ->whereOr('aqcs_device.model', 'like', '%'.$qry.'%')
            ->whereOr('aqcs_device.device_index', 'like', '%'.$qry.'%');
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }

        if ($request->input('status') && !empty($request->input('status'))) 
        {
            $qry=$request->input('status');
           
            $results = $results->where("aqcs_device.status",$qry);
            array_push($querystr, 'status='.$qry);
            $appends = array_merge ($appends,['status'=>$qry]);
        }

        if ($request->input('location_id') && !empty($request->input('location_id'))) 
        {
            
            $qry=$request->input('location_id');
           
            $results = $results->WhereIn("aqcs_device.location_id",$qry);
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
        $location = $default['location'];
        $airtype = $default['airtype'];
        #DEFIND MODEL#
        $results = $this->listingmodel();
        if(empty($condition))
        {
            $results = $results->where('aqcs_airqm.trash', '!=', 'yes');
        }
        else
        {
            //
        }

        $sfp = $this->sfp($request, $results);


    	return view('backend.v'.$this->obj_info['name'].'.index', compact('location','airtype'))
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
        $location = $default['location'];
        $airtype = $default['airtype'];
        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('aqcs_airqm.trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index', compact('location','airtype'))
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
        $location = $default['location'];
        $airtype = $default['airtype'];
        $device = $default['device'];

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'airtype',
                            'device',
                            'location'
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
                    $savesub = Airqualitydetail::insert($data['subtableData']);
                      
                      if(!$savesub)
                      {
                          $savedata= false;
                          $destroyinv = $this->model->where($this->fprimarykey, (int)$data['id'])->delete(); 
 
                          $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                          return [
                              'act' => false,
                              'url' => $routing,
                              'passdata' => [
                                              'errors' => __('ccms.rqnvalid'),
                                              'input' => $request->input(),
                                              'products' => $prodctsarray,
                                              'submitto' => 'create'
                                          ]
                          ];
                        
                        
                        
                        
                     }
                    
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
                $tableData = array_except($data['tableData'], [$this->fprimarykey, 'trash', 'add_date', 'blongto']);
                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($tableData);
                
                
                $subtable = $data['subtableData'];
                foreach($subtable as $id => $record){
                    $update_sub = Airqualitydetail::
                    where('aqmd_id',$record['aqmd_id'])
                    ->where('airtype_id', $record['airtype_id'])
                                            ->update(['air_qty' => $record['air_qty']]);
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
        $location = $default['location'];
        $airtype = $default['airtype'];
        $device = $default['device'];
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
        $x = [];
        foreach ($input as $key => $value) {
            $x[$key]= $value;
        }

        $input=$x;
        $input['record_datetime'] = date("d-m-Y H:i:s", strtotime($input['record_datetime'])); 

        //sub
        $input_sub = Airqualitydetail::where($this->fprimarykey, (int)$editid)->get();
        $subtable = [];
        if(!$input_sub->isEmpty()){
            $input_sub = $input_sub->toArray();
            foreach($input_sub as $record){
                $subtable['aqmd_id_'.$record['airtype_id']] = $record['aqmd_id'];
                $subtable['airtype_'.$record['airtype_id']] = $record['air_qty'];
            }
        }
        $input = array_merge($input, $subtable);
        skip:

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'location',
                            'device',
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
     
            $rules['device_id'] = "required|numeric|gt:0";
            $rules['record_datetime'] = "required|date_format:Y-m-d H:i";
            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $numrecord=count($request->input('subairqty'));
            $countsubform['countsubform'] = $numrecord;
            $rules['countsubform'] = 'required|numeric|gt:0';
            for($i=0; $i<$numrecord; $i++)
            {
               $rules['subairqty.'.$i]       = 'required';
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
            $validator =Validator::make(array_merge($request->input(), $countsubform), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){
        $newid=($isupdate)? $request->input($this->fprimarykey) : $this->model->max($this->fprimarykey)+1;
        $default=$this->default();
        $airtype = $default['airtype'];
        $devicewithcode = $default['devicewithcode'];
        $subtableData = [];
        $paramenters = [];
        $i=0;
        foreach($airtype as $id => $record){
            $aqmd_id = !empty($request->input('aqmd_id_'.$id))?$request->input('aqmd_id_'.$id):'';
            $airtype_id = $id??0;
            $aritype_code = $record['code']??'';
            $air_qty = $request->input('airtype_'.$id) ?? 0;
                $subrecord = [
                    'aqmd_id' => $aqmd_id,
                    $this->fprimarykey => $newid,
                    'airtype_id' => $airtype_id,
                    'airtype_code' => $aritype_code,
                    'air_qty' => $air_qty,
                    'display' => 1,
                    'ordering' => 0,
                    'tag' => '',
    
                ];
                $i++;
                $paramenters[$aritype_code] = $air_qty;
                array_push($subtableData, $subrecord);
        }
        $device_id = !empty($request->input('device_id'))?$request->input('device_id'):0;
        $device_code = $devicewithcode[$device_id]['code']??0;
        $record_datetime = !empty($request->input('record_datetime'))?date("Y-m-d H:i:s", strtotime($request->input('record_datetime'))):date("Y-m-d H:i:s");
        $tableData = [
            
            $this->fprimarykey => $newid,
            'device_id' => $device_id,
            'device_code' => $device_code,
            'tempreture' => '',
            'humidity' => '',
            'status' => 'yes',
            'record_datetime' => $record_datetime,
            'ordering' => 0,
            'paramenters' => json_encode($paramenters),
            'tag' => '',
            'trash' => 'no',
            'add_date' => date('Y-m-d'),
            'blongto' => $this->args['userinfo']['id'],

        ];

        return ['tableData' => $tableData, 'subtableData' => $subtableData, 'id'=>$newid];

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

    /*============================= Start Import Stuff*/
    public function airimport(Request $request)
    {
        $obj_info=$this->obj_info;

        $default=$this->default();
        $js_config = $default['js_config'];
        
        return view('backend.v'.$this->obj_info['name'].'.import',
                    compact('obj_info',
                            'js_config'
                            )


                )->with(
                    [
                        'submitto'  => 'storeimport',
                        'fprimarykey'     => $this->fprimarykey,
                        'caption' => __('ccms.import')
                    ]
                );
    } /*../function..*/

    public function loadimportdata(Request $request){
        if ($request->isMethod('post'))
        {
          $obj_info=$this->obj_info;
          $validator = Validator::make($request->all(), [
              'file_import' => 'required|file|mimes:xls,xlsx,xlsm'
          ]);
          if ($validator->fails()) {
            return response()->json([
                        'act' => false,
                        'obj_info' => $obj_info,
                        'input' => $request->input(),
                        'errors' => $validator->errors()->first(),
                    ]);
          }
          
          $default=$this->default();
          $js_config = $default['js_config'];
          $airtype = $default['airtype'];
          $devices = Device::
          leftJoin('aqcs_location','aqcs_device.location_id', '=', 'aqcs_location.location_id')
          ->select(\DB::raw("device_id, device_index, JSON_UNQUOTE(aqcs_location.title->'$.".$this->dflang[0]."') AS location"
            ))->get()->keyby('device_index')->toArray();

          $args = $this->args;
          $data = $this->importsetinfo($request);
          $request = $data['request'];
          
          $validator = $this->importvalidation($data);
         if ($validator->fails()) {
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'import']);
                return [
                    'act' => false,
                    'url' => $routing,
                    'errors' => $validator->errors()->first(),
                ];
          }
          return view('backend.v'.$this->obj_info['name'].'.importlist', compact('devices'))->with([
            'results'=> $data['spreadsheet'],
            'main_data' => $data['main_data'],
            'args' => $args
          ]); 



        }
      }
      /**/

    public function storeimport(Request $request){
        if ($request->isMethod('post'))
        {
          $obj_info=$this->obj_info;
          $validator = Validator::make($request->all(), [
              'file_import' => 'required|file|mimes:xls,xlsx,xlsm'
          ]);
          if ($validator->fails()) {
            return response()->json([
                        'act' => false,
                        'obj_info' => $obj_info,
                        'input' => $request->input(),
                        'errors' => $validator->errors()->first(),
                    ]);
          }
  
          $data = $this->importsetinfo($request);
          //Try validation here
          $validator = $this->importvalidation($data);
           if ($validator->fails()) {
                  $routing=url_builder($obj_info['routing'],[$obj_info['name'],'import']);
                  return [
                      'act' => false,
                      'url' => $routing,
                      'errors' => $validator->errors()->first(),
                  ];
            }
          $tableData = $data['tableData'];
          $tableDataId = $data['tableDataId'];
          $subtableData = $data['subtableData'];
          $savedata = false;
          if(!empty($tableData) && !empty($subtableData)){
            $savedata = $this->model->insert($tableData);
          }

          
          if($savedata){
            $savesub = Airqualitydetail::insert($subtableData);
            if(!$savesub)
            {
                      $savedata= false;
                      $destroyinv = $this->model->whereIn($this->fprimarykey, $tableDataId)->delete(); 
                      $routing=url_builder($obj_info['routing'],[$obj_info['name'],'import']);
                      return [
                        'act' => false,
                        'url' => $routing,
                        'errors' => __('ccms.rqnvalid'),
                    ];

            }

            $success_ms = __('ccms.suc_save');
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            $return = [
                    'callback' => 'reloadImportData',
                    'status' => $savedata,
                    'container' => '',
                    'success' => $success_ms,
                    'url' => $routing,
                ];
  
            return json_encode($return);
          }
          
          return response()->json([
              'act' => false,
              'obj_info' => $obj_info,
              'input' => $request->input(),
              'errors' => $validator->errors()->first(),
          ]); 
        }
  
      }
    
    /**/

    public function importvalidation($data){
      
        $tableData = $data['tableData'];    
        $count_main = count($tableData);
        $for_main = [];
        for($i=0; $i<$count_main; $i++){
            //date_format:Y-m-d H:i:s
            $rules["main".$i.'.device_id'] = 'required|numeric|gt:0';
            $rules["main".$i.'.record_datetime'] = 'required|date_format:Y-m-d H:i';
            $for_main["main".$i] = $tableData[$i];
        }
        $subtableData = $data['subtableData'];
        $for_rule = [];
        $count_record = count($subtableData);
        for($i=0; $i<$count_record; $i++){
          $rules["rule".$i.'.airtype_id'] = 'required|numeric|gt:0';
          $for_rule["rule".$i] = $subtableData[$i];
        }
        
        $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => 'The :attribute '. __('ccms.fieldreqire'),
                'numeric' => 'The :attribute field must be numeric.',
                'gt' => 'The :attribute is incorrect'
            ];
        
        $validator =Validator::make(array_merge($for_main, $for_rule), $rules, $validatorMessages);
        
        return $validator;
    }
    
    /**/

      public function importsetinfo($request){
        
        #loading and convert data to array from excel file
        /*
        $spreadsheet = IOFactory::load($request->file('file_import'));
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $collectSheet = collect(array_values($sheetData));
        */
        
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('file_import'));
        $reader->setLoadSheetsOnly(["rawdata"]);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file_import'));
        //$spreadsheet->getSheet(0);
        //$highestColumm = $spreadsheet->setActiveSheetIndex(0)->getHighestColumn();
        //$airtype_param = range('D', $highestColumm);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        
        $collectSheet = collect(array_values($sheetData));
      
        //$collectSheet = $collectSheet->forget(0);// remmove first row
        $collectSheet = collect($collectSheet->values()->all()); // refill array key index
        $collectSheet = $collectSheet->where('B', '!=', null);
      
        
        #Main data is using for read AirType Code
        $main_data = $collectSheet->first();
        unset($main_data['A'],$main_data['B'], $main_data['C']);
        $tableData = [];
        $tableDataId= [];
        $subtableData=[];
        $paramenters = [];
        //$add_date=(isset($main_data['C']) && !empty($main_data['C'])) ? date('Y-m-d', strtotime($main_data['C'])) : date('Y-m-d');
        
        //////////////
        $collectSheet = $collectSheet->forget(0);
        if($collectSheet->count() > 0){
            $devices = Device::select(\DB::raw("device_id, device_index"
            ))->get()->keyby('device_index')->toArray();

            $airtypes = Airtype::select(\DB::raw("airtype_id, code"
            ))->get()->keyby('code')->toArray();

            $newid= $this->model->max($this->fprimarykey);

          foreach($collectSheet as $row){
            $newid +=1;
            $device_code = $row['B'] ?? '';
            $device_id = $devices[$device_code]['device_id']??0;
            
            $record_datetime =  !empty($row['C'])?gmdate("Y-m-d H:i", ExcelDateToUnix($row['C'])):'';
            
            /*for sub records*/
            foreach($main_data as $column => $aritype_code){
                $airtype_id = $airtypes[$aritype_code]['airtype_id']??0;
                $subrecord = [
                    'aqmd_id' => 0,
                    $this->fprimarykey => $newid,
                    'airtype_id' => $airtype_id,
                    'airtype_code' => $aritype_code??'',
                    'air_qty' => $row[$column] ?? 0,
                    'display' => 1,
                    'ordering' => 0,
                    'tag' => '',
    
                ];
                $paramenters[$aritype_code??''] = $row[$column] ?? 0;
                array_push($subtableData, $subrecord);
                
            }
            /*end sub*/
            $tableDataId[]=$newid;
            $record = [
                $this->fprimarykey => $newid,
                'device_id' => $device_id,
                'device_code' => $device_code,
                'tempreture' => '',
                'humidity' => '',
                'status' => 'yes',
                'record_datetime' => $record_datetime,
                'ordering' => 0,
                'paramenters' => json_encode($paramenters),
                'tag' => '',
                'trash' => 'no',
                'add_date' => date('Y-m-d'),
                'blongto' => $this->args['userinfo']['id'],

            ];
            
            array_push($tableData, $record);


          }
        }
        return [
            'request' => $request,
            'main_data' => $main_data,
            'tableData' => $tableData,
            'tableDataId' => $tableDataId,
            'subtableData' => $subtableData,
            'spreadsheet' => $collectSheet,
          ];
    }

    public function storeapi(Request $request)
    {
        //dd($request->all());
        //dd(json_decode($request->getContent(), true));
        $apidata = json_decode($request->getContent(), true);
        $tableData = [];
        $tableDataId= [];
        $subtableData=[];
        $paramenters = [];
        if(!empty($apidata)){
            $devices = Device::select(\DB::raw("device_id, device_index"
            ))->get()->keyby('device_index')->toArray();

            $airtypes = Airtype::select(\DB::raw("airtype_id, code"
            ))->get()->keyby('code')->toArray();

            $newid= $this->model->max($this->fprimarykey);
            foreach($apidata as $row){
                
                $device_code = $row['DeviceID'] ?? '';
                $device_id = $devices[$device_code]['device_id']??0;
                //$record_datetime =  !empty($row['DateTime'])?gmdate("Y-m-d H:i", ExcelDateToUnix($row['DateTime'])):'';
                $stringdate = !empty($row['DateTime'])?str_replace(".0Z","", $row['DateTime']):'';
                $record_datetime = !empty($stringdate)?date("Y-m-d H:i:s", strtotime($stringdate)):'';
                if(!empty($device_id) && !empty($record_datetime)){
                    $newid +=1;
                    /*for sub records*/
                        foreach($airtypes as $code => $items){
                            
                            $airtype_id = $items['airtype_id']??0;
                            $subrecord = [
                                'aqmd_id' => 0,
                                $this->fprimarykey => $newid,
                                'airtype_id' => $airtype_id,
                                'airtype_code' => $code??'',
                                'air_qty' => $row[$code] ?? 0,
                                'display' => 1,
                                'ordering' => 0,
                                'tag' => '',
                
                            ];
                            $paramenters[$code??''] = $row[$code] ?? 0;
                            array_push($subtableData, $subrecord);
                            
                        }
                        /*end sub*/
                    
                    $tableDataId[]=$newid;
                    $record = [
                            $this->fprimarykey => $newid,
                            'device_id' => $device_id,
                            'device_code' => $device_code,
                            'tempreture' => '',
                            'humidity' => '',
                            'status' => 'yes',
                            'record_datetime' => $record_datetime,
                            'ordering' => 0,
                            'paramenters' => json_encode($paramenters),
                            'tag' => '',
                            'trash' => 'no',
                            'add_date' => date('Y-m-d'),
                            'blongto' => $this->args['userinfo']['id'],
            
                        ];
                        
                        array_push($tableData, $record);
                }
                
              }

            ////////
        }

        $savedata = false;
          if(!empty($tableData) && !empty($subtableData)){
            $savedata = $this->model->insert($tableData);
          }
       
          if($savedata){
            $savesub = Airqualitydetail::insert($subtableData);
            if(!$savesub)
            {
                      $savedata= false;
                      $destroyinv = $this->model->whereIn($this->fprimarykey, $tableDataId)->delete(); 
                      return [
                        'act' => false,
                        'url' => $routing,
                        'errors' => __('ccms.rqnvalid'),
                    ];

            }

            $success_ms = __('ccms.suc_save');
            $return = [
                    'status' => $savedata,
                    'success' => $success_ms,
                ];
  
            return json_encode($return);
          }
          
          return response()->json([
              'act' => false,
              'errors' => $validator->errors()->first(),
          ]); 

    }

    
}