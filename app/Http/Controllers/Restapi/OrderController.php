<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Backend\Quotation;
use App\Models\Backend\Size;
use App\Models\Backend\Color;

class OrderController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='qt_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'order','title'=>'Order','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;


	public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb10');

        $this->args = $args;
        $this->model = new Quotation();
       $this->tablename = $this->model->Gettable();
        $this->dflang = config('ccms.multilang')[0];
	} /*../function..*/
  
  public function default()
  {
    $sizes=Size::getsize(get_current_request_lan())->pluck('title', 's_id');
    $colors=Color::getcolor(get_current_request_lan())->pluck('title', 'cl_id');

    return ['sizes' =>$sizes, 'colors'=>$colors];
  } /*../function.s.*/
   
	public function index(Request $request, $condition=[], $setting=[]){
    if(!$request->has('cm_id') && empty($request->input('cm_id'))){
      return response()->json([
        'status' => 'error',
        'message' => 'The customer ID is required!',
      ]);  
    }
    
    $results = $this->listingModel();
    
    $results = $this->sfp($request, $results);
    
    return response()->json([
      'status' => 'success',
      'data' => $results['results'],
      'links' => $results['paginationlinks']->toHtml()
    ]);
	}
  
  public function listingModel()
  {
    $branchcondition='=';
    if(empty($this->args['userinfo']['branch_id']))
    {$branchcondition='<>';}

    #DEFIND MODEL#
    return $this->model
      ->leftJoin('pos_qtstatus', $this->tablename.'.stage', '=', 'pos_qtstatus.qts_id')
      ->select(\DB::raw($this->fprimarykey." AS id, $this->fprimarykey, $this->tablename.title, JSON_UNQUOTE(pos_qtstatus.title->'$.".$this->dflang[0]."') AS stagetitle, stage, $this->tablename.branch_id as branch_id, iss_date, iss_date as inv_date, due_date, gtotal, mainvat, maindiscount, fter_note, sale_id, $this->tablename.tags as tags, $this->tablename.trash as trash"
                                              )
                                      )
        ->where($this->tablename.'.branch_id', $branchcondition , $this->args['userinfo']['branch_id']??0);
  } /*../function..*/
  
  public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array($this->fprimarykey,'title', 'inv_date'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : $this->fprimarykey;//$this->tablename.'.inv_date'; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
      
        if($sort=='inv_date') $sort = $this->tablename.'.inv_date';
        $results = $results->orderby($sort, $order);
        
        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title') && !empty($request->input('title'))) 
        {
            $qry=$request->input('title');

             $results = $results->where(function( $query ) use($qry){
                   $query
                  ->whereRaw("lower(JSON_UNQUOTE($this->tablename.title)) like '%".strtolower($qry)."%'");
              });
          
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }
      
         if ($request->has('cm_id') && !empty($request->input('cm_id'))) 
        {
            $qry=$request->input('cm_id');
            $customer_cond="$this->tablename.cm_id=".(int)$qry;
            $results = $results->whereRaw($customer_cond);
            
            array_push($querystr, 'cm_id='.$qry);
            $appends = array_merge ($appends,['cm_id'=>$qry]);
        }
      
        
     
       if ($request->has('stage') && !empty($request->input('stage'))) 
        {
            $qry=$request->input('stage');
            $stage_cond="$this->tablename.stage=".(int)$qry;
            $results = $results->whereRaw($stage_cond);
            
            array_push($querystr, 'stage='.$qry);
            $appends = array_merge ($appends,['stage'=>$qry]);
        }

        $date_cond='1=1';
        if ($request->has('fromdate') && !empty($request->input('fromdate'))) 
        {
            $qry=$request->input('fromdate');
            $fromdate=date("Y-m-d", strtotime($qry));
            $date_cond="$this->tablename.add_date='".$fromdate."'";
            
            array_push($querystr, 'fromdate='.$qry);
            $appends = array_merge ($appends,['fromdate'=>$qry]);
        }
        if ($request->has('todate') && !empty($request->input('todate'))) 
        {
            $qry=$request->input('todate');
            $todate=date("Y-m-d", strtotime($qry));
            $date_cond="$this->tablename.add_date='".$todate."'";

            array_push($querystr, 'todate='.$qry);
            $appends = array_merge ($appends,['todate'=>$qry]);
        }
        if($request->has('fromdate') && $request->has('todate') && !empty($request->input('fromdate')) && !empty($request->input('todate')))
        {
            $fromdate=$request->input('fromdate');
            $fromdate=date("Y-m-d", strtotime($fromdate));

            $todate=$request->input('todate');

            $todate=date("Y-m-d", strtotime($todate));

            $date_cond="($this->tablename.add_date between '$fromdate' and '$todate')";
        }
        $results = $results->whereRaw($date_cond);
        
        // PAGINATION and PERPAGE
        $perpage=null;
        $perpage_query=[];
        if ($request->has('perpage')) 
        {
            $perpage = $request->input('perpage');
            if($perpage<1)$perpage=14;
            $perpage_query = ['perpage='.$perpage];
            $appends = array_merge ($appends,['perpage'=>$perpage]);
          
        }
        else
        {
            $perpage = $this->rcdperpage<0 ? config('ccms.rpp') : $this->rcdperpage;
        }
      
         
      
        $results = $results->paginate($perpage);
    
        if($request->has('baseUrl')){
          $results->withPath($request->input('baseUrl'));
        }
        

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
  
    public function show(Request $request, $id=0){
        
        $obj_info=$this->obj_info;
        $default=$this->default();
        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];
    
		    $results = $this->listingmodel()->with('quotations')
            ->where("$this->fprimarykey", $id)
            ->where("cm_id", $request->input('cm_id'))
            ->first();
    
        
        return response()->json([
            'obj_info' => $obj_info,
            'lan' => $request->input('lan'),
            'data' => [
              'quotation' => $results,
              'sizes' => $allsizes,
              'colors' => $allcolors
            ]
          ]);
	}
}  