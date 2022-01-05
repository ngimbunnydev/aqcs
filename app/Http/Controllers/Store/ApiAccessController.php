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

    public function index(){
      $model = new Frontlivemap;
      $livemap =  $model
        ->join('aqcs_location','airqty_livemap.location_id', '=', 'aqcs_location.location_id')
        ->select(\DB::raw("airqty_livemap.location_id, latlong, airtype_id,GROUP_CONCAT(record_datetime) as record_datetime, GROUP_CONCAT(qty) as group_qty, round(avg(qty),2) AS qty"))
        ->groupBy('airqty_livemap.location_id')
        ->groupBy('airtype_id');
      dd($livemap->get()->toArray());
      return response()->json([
        'status' => true,
        'message' => 'hello',
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