<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Backend\Pcategory;


class CategoryController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='c_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'category','title'=>'Category','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;



	public function __construct(array $args){ //public function __construct(Array args){
    
      $this->obj_info['title'] = __('label.lb10');
      $this->args = $args;
      $this->dflang = config('ccms.multilang')[0];
      $this->model = new Pcategory;

   
	} /*../function..*/
  
  public function default()
    {
        $categories=$this->model->getcategory(get_current_request_lan())->get();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['c_id','parent_id'],0);
    
        $newCategories = $this->model->with('children')->get();
        return ['cat_tree'=>$cat_tree, 'categories' => $newCategories];
    } /*../function..*/
   
	public function index(Request $request, $condition=[], $setting=[]){
        
    $obj_info=$this->obj_info;
    $default=$this->default();
    $cat_tree = $default['cat_tree'];
    
    return response()->json([
      'cat_tree' => $cat_tree,
      'categories' => $default['categories']
    ]);
    
	}
  
  
}  