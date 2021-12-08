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




class LivedataController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='aqm_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'livedata','title'=>'Live Data','routing'=>'admin.controller','icon'=>'<i class="fa fa-clipboard-list" aria-hidden="true"></i>'];
    
    private $protectme;

	public function __construct(array $args){ //public function __construct(Array args){    
    $this->obj_info['title'] = __('label.livedata');
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
        
      $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}

        $js_config = [
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $airtype = Airtype::where('trash', '!=', 'yes')
        ->select(\DB::raw("airtype_id, code, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->get()->keyBy('airtype_id')->toArray();
      
        $device_qry = Device::
        leftjoin('aqcs_location','aqcs_device.location_id', '=', 'aqcs_location.location_id')
        ->where('branch_id', $branchcondition , $this->args['userinfo']['branch_id']??0)
        ->where('aqcs_device.trash', '!=', 'yes')
        ->select(\DB::raw("aqcs_location.location_id as location_id,device_id, aqcs_device.code as code, JSON_UNQUOTE(aqcs_device.title->'$.".$this->dflang[0]."') as title"
    ));
        $device = $device_qry->get()->toArray();
        $device_first = $device_qry->first()?$device_qry->first()->toArray():[];

        $location = Location::
        where('trash', '!=', 'yes')
        ->where('branch_id', $branchcondition , $this->args['userinfo']['branch_id']??0)
        ->select(\DB::raw("location_id, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->pluck('title', 'location_id')->toArray();


        
        

        return ['js_config'=>$js_config, 
        'airtype' => $airtype, 
        'device' => $device, 
        'location' => $location,
        'device_first' => $device_first,
        ];

        
    } /*../function..*/

    public function listingModel()
    {
        $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}

      
        #DEFIND MODEL#
        return $this->model
        ->join('aqcs_airqmdetail','aqcs_airqm.aqm_id', '=', 'aqcs_airqmdetail.aqm_id')
        ->join('aqcs_airtype','aqcs_airqmdetail.airtype_id', '=', 'aqcs_airtype.airtype_id')
        ->select(\DB::raw("aqcs_airqm.aqm_id AS id,  air_qty, standard_qty, record_datetime,
        JSON_UNQUOTE(aqcs_airtype.title->'$.".$this->dflang[0]."') AS title"
    ));
        //->where('aqcs_airqm.aqm_id', $last_quality['aqm_id']);
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $results = $results->orderby('record_datetime', 'desc');

        $sort = 'title';
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);
        $default=$this->default();
        $device_first = $default['device_first'];
        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        $first_deviceid = 0;
        if(!empty($device_first)){
            $first_deviceid = $device_first['device_id'];
        }
        $last_quality = null;

        if ($request->input('device') && !empty($request->input('device'))) 
        {
            
            $qry=$request->input('device');
            $last_bydevice = $this->model
            ->select('aqm_id','device_id')
            ->where('device_id', (int)$qry)
            ->orderby('record_datetime', 'desc')
            ->first();
            array_push($querystr, 'device='.$qry);
            $appends = array_merge ($appends,['device'=>$qry]);
        }
        else{
            $last_quality = $this->model
            ->select('aqm_id','device_id')
            ->where('device_id', (int)$first_deviceid)
            ->orderby('record_datetime', 'desc')
            ->first();
        }

        if(isset($last_bydevice) && $last_bydevice){
            $last_quality = $last_bydevice;
        }

        $aqm_id = 0;
        $device_info = [];
        if($last_quality!=null || $last_quality){
            $last_quality = $last_quality->toArray();
            $aqm_id = $last_quality['aqm_id'];
            $device_id  = $last_quality['device_id'];
            $device_info = Device::
            leftjoin('aqcs_location','aqcs_device.location_id', '=', 'aqcs_location.location_id')
            ->where('aqcs_device.device_id', $device_id)
            ->select(\DB::raw("device_index,JSON_UNQUOTE(aqcs_location.title->'$.".$this->dflang[0]."') as location, JSON_UNQUOTE(aqcs_device.title->'$.".$this->dflang[0]."') as device"))
            ->get()->toArray()[0];
            
        }

        $results = $results->where('aqcs_airqm.aqm_id', $aqm_id);
        

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
                        'perpage_query' => $perpage_query,
                        'device_info' => $device_info,
                    ];
    } /*../function..*/

	public function index(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $location = $default['location'];
        $device = $default['device'];
        $device_combo = array_column($device, 'title', 'device_id');
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

    	return view('backend.v'.$this->obj_info['name'].'.index', compact('location', 'device', 'device_combo','airtype'))
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('label.report')])
                ->with($setting);

    } /*../function..*/




    
}