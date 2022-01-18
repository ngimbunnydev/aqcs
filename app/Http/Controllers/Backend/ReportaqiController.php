<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
use Image;
#use Illuminate\Validation\Rule;


use App\Models\Backend\Device;
use App\Models\Backend\Location;
use App\Models\Backend\Airtype;
use App\Models\Backend\Benchmark;
use App\Models\Backend\Color;
use App\Models\Backend\Evaluation;
use App\Models\Backend\Branch;
use App\Models\Backend\Frontlivemap;




class ReportaqiController extends Controller
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
    private $obj_info=['name'=>'reportaqi','title'=>'AQI Report','routing'=>'admin.controller','icon'=>'<i class="fa fa-clipboard-list" aria-hidden="true"></i>'];
    
    private $protectme;

	public function __construct(array $args){ //public function __construct(Array args){    
        $this->protectme = [  
            config('ccms.protectact.index'),

            ];
    $this->obj_info['title'] = 'AQI Report';//__('label.livedata');
    $this->args = $args;
    $this->model = new Frontlivemap;
	
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
        ->select(\DB::raw("airtype_id, code, standard_qty, JSON_UNQUOTE(title->'$.".$this->dflang[0]."') as title, unit, color, noted"
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

        #DEFIND MODEL#
        return $this->model
        ->select(\DB::raw("airqty_livemap.location_id, max(record_datetime) as record_datetime, GROUP_CONCAT(airtype_id) as airtype, GROUP_CONCAT(qty) AS `qty`"))
        ->groupBy('location_id');
        
    } /*../function..*/

    public function sfp($request, $results)
    {
        $default=$this->default();
        $airtype = $default['airtype'];
        $benchmark = Benchmark::
                leftJoin('aqcs_airtype','aqcs_benchmark.airtype_id', '=', 'aqcs_airtype.airtype_id')
                ->select(\DB::raw("benchmark_id AS id, evaluation_id,rangfrom,rangto,indexfrom,indexto, cl_id,
                    JSON_UNQUOTE(aqcs_airtype.title->'$.".$this->dflang[0]."') AS airtype,
                    JSON_UNQUOTE(aqcs_benchmark.description->'$.".$this->dflang[0]."') AS description"
                                                            )
                );

        $index_rang =  Benchmark::
        select(\DB::raw("airtype_id, min(rangto) as minrangto, max(rangto) as maxrangto, min(indexto) as minindexto, max(indexto) as maxindexto"))
        ->groupby('airtype_id')->get()->keyby('airtype_id')->toArray();
        
        $data = [];
        if(!$results->get()->isEmpty()){
            $results = $results->get();
            foreach($results as $ind => $record){
                
                $id = explode(',', $record->airtype);
				$val = explode(',', $record->qty);
                $airtype_qty = array_combine($id, $val);
                
                //work with aqi function for evaluate new qty
                $cal_result = [];
                foreach($airtype_qty as $id => $qty){

                    $newqty = 0;
                    $cacl_aqi = [];
                    if(isset($index_rang[$id])){
                        $avg_qty = $qty;
                        $clow   = $index_rang[$id]['minrangto'];
                        $chight = $index_rang[$id]['maxrangto'];
                        $ilow   = $index_rang[$id]['minindexto'];
                        $ihight = $index_rang[$id]['maxindexto'];
                        $cacl_aqi = cacl_aqi($avg_qty,$clow,$chight,$ilow,$ihight);
                        $newqty = $cacl_aqi['qty'];
                        
                    }
                    $cal_result[$id] = $cacl_aqi;
                    $airtype_qty[$id] = $newqty;
                }
                ///
				
                $max_qty = max(array_values($airtype_qty));
                $max_aritype = array_search($max_qty, $airtype_qty);

                /*
                $benchmark_detail = clone $benchmark;
                $benchmark_detail = $benchmark_detail->where('aqcs_benchmark.airtype_id', $max_aritype)->get();

                if($benchmark_detail->isEmpty()){
                        $benchmark_detail = $benchmark->where('aqcs_benchmark.airtype_id', 1)->get();
                }
                */

          
                $data[] = [
                    'location_id' => $record->location_id,
                    'record_datetime' => $record->record_datetime,
                    'airtype_id' => $max_aritype,
                    'result' => $cal_result[$max_aritype]??[]
                ];
            }
        }
        return [
            'results'           => $data,
        ];

    } /*../function..*/

	public function index(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $branch = $default['branch'];
        $airtype = $default['airtype'];
        $location = $default['location'];
        #DEFIND MODEL#
        $results = $this->listingmodel();
        
        $sfp = $this->sfp($request, $results);

    	return view('backend.v'.$this->obj_info['name'].'.index', compact('branch','airtype', 'location'))
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('label.report')])
                ->with($setting);

    } /*../function..*/

    public function ptoexcel(Request $request){
        if ($request->isMethod('post')){
            //dd($request->input());
            
            /////////
            $obj_info=$this->obj_info;
            $args = $this->args;
            $default=$this->default();
            $branch = $default['branch'];
            $airtype = $default['airtype'];
            $location = $default['location'];
            #DEFIND MODEL#
            $results = $this->listingmodel();
            $sfp = $this->sfp($request, $results);
            
            $blade = 'backend.v'.$this->obj_info['name'].'.voucher';
                $previewdata= view($blade, compact('args', 'branch','airtype','location'))
                ->with(['act' => ''])
                ->with(['obj_info' => $obj_info])
                ->with($sfp);              
              
              return $previewdata;
            
            

            /////////


        }
    }

    public function generatepdf(Request $request){
        $image = Image::make($request->input('imgBase64'));
        $image->save('public/bar.jpg');
    }



    
}
