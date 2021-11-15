<?php
namespace App\Http\Controllers\Restapi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\QuotationController;

class CheckoutController extends Controller
{
	
  private $args;
	  private $model;
    private $tablename;
    private $fprimarykey='qt_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'checkout','title'=>'Checkout','routing'=>'admin.controller','icon'=>'<i class="fa fa-cubes" aria-hidden="true"></i>'];
    
    private $attribute;
    private $objPos;
    private $objQuotation;
    private $objCategory;

	public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb10');

        $this->args = $args;
        $this->dflang = config('ccms.multilang')[0];
        $this->objQuotation = new QuotationController($args);
    $this->objCategory = new CategoryController($args);
	} /*../function..*/
  
  public function default()
  {
    $sizes=Size::getsize(get_current_request_lan())->pluck('title', 's_id');
    $colors=Color::getcolor(get_current_request_lan())->pluck('title', 'cl_id');

    return ['sizes' =>$sizes, 'colors'=>$colors];
  } /*../function.s.*/
   
	public function index(Request $request, $condition=[], $setting=[]){
        return response()->json([
          'categories' => $this->objCategory->default()['categories'],
        ]);
	}
  
  public function listingModel()
  {
    return null;
  } /*../function..*/

  public function store(Request $request){
     //return $this->objQuotation->storeapi($request);
    return response()->json(['data'=>'hiiii']);
	}
  
}  