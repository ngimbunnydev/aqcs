<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;

use App\Models\Backend\Systracking;


class SystrackingController extends Controller
{
    private $args;
    private $model;
    private $tablename;
    private $fprimarykey='track_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'systracking','title'=>'System Tracking','routing'=>'admin.controller','icon'=>'<i class="fa fa-folder orange" aria-hidden="true"></i>'];

    private $protectme;


    public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb221');
        $this->protectme = [  
          config('ccms.protectact.index'),
        ];

        $this->args = $args;
        $this->model = new Systracking;
        $this->tablename = $this->model->Gettable();
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
           'jsmessage' => [ 'df_confirm' => __('ccms.df_confirm') ],
        ];
        return [];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model
                    ->selectRaw("
                      $this->fprimarykey as id, name as username, ipaddress as ip, 
                      obj_asscess as track_obj, obj_id, action, track_date
                    ")
                    ->join('users', 'users.id', "$this->tablename.userid");
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array($this->fprimarykey,'name', 'track_date'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);
       
        //dd($results->toSql());
        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->input('title')) 
        {
            $qry=$request->input('title');
            //$results = $results->whereRaw("lower(name) like '%".strtolower($qry)."%'");
            $results = $results->where(function ($query) use($qry) {
                $query->whereRaw("lower(name) like '%".strtolower($qry)."%'")
                      ->orWhereRaw("lower(concat(latinname, SPACE(1), nativename)) like '%".strtolower($qry)."%'");
            });
            
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }
      
        $date_cond='1=1';
        $date_validate = date_validate_query("DATE_FORMAT($this->tablename.track_date, '%Y-%m-%d')");
        if(!empty($date_validate['query'])){
          array_push($querystr, $date_validate['request_query_string']);
          $appends = array_merge($appends, $date_validate['appends']);
          $date_cond = $date_validate['query'];
        }
        
        $results = $results->whereRaw($date_cond);
        //dd($results->toSql());
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
        
        #DEFIND MODEL#
        $results = $this->listingmodel();

        if(empty($condition))
        {
            $results = $results->where('trash', '!=', 'yes');
        }
        
           
        $sfp = $this->sfp($request, $results);
  
        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with($setting);


    } /*../function..*/  
}