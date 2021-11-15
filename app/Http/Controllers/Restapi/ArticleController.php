<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Backend\Article;


class ArticleController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='a_id';
    private $tbltranslate='cms_article';
    private $tblfile='cms_articlefile';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'article','title'=>'Article','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;
    private $objPos;
    private $objCategory;


	public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb10');

        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
        $this->model = new Article;
    $this->objCategory = new CategoryController($args);
	} /*../function..*/
  
  public function default()
  {
    return [];
  } /*../function.s.*/
   
	public function index(Request $request, $condition=[], $setting=[]){ 
    return response()->json([]);
	}
  
  public function listingModel()
  {
      #DEFIND MODEL#

      $products = $this->model->select(\DB::raw("
          a_id, JSON_UNQUOTE(title->'$.".get_current_request_lan()."') AS title, 
          title as multi_title, imginfo, c_id, status,
          parent_id, tag, add_date
      "));


      return $products;

  } /*../function..*/

  public function show(Request $request, $id=0){
        
        $obj_info=$this->obj_info;
        $default=$this->default();

		    $results = $this->listingmodel()->with('articleFiles', 'articleDetails')->where($this->fprimarykey, $id)->first();
        
        return response()->json([
            'obj_info' => $obj_info,
            'lan' => $request->input('lan'),
            'data' => [
              'article' => $results,
              'cat_tree' => $this->objCategory->default()['cat_tree']
            ]
          ]);
	}
  
  public function sfp($request, $results)
  {
    $allowed = array('id', 'pd_id', 'code','title', 'c_id', 'ordering', 'add_date'); // add allowable columns to sort on
    $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'title'; // if user type in the url a column that doesnt exist app will default to id
    $order = $request->input('order') === 'asc' ? 'asc' : 'desc'; // default desc
    $results = $results->orderby($sort, $order);
      // FILTERS
      $appends = []; #set its elements for Appending to Pagination#
      $querystr = [];

    if ($request->input('c_id')) 
      {
          $qry=$request->input('c_id');
          if(is_numeric($qry)){
            $qry=(int)$qry;
            $subcate = Pcategory::where('parent_id',$qry);

            $subcate=$subcate->pluck('c_id')->toArray()??'';
            if(!empty($subcate)) 
              $results = $results->where(function( $query ) use($qry, $subcate){
                   $query->whereRaw("FIND_IN_SET('$qry',cms_pcategory.c_id)")
                   ->orWhereIn('cms_pcategory.c_id', $subcate);
              });
            else
            $results = $results->whereRaw("FIND_IN_SET('$qry',cms_pcategory.c_id)"); 
            array_push($querystr, 'c_id='.$qry);
            $appends = array_merge ($appends,['c_id'=>$qry]);
          }
      
          $results = $results->orderby('id', 'desc');
      }
    
      if($request->has('title') &&!empty($request->input('title'))){
        $qry=$request->input('title');
        $results = $results->whereRaw("lower(JSON_UNQUOTE(cms_product.title->'$.".get_current_request_lan()."')) like '".strtolower($qry)."%'");
        array_push($querystr, 'title='.$qry);
        $appends = array_merge ($appends,['title'=>$qry]);
      }
      
      $perpage = $this->rcdperpage<0 ? 14 : $this->rcdperpage;
      $perpage_query = ['perpage='.$perpage];
      $appends = array_merge ($appends,['perpage'=>$perpage]);
    
      $results = $results->paginate($perpage);
    
      $appends = array_merge ($appends, [
        'sort'      => $request->input('sort'), 
        'order'     => $request->input('order')
      ]);

      $pagination = $results->appends($appends);

      $recordinfo = recordInfo($pagination->currentPage(), $pagination->perPage(), $pagination->total(), $pagination->lastPage());

      return [
          'results'           => $results,
          'paginationlinks'    => $pagination->links(),
          'recordinfo'    => $recordinfo,
          'querystr'      => $querystr,
          //'perpage_query' => $perpage_query
      ];
  } /*../function..*/
  
}  