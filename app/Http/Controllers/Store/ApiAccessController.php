<?php 
namespace App\Http\Controllers\Store;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App,Session;
use Validator;


use App\Models\Backend\Frontlivemap;
use App\Models\Backend\Location;
use App\Models\Backend\Airtype;

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
   
      $model = new Frontlivemap;
      $results =  $model
        ->join('aqcs_location','airqty_livemap.location_id', '=', 'aqcs_location.location_id')
        //->join('aqcs_airtype','airqty_livemap.airtype_id', '=', 'aqcs_airtype.airtype_id')
        ->select(\DB::raw("airqty_livemap.location_id, JSON_UNQUOTE(aqcs_location.title->'$.en') as title, aqcs_location.code as code,  latlong, max(record_datetime) as record_datetime,  GROUP_CONCAT(airtype_id) as group_airtype, GROUP_CONCAT(qty) as group_qty"))
        ->groupBy('airqty_livemap.location_id');

        // if ($request->input('airtype') && !empty($request->input('airtype'))) 
        // {
        //     $airtype_id=$request->input('airtype');
        //     $results = $results->where('airtype_id', $airtype_id); 
        // }
        // else{
        //     $first_value = reset($airtype);
        //     $airtype_id=$first_value['airtype_id'];
        //     $results = $results->where('airtype_id', $airtype_id); 
        // }

      $df_airtype = reset($airtype);  
      return response()->json([
        'status' => true,
        'livemap' => $results->get()->toArray(),
        'airtype' => $airtype,
        'df_airtype' => $df_airtype
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