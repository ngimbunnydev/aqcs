<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
use Image;
#use Illuminate\Validation\Rule;

use App\Models\Backend\Airqualitymonitoring;
use App\Models\Backend\Airqualitydetail;
use App\Models\Backend\Device;
use App\Models\Backend\Location;
use App\Models\Backend\Airtype;
use App\Models\Backend\Branch;
use App\Models\Backend\Reportdatetimebybranch;




class ReportbranchController extends Controller
{
    private $args;
    private $model;
	private $modelbyminute;
    private $modelbyhour;
    private $modelbyday;
    private $tablename;
    private $fprimarykey='aqm_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page#
    private $obj_info=['name'=>'reportbranch','title'=>'Province/Country','routing'=>'admin.controller','icon'=>'<i class="fa fa-clipboard-list" aria-hidden="true"></i>'];
    
    private $protectme;

	public function __construct(array $args){ //public function __construct(Array args){    
    $this->obj_info['title'] = 'Province/Country';//__('label.livedata');
    $this->args = $args;
    $this->model = new Reportdatetimebybranch;
	
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
        ->select(\DB::raw("airtype_id, code, standard_qty, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
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

        $branch = Branch::
                where('trash', '!=', 'yes')->select(\DB::raw("branch_id, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title"
                                                ))->pluck('title', 'branch_id')->toArray();


        

        return ['js_config'=>$js_config, 
        'airtype' => $airtype, 
        'device' => $device, 
        'location' => $location,
        'device_first' => $device_first,
        'branch' => $branch,
        ];

        
    } /*../function..*/

    public function listingModel()
    {
        $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}

        $tablename = 'airqty_bybranch';
        #DEFIND MODEL#
        return $this->model
        ->select(\DB::raw($tablename.".branch_id, airtype_id, round(avg(qty),2) as qty"))
        ->groupBy('branch_id');
        
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $tablename = 'airqty_bybranch';
        $results = $results->orderby('record_datetime', 'desc');
        $sort = 'title';
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        //$results = $results->orderby($sort, $order);

        $default=$this->default();
        $device_first = $default['device_first'];
        $airtype = $default['airtype'];
        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

         
        
        if ($request->input('airtype') && !empty($request->input('airtype'))) 
        {
            $airtype_id=$request->input('airtype');
            array_push($querystr, 'airtype='.$airtype_id);
            $appends = array_merge ($appends,['airtype'=>$airtype_id]);
            $results = $results->where($tablename.'.airtype_id', $airtype_id); 
        }
        else{
            $first_value = reset($airtype);
            $airtype_id=$first_value['airtype_id'];
            $results = $results->where($tablename.'.airtype_id', $airtype_id); 
        }

        $date_cond="DATE_FORMAT(record_datetime,'%Y-%m-%d')= CURDATE()";
        $datefield = "DATE_FORMAT(record_datetime,'%Y-%m-%d')";

        if ($request->has('fromdate') && !empty($request->input('fromdate')))
        {
            $qry=$request->input('fromdate');
            $fromdate=date("Y-m-d", strtotime($qry));
            $date_cond="$datefield='".$fromdate."'";
            
            array_push($querystr, 'fromdate='.$qry);
            $appends = array_merge ($appends,['fromdate'=>$qry]);
        }
        if ($request->has('todate') && !empty($request->input('todate')))
        {
            $qry=$request->input('todate');
            $todate=date("Y-m-d", strtotime($qry));
            $date_cond="$datefield='".$todate."'";

            array_push($querystr, 'todate='.$qry);
            $appends = array_merge ($appends,['todate'=>$qry]);
        }
        if($request->has('fromdate') && $request->has('todate') && !empty($request->input('fromdate')) && !empty($request->input('todate')))
        {
            $fromdate=$request->input('fromdate');
            $fromdate=date("Y-m-d", strtotime($fromdate));

            $todate=$request->input('todate');

            $todate=date("Y-m-d", strtotime($todate));

            $date_cond="($datefield between '$fromdate' and '$todate')";
        }
        
        $results = $results->whereRaw($date_cond);
        //dd($results->get());
        #no need to send default sort and order to Blade#
        // if($sort==$this->fprimarykey && $order=='desc')
        // {
        //     $sort = '';
        //     $order = '';
        // }
        

        $data = [];
        if(!$results->get()->isEmpty()){
            $data = $results->get()->keyby('branch_id')->toArray();
        }

        return [
            'results'           => $data,
            'querystr'      => $querystr,
            'airtype_id' => $airtype_id,
        ];

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
        //dd($results);
        return [
                        'results'           => $results,
                        'paginationlinks'    => $pagination->links(),
                        'recordinfo'    => $recordinfo,
                        'sort'          => $sort,
                        'order'         => $order,
                        'querystr'      => $querystr,
                        'perpage_query' => $perpage_query,
                       
                        'airtype_id' => $airtype_id,
                    ];
    } /*../function..*/

	public function index(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $branch = $default['branch'];
        $airtype = $default['airtype'];
        #DEFIND MODEL#
        $results = $this->listingmodel();
        
        $sfp = $this->sfp($request, $results);

    	return view('backend.v'.$this->obj_info['name'].'.index', compact('branch','airtype'))
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('label.report')])
                ->with($setting);

    } /*../function..*/

    public function ptoexcel(Request $request){
        if ($request->isMethod('post')){
            //dd($request->input());
            $type = $request->input('exportType');
            /////////
            $obj_info=$this->obj_info;
            $args = $this->args;
            $default=$this->default();
            $branch = $default['branch'];
            $airtype = $default['airtype'];
            #DEFIND MODEL#
            $results = $this->listingmodel();
            $sfp = $this->sfp($request, $results);
            
            if($type=='pdf'){
                $blade = 'backend.v'.$this->obj_info['name'].'.voucher';
                $previewdata= view($blade, compact('args', 'branch','airtype'))
                ->with(['act' => ''])
                ->with(['obj_info' => $obj_info])
                ->with($sfp);

                //$path = resource_path('views/backend/v'.$this->obj_info['name'].'/topdf.html');
                //\File::put($path,$previewdata);
              
              $data = [
                    'id' => 'aaa'
                  ];
      
              $return = [
                          'callback' => 'savesuccess',
                          'container' => '',
                          'data'  => $data,
                          'message' => '',
                          'success' => ''
                      ];
            
              
              
              return $previewdata;
                
            }
            else{
                $blade = get_view_by_db_name($this->obj_info['name'], 'p2excel');//b2excel
                return view($blade, compact('args', 'branch','airtype'))
                  ->with(['act' => ''])
                  ->with(['obj_info' => $obj_info])
                  ->with($sfp);
            }
            
            

            /////////


        }
    }

    public function generatepdf(Request $request){
        $image = Image::make($request->input('imgBase64'));
        $image->save('public/bar.jpg');
    }



    
}
