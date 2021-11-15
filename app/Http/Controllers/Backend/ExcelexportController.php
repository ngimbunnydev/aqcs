<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ExcelexportController extends Controller
{
    private $args;
	  private $model;
    private $fprimarykey='cm_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'excelexport','title'=>'Excel Export','routing'=>'admin.controller','icon'=>'<i class="fa fa-bookmark" aria-hidden="true"></i>'];

    private $protectme;

    private $tablename;


	public function __construct(array $args){ //public function __construct(Array args){
    //$this->obj_info['title'] = __('label.lb185');
    
    $this->protectme = [  
      ['index', 'index','Update'],
      ['update', 'index','Update'],
                          
    ];

        $this->args = $args;
		//$this->model = new Customer;
        $this->dflang = config('ccms.multilang')[0];
        //$this->tablename = $this->model->Gettable();
        //dd($args);

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
        return [];
    } /*../function..*/

    public function listingModel()
    {
        $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}
        return $this->model->where($this->tablename.'.branch_id', $branchcondition , $this->args['userinfo']['branch_id']??0);
    } /*../function..*/



    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('cm_code'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            $results = $results->where('latinname', 'like', '%'.$qry.'%')
              ->orWhereRaw("cm_code like '%".$qry."%'")
              ->orWhere($this->fprimarykey, '=', (int)$qry);
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }

        if ($request->input('ct_id')) 
        {
            $qry=$request->input('ct_id');
            $results = $results->whereRaw("FIND_IN_SET('$qry',ct_id)");
            array_push($querystr, 'ct_id='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }
      
        if ($request->input('lc_id')) 
        {
            $qry=$request->input('lc_id');
            $results = $results->whereRaw("location_id=".(int)$qry);
            array_push($querystr, 'lc_id='.$qry);
            $appends = array_merge ($appends,['lc_id'=>$qry]);
        }
        
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
        if($perpage<0){
          $perpage = $results->count();
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
      $results = $this->listingmodel()->with('customerType');
      $sfp = $this->sfp($request, $results);

      $filename =$obj_info['title'].".xls";
      $blade = get_view_by_db_name($this->obj_info['name'], 'download');
      return view($blade)
              ->with(['act' => 'index'])
              ->with(['obj_info' => $obj_info])
              ->with($sfp)
              ->with($this->default())
              ->with(['caption' => $filename])
              ->with($setting);
    } /*../function..*/ 
}