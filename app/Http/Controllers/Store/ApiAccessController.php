<?php 
namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App,Session;
use Validator;


use App\Models\Backend\Frontlivemap;
use App\Models\Backend\Location;
use App\Models\Backend\Airtype;
use App\Models\Backend\Benchmark;

class ApiAccessController extends Controller
{
    protected $redirectTo = '/';

    private $request;
    private $obj;
    private $act;
    private $id;
    private $title;

    private $path="App\Http\Controllers\Backend\\";
    private $path_restapi="App\Http\Controllers\Restapi\\";
    private $ajax_paths;//=config('ccms.linktype');//"App\Plugins\\";

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Request $request,$obj='dashboard',$act='index',$id=0, $title='')
    {
        $this->langConfig($request);
        $this->request= $request;
        $this->obj= $obj;
        $this->act= $act;
        $this->id= $id;
        $this->title= $title;
        $this->ajax_paths=config('ccms.ajax_paths');
        /*888*****///$this->pmntor();/********/
        //$this->middleware('guest', ['except' => 'logout']);

        $conf_lang_by_db = 'ccms.'.config('ccms.backend').'_multilang';
        
        if(null!==config($conf_lang_by_db)){
          config(['ccms.multilang' => config($conf_lang_by_db)]);
        }
      
        if(!empty(config('currencyinfo.symbol')))
           config(['ccms.discounttype.-1' => config('currencyinfo.symbol')]);
        

    }

    public function index(Request $request){
      $dflang = config('ccms.multilang')[0];
      $airtype = Airtype::where('trash', '!=', 'yes')
        ->select(\DB::raw("airtype_id, code, standard_qty, JSON_UNQUOTE(title->'$.".$dflang[0]."') as title, unit, color, noted"
                                                ))->get()->keyBy('airtype_id')->toArray();

      $benchmark = Benchmark::
                                                leftJoin('aqcs_airtype','aqcs_benchmark.airtype_id', '=', 'aqcs_airtype.airtype_id')
                                                ->select(\DB::raw("benchmark_id AS id, evaluation_id,rangfrom,rangto,indexfrom,indexto, cl_id,
                                                    JSON_UNQUOTE(aqcs_airtype.title->'$.".$dflang[0]."') AS airtype,
                                                    JSON_UNQUOTE(aqcs_benchmark.description->'$.".$dflang[0]."') AS description"
                                                                                            )
                                                );
                                
      $index_rang =  Benchmark::
                                        select(\DB::raw("airtype_id, min(rangto) as minrangto, max(rangto) as maxrangto, min(indexto) as minindexto, max(indexto) as maxindexto"))
                                        ->groupby('airtype_id')->get()->keyby('airtype_id')->toArray();

      
      $model = new Frontlivemap;
      $results =  $model
        ->join('aqcs_location','airqty_livemap.location_id', '=', 'aqcs_location.location_id')
        //->join('aqcs_airtype','airqty_livemap.airtype_id', '=', 'aqcs_airtype.airtype_id')
        ->select(\DB::raw("airqty_livemap.location_id, JSON_UNQUOTE(aqcs_location.title->'$.en') as title, aqcs_location.code as code,  latlong, max(record_datetime) as record_datetime,  GROUP_CONCAT(airtype_id) as airtype, GROUP_CONCAT(qty) as qty"))
        ->groupBy('airqty_livemap.location_id');

        //dd($results->get()->toArray());                                       
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
                    "title" => $record->title,
                    "code" => $record->code,
                    "latlong" => $record->latlong,
                    'record_datetime' => $record->record_datetime,
                    'airtype_id' => $max_aritype,
                    'result' => $cal_result[$max_aritype]??[]
                ];
            }
        }


      return response()->json([
        'status' => true,
        'livemap' => $data,
        'airtype' => $airtype,
      ]);
    }

    public function langConfig($request){
        
        if ($request->exists('lang'))
        {
                
               $lang = $request->query('lang'); 
                
                $backendlang = array_keys(config('ccms.bankendlang'));
               if (! in_array($lang,$backendlang))//['en','kh']
                {
                    $lang = 'en';
                }
                //$request->session()->put('lang', $lang);
                //$lang = $request->session()->get('lang', 'en');
                session(['lang' => $lang]);
        }
        elseif(null==session('lang'))
        {
            session(['lang' => 'en']);
        }
        $lang = session('lang');
        App::setLocale($lang);
    }

   


}