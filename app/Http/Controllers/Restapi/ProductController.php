<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Backend\Product;
use App\Models\Backend\Pcategory;
use App\Models\Backend\Size;
use App\Models\Backend\Color;


class ProductController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='pd_id';
    private $tbltranslate='cms_productdetail';
    private $tblfile='cms_productfile';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'product','title'=>'Product','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;
    private $objPos;
    private $objCategory;


	public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb10');

        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
        $this->model = new Product;
       $this->objCategory = new CategoryController($args);
	} /*../function..*/
  
  public function default()
  {
    $sizes=Size::getsize(get_current_request_lan())->pluck('title', 's_id');
    $colors=Color::getcolor(get_current_request_lan())->pluck('title', 'cl_id');

    return ['sizes' =>$sizes, 'colors'=>$colors];
  } /*../function.s.*/
   
	public function index(Request $request, $condition=[], $setting=[]){
        
    $obj_info=$this->obj_info;
    $default=$this->default();
    $allsizes = $default['sizes'];
    $allcolors = $default['colors'];

    $results = $this->listingmodel();
    $sfp = $this->sfp($request, $results);
    $results = $sfp['results'];
    $ind=0;
    foreach($results as $row){

     $row = (object)$row;
      $row->title = html_entity_decode(html_entity_decode($row->title));
      #pricing
      $pricing = json_decode($row->pricing);
      $priceformat = formatmoney($pricing->dfpricing, true);
      $row->priceformat=$priceformat;
      $results[$ind]=$row;
      $ind++;

    }

    return response()->json([
      'obj_info' => $obj_info,
      'results' => $results,
      'allsizes' => $allsizes,
      'allcolors' => $allcolors,
      'lan' => $request->input('lan'),
      'cat_tree' => $this->objCategory->default()['cat_tree'],
      'categories' => $this->objCategory->default()['categories'],
    ]);
	}
  
  public function listingModel()
  {
      #DEFIND MODEL#

      $products = $this->model
        ->leftJoin('cms_unit', 'cms_unit.unt_id', '=', 'cms_product.unt_id')
        ->leftJoin('cms_pcategory', 'cms_pcategory.c_id', '=', 'cms_product.c_id')
        ->leftJoin('cms_productdetail', 'cms_product.pd_id', '=', 'cms_productdetail.pd_id')
        ->select(\DB::raw("cms_product.pd_id AS id, cms_product.pd_id,cms_product.c_id, barcode, sizes, colors, imginfo , pricing, cms_product.parent_id,	xtraprice,
                            cms_product.unt_id,
                            madewith,isservice, discount,pvat, 
                            cms_product.tag as tag,
                            cms_product.title as multi_title,
                            JSON_UNQUOTE(cms_product.title->'$.".get_current_request_lan()."') AS title,
                            JSON_UNQUOTE(cms_unit.title->'$.".get_current_request_lan()."') AS unit,
                            translate"
                          )
        )
        ->where('cms_product.trash', '!=', 'yes')
        ->where('cms_pcategory.display', '!=', 'no')
        ->where('cms_productdetail.lg_code', get_current_request_lan())
        ->groupBy('cms_product.pd_id');


      return $products;

  } /*../function..*/

  public function show(Request $request, $id=0){
        
        $obj_info=$this->obj_info;
        $default=$this->default();
        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];
    
		    $results = $this->listingmodel()->with('productFiles')->where("cms_product.$this->fprimarykey", $id)->first();
        $related = [];
        if($results){
          $cat_arr = explode(",", $results->c_id);
          $last_cat_id = end($cat_arr);
          $related = $this->listingmodel()->where('cms_product.pd_id', '<>', $results->pd_id)->whereRaw("FIND_IN_SET('$last_cat_id',cms_product.c_id)")->take(14)->get()->toArray();
          $ind=0;
          foreach($related as $row){

           $row = (object)$row;
            $row->title = html_entity_decode(html_entity_decode($row->title));
            #pricing
            $pricing = json_decode($row->pricing);
            $priceformat = formatmoney($pricing->dfpricing, true);
            $row->priceformat=$priceformat;
            $related[$ind]=$row;
            $ind++;

          }
        }
        
        return response()->json([
            'obj_info' => $obj_info,
            'lan' => $request->input('lan'),
            'data' => [
              'product' => $results,
              'related' => $related,
              'images' => [],
              'cat_tree' => $this->objCategory->default()['cat_tree'],
              'categories' => $this->objCategory->default()['categories'],
              'sizes' => $allsizes,
              'colors' => $allcolors
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