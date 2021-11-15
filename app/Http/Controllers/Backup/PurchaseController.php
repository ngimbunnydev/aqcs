<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
use PDF;
//use Illuminate\Validation\Rule;
use App\Rules\Notin_array;
use App\Rules\Required_qty;

use App\Models\Backend\Purchase;
use App\Models\Backend\Purchases;
use App\Models\Backend\Accountno;

use App\Models\Backend\Product;
use App\Models\Backend\Size;
use App\Models\Backend\Color;
use App\Models\Backend\Supplier;
use App\Models\Backend\General;


use App\Http\Controllers\Backend\InvcycleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\AddstockController;
use App\Http\Controllers\Backend\RpaymentController;
use App\Http\Controllers\Backend\QuotationController;


class PurchaseController extends Controller
{
    private $args;
    private $model;
    private $fprimarykey='pch_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'purchase','title'=>'Purchase','routing'=>'admin.controller','icon'=>'<i class="fa fa-shopping-cart" aria-hidden="true" style="color: #000099"></i>'];

    private $protectme;

    private $invcycle;
    private $users;
    private $addstock;

    private $tablename;
    private $submodel;


    public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb164');
        $this->protectme = [  
                        config('ccms.protectact.index'),
                        config('ccms.protectact.create'),
                        config('ccms.protectact.duplicate'),
                        config('ccms.protectact.store'),
                        config('ccms.protectact.edit'),
                        config('ccms.protectact.update'),
                        config('ccms.protectact.delete'),
                        config('ccms.protectact.restore'),
                        config('ccms.protectact.destroy'),

                        ];
                        

        $this->args = $args;
        $this->model = new Purchase;
        $this->submodel = new Purchases;
        $this->tablename = $this->model->Gettable();
        $this->dflang = config('ccms.multilang')[0];

        $this->invcycle = new InvcycleController($args);
        $this->users = new UserController($args);
        
        $this->addstock = new AddstockController($args);
      

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
            
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $js_config = array_merge($js_config);

        $sizes=Size::getsize($this->dflang[0])->pluck('title', 's_id');
        $colors=Color::getcolor($this->dflang[0])->pluck('title', 'cl_id');
      
        $accountno = Accountno::getlist([6]);
        
        return ['js_config'=>$js_config, 'sizes' =>$sizes, 'colors'=>$colors, 'accountno'=>$accountno];
    } /*../function..*/

    public function listingModel()
    {
        $branchcondition='=';
        if(empty($this->args['userinfo']['branch_id']))
        {$branchcondition='<>';}
      
        #DEFIND MODEL#
        return $this->model
        ->leftJoin('pos_supplier', $this->tablename.'.supplier_id', '=', 'pos_supplier.supplier_id')
        ->select(\DB::raw(   $this->fprimarykey." AS id, $this->tablename.title, $this->tablename.branch_id as branch_id, accno_id, accno_discount ,stage, inv_date, gtotal, paid,mainvat,maindiscount, fter_note, latinname, $this->tablename.supplier_id, cm_code, nativename, personincharge, cphone, caddress, (gtotal-paid) as balance,sale_id, $this->tablename.tags as tags, $this->tablename.trash as trash"
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
            //$results = $results->where('title', 'like', '%'.$qry.'%');
          
             $results = $results->where(function( $query ) use($qry){
                   $query
                  ->whereRaw("lower(JSON_UNQUOTE(title)) like '%".strtolower($qry)."%'")
                  ->orWhereRaw("latinname like '%".$qry."%'")
                  ->orWhereRaw("nativename like '%".$qry."%'")
                  ->orWhereRaw("personincharge like '%".$qry."%'");
              });
          
          
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
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
        
      

        //dd($results->toSql());
        #no need to send default sort and order to Blade#
        // if($sort==$this->fprimarykey && $order=='desc')
        // {
        //     $sort = '';
        //     $order = '';
        // }
        

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

        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with($setting);


    } /*../function..*/


    public function trash(Request $request)
    {
        $obj_info= $this->obj_info;
        return view('backend.widget.noaction',
                    compact('obj_info'
                            )


                )->with(
                    [
                        
                        'caption' => __('ccms.noaction')
                    ]
                );
    } /*../function..*/


    public function create(Request $request)
    {

        $obj_info=$this->obj_info;

        $default=$this->default();
        $js_config = $default['js_config'];
        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];
        $accountno = $default['accountno'];
        $inncycle=$this->invcycle->listingModel($this->dflang[0])->pluck('title', 'id')->where('trash', '!=', 'yes');
        $inncycle = json_decode(json_encode($inncycle), true);

        $users=$this->users->listingModel($this->dflang[0])->pluck('name', 'id')->where('trash', '!=', 'yes');
        $users = json_decode(json_encode($users), true);
        $args = $this->args;

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'allsizes',
                            'allcolors',
                            'accountno',
                            'inncycle',
                            'users',
                            'args'
                            )


                )->with(
                    [
                        'submitto'  => 'store',
                        'fprimarykey'     => $this->fprimarykey,
                        'caption' => __('ccms.new')
                    ]
                );
    } /*../function..*/


    public function store(Request $request)
    {
        //https://scotch.io/tutorials/simple-laravel-crud-with-resource-controllers
        //return redirect()->back();

        //dd($request->all());

        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            $preview = false;
            $returntype = '';
            if($request->has('returntype'))
            {
               $preview = true; 
                $returntype = $request->input('returntype');
            }

            $possave = false;
                if($request->has('possave'))
                {
                   $possave = true; 
                }

            $validator = $this->validation($request);
          
            if ($validator->fails()) {
                //$errors = $validator->errors();
                //foreach ($errors->all() as $message) {
                    //echo $message;
                //}

                if($preview){
                    

                    $routing=url_builder($obj_info['routing'],[$obj_info['name'],'preview']);
                    return [
                                        'act' => false,
                                        'url' => $routing,
                                        'passdata' => [
                                                        
                                                    ]
                                    ];



                }

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                $subpd_id = $request->input('subpd_id');
                $prodctsarray=[];
                if(count($subpd_id)>1)
                {
                    array_pop($subpd_id);
                    $prodctsarray=productsubform($subpd_id, new Product, $this->dflang); 
                } 

                

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input(),
                                    'products' => $prodctsarray,
                                    'submitto' => 'create'
                                ]
                ];

            } else {

                $data=$this->setinfo($request);
                
                $savedata =false;
              
                $pd_ids = array_pluck($data['subtableData'], 'pd_id');
                    $productinfo = Product::select(\DB::raw("pd_id, pcost, avgcost, xtracost, madewith, imginfo, isservice,
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                            );
                $productinfo = $productinfo->orwhereIn('pd_id', $pd_ids)->get()->keyBy('pd_id')->toArray();
                           
                if($preview){
                    $routing=url_builder($obj_info['routing'],[$obj_info['name'],'preview']);
                    return [
                                        'act' => false,
                                        'url' => $routing,
                                        
                                        'passdata' => [
                                                        
                                                        'input' => $data,
                                                        'id' => $data['id'],
                                                        'returntype' => $returntype
                                                    ]
                                    ];

                }
                

                
                $savedata = $this->model->insert($data['tableData']);
                if($savedata)
                {
                   
                      /*save sub invoice*/ 
                      //
                      $savsubinvoice = Purchases::insert($data['subtableData']);
                      if(!$savsubinvoice)
                      {
                          $savedata= false;
                          $destroyinv = $this->model->where($this->fprimarykey, (int)$data['id'])->delete(); 
                          
                          $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                          $subpd_id = $request->input('subpd_id');
                          $prodctsarray=[];
                          if(count($subpd_id)>1)
                          {
                              array_pop($subpd_id);
                              $prodctsarray=productsubform($subpd_id, new Product, $this->dflang); 
                          } 
                          return [
                              'act' => false,
                              'url' => $routing,
                              'passdata' => [
                                              'errors' => __('ccms.rqnvalid'),
                                              'input' => $request->input(),
                                              'products' => $prodctsarray,
                                              'submitto' => 'create'
                                          ]
                          ];
                        
                        
                        
                        
                     }
                    else
                    {
                      
                      /*Add Product To Stock*/
                      $pchase_detail = Purchases::where('pch_id', $data['id'])->select('*');
                      $pchase_detail = $pchase_detail->get()->keyBy('pchd_id')->toArray();
                      $pchased_id = array_keys($pchase_detail);
                      
                      $request->request->add(['pchase_id'=>$data['id'], 'pchased_id'=>$pchased_id]);
                      $addstock_data = $this->addstock->setinfo($request);
                      $save_stock = $this->addstock->insertdata($request, $addstock_data, false);
                      //dd($save_stock);
                      /*End Stock*/
                      
                      /*Add General Journal*/
                      $gj_data=[];
                      //For Main
                      $purchase_acc = $data['tableData']['accno_id'];
                      $purchase_natureside =  natureside([$purchase_acc]);
                      if($purchase_natureside){
                        $purchase_natureside = $purchase_natureside[$purchase_acc];
                        $amount = ['dr'=>0, 'cr'=>0];
                        $amount[$purchase_natureside->natureside] = $data['tableData']['gtotal'];
                         $gj_record = [
                              0,
                              $data['tableData']['branch_id'],
                              'pch',
                              $data['id'],
                              '',
                              0,
                              $purchase_natureside->accno_id,
                              $purchase_natureside->title,
                              $amount['dr'],
                              $amount['cr'],
                              '',
                              $data['tableData']['inv_date'],
                              '',
                              $data['tableData']['add_date'],
                              'no',
                              $data['tableData']['blongto']
                            ];
                        
                          array_push($gj_data, gf_setinfo($gj_record));
                      }
                      
                      
                      //For Sub
                      $product_ids = array_column($pchase_detail, 'pd_id');
                      $product_acc = productAccount($product_ids);
                      
                      $coa = $product_acc['coa'];
                      $natureside = $product_acc['natureside'];
                      //initial data
                      
                      foreach($pchase_detail as $record){
                        $pd_id = $record['pd_id'];
                        //$cost = $record['amount'];
                        $amount = ['dr'=>0, 'cr'=>0];
                        
                        //$product_coa = [$coa[$pd_id]->accno_id, $coa[$pd_id]->accno_idrpm, $coa[$pd_id]->accno_idcogs];
                        $accno_id = $coa[$pd_id]->accno_id;
                        
                        if(!empty($accno_id)){
                          $dr_cr = $natureside[$accno_id]->natureside;
                          $amount[$dr_cr] = $record['amount'];
                          $gj_record = [
                              0,
                              $data['tableData']['branch_id'],
                              'pch',
                              $record['pch_id'],
                              'pchd',
                              $record['pchd_id'],
                              $accno_id,
                              $natureside[$accno_id]->title,
                              $amount['dr'],
                              $amount['cr'],
                              '',
                              $data['tableData']['inv_date'],
                              '',
                              $data['tableData']['add_date'],
                              'no',
                              $data['tableData']['blongto']
                            ];
                            array_push($gj_data, gf_setinfo($gj_record));
                        }
                        
                        
                       
                      }/*end For*/
                      
                      $sav_gj =  save_gj($gj_data);
                      /*end GJ*/
                      
                      
                    }
                 //
                    

                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $arr_savetype=[
                        "save"=>"index", 
                        "save.1"=>"index?pay=".$data['id'],
                        "save.2"=>"index?pdf=".$data['id'],
                        "save.3"=>"index?receipt=".$data['id'], 
                        "new"=>"create", 
                        "apply"=> 'edit/'.$data['id']
                    ];

                    $action = empty($arr_savetype[$savetype])? 'index' : $arr_savetype[$savetype];

                    $routing=url_builder(
                        $obj_info['routing'],
                        [$obj_info['name'], $action]
                    );
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms)
                            #->with($this->fprimarykey , $data['id']);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        //'input' => $request->input(),
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];

                }/*../if savedata==true..*/
            }
          
          
        } /*../if POST..*/

        
    } /*../function..*/
  
    function save($data, $preview){
      ///
         
                
                $savedata =false;
              
                $pd_ids = array_pluck($data['subtableData'], 'pd_id');
                    $productinfo = Product::select(\DB::raw("pd_id, pcost, avgcost, xtracost, madewith, imginfo, isservice,
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                            );
                $productinfo = $productinfo->orwhereIn('pd_id', $pd_ids)->get()->keyBy('pd_id')->toArray();
                           
                if($preview){
                    $routing=url_builder($obj_info['routing'],[$obj_info['name'],'preview']);
                    return [
                                        'act' => false,
                                        'url' => $routing,
                                        
                                        'passdata' => [
                                                        
                                                        'input' => $data,
                                                        'id' => $data['id'],
                                                        'returntype' => $returntype
                                                    ]
                                    ];

                }
                

                
                $savedata = $this->model->insert($data['tableData']);
                if($savedata)
                {
                   
                      /*save sub invoice*/ 
                      //
                      $savsubinvoice = Purchases::insert($data['subtableData']);
                      if(!$savsubinvoice)
                      {
                          $savedata= false;
                          $destroyinv = $this->model->where($this->fprimarykey, (int)$data['id'])->delete(); 
                          
                          $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                          $subpd_id = $request->input('subpd_id');
                          $prodctsarray=[];
                          if(count($subpd_id)>1)
                          {
                              array_pop($subpd_id);
                              $prodctsarray=productsubform($subpd_id, new Product, $this->dflang); 
                          } 
                          return [
                              'act' => false,
                              'url' => $routing,
                              'passdata' => [
                                              'errors' => __('ccms.rqnvalid'),
                                              'input' => $request->input(),
                                              'products' => $prodctsarray,
                                              'submitto' => 'create'
                                          ]
                          ];
                        
                        
                        
                        
                     }
                    else
                    {
                      
                      /*Add Product To Stock*/
                      $pchase_detail = Purchases::where('pch_id', $data['id'])->select('*');
                      $pchase_detail = $pchase_detail->get()->keyBy('pchd_id')->toArray();
                      $pchased_id = array_keys($pchase_detail);
                      
                      $request->request->add(['pchase_id'=>$data['id'], 'pchased_id'=>$pchased_id]);
                      $addstock_data = $this->addstock->setinfo($request);
                      $save_stock = $this->addstock->insertdata($request, $addstock_data, false);
                      //dd($save_stock);
                      /*End Stock*/
                      
                      /*Add General Journal*/
                      $gj_data=[];
                      //For Main
                      $purchase_acc = $data['tableData']['accno_id'];
                      $purchase_natureside =  natureside([$purchase_acc]);
                      if($purchase_natureside){
                        $purchase_natureside = $purchase_natureside[$purchase_acc];
                        $amount = ['dr'=>0, 'cr'=>0];
                        $amount[$purchase_natureside->natureside] = $data['tableData']['gtotal'];
                         $gj_record = [
                              0,
                              $data['tableData']['branch_id'],
                              'pch',
                              $data['id'],
                              '',
                              0,
                              $purchase_natureside->accno_id,
                              $purchase_natureside->title,
                              $amount['dr'],
                              $amount['cr'],
                              '',
                              $data['tableData']['inv_date'],
                              '',
                              $data['tableData']['add_date'],
                              'no',
                              $data['tableData']['blongto']
                            ];
                        
                          array_push($gj_data, gf_setinfo($gj_record));
                      }
                      
                      
                      //For Sub
                      $product_ids = array_column($pchase_detail, 'pd_id');
                      $product_acc = productAccount($product_ids);
                      
                      $coa = $product_acc['coa'];
                      $natureside = $product_acc['natureside'];
                      //initial data
                      
                      foreach($pchase_detail as $record){
                        $pd_id = $record['pd_id'];
                        //$cost = $record['amount'];
                        $amount = ['dr'=>0, 'cr'=>0];
                        
                        //$product_coa = [$coa[$pd_id]->accno_id, $coa[$pd_id]->accno_idrpm, $coa[$pd_id]->accno_idcogs];
                        $accno_id = $coa[$pd_id]->accno_id;
                        
                        if(!empty($accno_id)){
                          $dr_cr = $natureside[$accno_id]->natureside;
                          $amount[$dr_cr] = $record['amount'];
                          $gj_record = [
                              0,
                              $data['tableData']['branch_id'],
                              'pch',
                              $record['pch_id'],
                              'pchd',
                              $record['pchd_id'],
                              $accno_id,
                              $natureside[$accno_id]->title,
                              $amount['dr'],
                              $amount['cr'],
                              '',
                              $data['tableData']['inv_date'],
                              '',
                              $data['tableData']['add_date'],
                              'no',
                              $data['tableData']['blongto']
                            ];
                            array_push($gj_data, gf_setinfo($gj_record));
                        }
                        
                        
                       
                      }/*end For*/
                      
                      $sav_gj =  save_gj($gj_data);
                      /*end GJ*/
                      
                      
                    }
                 //
                    

                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $arr_savetype=[
                        "save"=>"index", 
                        "save.1"=>"index?pay=".$data['id'],
                        "save.2"=>"index?pdf=".$data['id'],
                        "save.3"=>"index?receipt=".$data['id'], 
                        "new"=>"create", 
                        "apply"=> 'edit/'.$data['id']
                    ];

                    $action = empty($arr_savetype[$savetype])? 'index' : $arr_savetype[$savetype];

                    $routing=url_builder(
                        $obj_info['routing'],
                        [$obj_info['name'], $action]
                    );
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms)
                            #->with($this->fprimarykey , $data['id']);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        //'input' => $request->input(),
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];

                }/*../if savedata==true..*/
      //
    }

    public function update(Request $request)
    {
        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {

            
            $validator = $this->validation($request, true);
            if ($validator->fails()) {
                //$errors = $validator->errors();
                //foreach ($errors->all() as $message) {
                    //echo $message;
                //}

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$request->input($this->fprimarykey)]);
                #return \Redirect::to($routing)
                #->with('errors', $validator->errors()->first())
                #->with('input' , $request->input());

                $subpd_id = $request->input('subpd_id');
                $prodctsarray=[];
                if(count($subpd_id)>1){
                    array_pop($subpd_id);
                    $prodctsarray = productsubform($subpd_id, new Product, $this->dflang);
                } 
                

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input(),
                                    'products' => $prodctsarray
                                ]
                ];

            } else {


                $data=$this->setinfo($request, true);
                $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);

                $adjuststockdata = $data['adjuststockdata'];

                /**
                    Add stock by old Ajdust stock and delete it out
                **/
                $sadj_id = $data['oldadjuststockid'];
                $oldadjuststocks = Adjuststocks::where('sadj_id', $sadj_id)->get()->toArray();
                $oldidtostock = array_pluck($oldadjuststocks, 'id_tostock');
                $proceedaddstock = [
                    'subtableData' => $oldadjuststocks,
                    'id_tostocks' => $oldidtostock,
                ];
                
                if(!empty($oldadjuststocks) && !empty($adjuststockdata['subtableData']))
                {    

                    /*save sub invoice*/
                    $deleteoldsub = Invoices::where($this->fprimarykey,$data['id'])->delete();
                    $savsubinvoice = Invoices::insert($data['subtableData']);

                    /*Add stock*/
                    stockProceed($proceedaddstock, $this->args, new Stock);
                    $deleteoldstock = Adjuststocks::where('sadj_id', $sadj_id)->delete();

                    /*Adjust again*/
                    $save_adjusts = Adjuststocks::insert($adjuststockdata['subtableData']);
                    stockProceed($adjuststockdata, $this->args, new Stock, -1);
                    
                
                }

                ############
                $savetype=strtolower($request->input('savetype'));
                $success_ms = __('ccms.suc_edit');
                    switch ($savetype) {
                        case 'save':
                            # code...
                            if ($request->session()->has('backurl')) 
                            {
                                $routing = $request->session()->get('backurl');
                                $request->session()->forget('backurl');
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                if(stripos($routing, $obj_info['name'])===false)
                                {
                                    $routing=url_builder($obj_info['routing'],[$obj_info['name']]);
                                }

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            }
                            else
                            {
                                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            }
                            
                            break;
                        case 'save.1':
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],"index?pay=".$data['id']]);
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            break;

                        case 'save.2':
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],"index?pdf=".$data['id']]);
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            break;

                        case 'save.3':
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],"index?receipt=".$data['id']]);
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id']
                                                    ]
                                    ];
                            break;

                        case 'new':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id']
                                                        
                                                    ]
                                    ];
                            break;

                        case 'apply':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$data['id']]);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id']
                                                        
                                                    ]
                                    ];
                            break;

                        default:
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id']
                                                        
                                                    ]
                                    ];
                            break;

                    }
               
            }
        } /*../if POST..*/

    } /*../end fun..*/

    public function edit(Request $request, $id=0)
    {
        return null;
        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info=$this->obj_info;

        $default=$this->default();
        $js_config = $default['js_config'];
        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];

        $inncycle=$this->invcycle->listingModel($this->dflang[0])->pluck('title', 'id')->where('trash', '!=', 'yes');
        $inncycle = json_decode(json_encode($inncycle), true);

        $users=$this->users->listingModel($this->dflang[0])->pluck('name', 'id')->where('trash', '!=', 'yes');
        $users = json_decode(json_encode($users), true);
        $args = $this->args;

        $input = null;
       
        
        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $input = $this->model->where($this->fprimarykey, (int)$editid)->where('trash', '!=', 'yes')->where('stage', '=', 0)->get(); 
        if($input->isEmpty())
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }
        

        $input = $input->toArray()[0];


        if((float) $input['paid']>0)
        {
            // //$routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            // $routing = redirect()->back()->getTargetUrl();
            // return \Redirect::to($routing)
            // ->with('tryid', $editid)
            // ->with('ispaid', true);
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }
        elseif((int)$input['stage']!=0)
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }
        elseif((int)$input['trash']!='yes')
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }
        elseif((int)$input['branch_id']!=$this->args['userinfo']['branch_id'])
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.nbltbranch'));
        }

        $oldadjuststock = Adjuststock::where($this->fprimarykey,$editid)->get()->toArray()[0];
        $wh_id = $oldadjuststock['wh_id'];
        if((int)$wh_id!=$this->args['userinfo']['wh_id'])
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.nbltwarehouse'));
        }

        #Customer
        if(!empty($input['cm_id']))
        {
            $customer = Customer::where('cm_id', $input['cm_id'])->first();
            $input['customer']=$customer->latinname;
            $input['ct_id']=$customer->ct_id;
           
        }

        #extract tag#
        $data_tag = json_decode($input['tags'], TRUE);
        $tag=[];
        foreach ($data_tag as $key => $value) {
            $tag[$key]=$value;
        }

        $input = array_merge($input, $tag);

        $input['maindiscounttype']=1;
        if($input['maindiscount']<0){
            $input['maindiscount'] = abs($input['maindiscount']);
            $input['maindiscounttype']=-1;
        }

        $input['inv_date'] = date("d-m-Y", strtotime($input['inv_date'])); 
        $input['due_date'] = date("d-m-Y", strtotime($input['due_date']));


        //dd($input);

        /*Subform ===> Shall be a function*/
        $subform = $this->RetrieveSubForm($this->submodel, $editid, $this->dflang[0]);
        $input = array_merge($input, $subform);       


        skip:
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'allsizes',
                            'allcolors',
                            'inncycle',
                            'users',
                            'args',
                            'input'
                            )


                )->with(
                    [
                        'submitto'      => 'update',
                        'fprimarykey'   => $this->fprimarykey,
                        'caption' => __('ccms.edit')
                    ]
                );
    } /*../end fun..*/

    public function validation($request, $isupdate=false){
        //dd($request->input());
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $update_rules= [ $this->fprimarykey => 'required'];

            $request->request->add(['wh_id' => $this->args['userinfo']['wh_id']??0]); 
            $rules = [
                        'title'      => 'required',
                        'inv_date'   => 'required',
                        'supplier_id' => 'required',
                        'wh_id'     => 'required|numeric|gt:0',
//                         'accno_id' => 'required|numeric|gt:0',
                        
                    ];

            $subform = $request->input('subpd_id');
            $numrecord=count($subform);
            $bulkqty = [];
            //$numrecord= $numrecord==1?1:$numrecord-1;

            if($numrecord>1)
            {
                $numrecord-=1;
                array_pop($subform);


                $results = Product::plookupquery($this->dflang);
                $results = $results->whereIn('pd_id', $subform);
                $bulkqty = bulkQtyForValidation($results, $request);

                /*get all product has made-with and it is a Service ==> shall be function*/
                $get_madewith = Product::select(['pd_id','madewith','isservice'])->whereIn('pd_id', $subform)->get()->toArray();
                $definemaewith=[];
                $definisservice=[];
                foreach ($get_madewith as $key => $value) {
                      $definemaewith[$value['pd_id']] =$value['madewith'];
                      $definisservice[$value['pd_id']] =$value['isservice'];
                }
  
                //if(beta())dd($definemaewith);

            }
            
            //dd(array_merge($request->input(),$bulkqty));

            $rules['subpd_id.*'] = "distinct";
            $madewith =[];
            for($i=0; $i<$numrecord; $i++)
            {
                    $rules['subpd_id.'.$i] = 'required|numeric|exists:cms_product,pd_id';
                    $rules['description.'.$i] = 'required';
              
                    //if(config('sysconfig.usingsizecolor')!='no')
                    $rules['bulkqty'.$i] = [new Required_qty];
                    $rules['subcost.'.$i] = 'required|numeric';
                    
                    /*Iservice*/
                    $isservice['isservice'.$i] = isset($definisservice[$subform[$i]])?$definisservice[$subform[$i]]:'';
                    $rules['isservice'.$i] = 'in:no';
              
                    /*product has mad-with not accept*/
                    $madewithdata = json_encode([]);
                      if(isset($definemaewith[$subform[$i]]) && !empty(isset($definemaewith[$subform[$i]]))){
                        $madewithdata = isset($definemaewith[$subform[$i]])?$definemaewith[$subform[$i]]:'';
                      }
                      //$madewithdata = isset($definemaewith[$subform[$i]])?$definemaewith[$subform[$i]]:json_encode(['']);
                      $madewithdata = json_decode($madewithdata,true);
                      $madewithstr = count($madewithdata)==0?'':'hasevalue';
                      $madewith['madewith'.$i] = $madewithstr;
                      $rules['madewith'.$i] = 'string|max:0';
                   
                    
            }
            
            
            

            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'max' => __('ccms.madewith'),
                'gt' =>  __('ccms.fieldreqire'),//__('ccms.gt')
                'in' => __('ccms.isservice'),
                'exists' => 'Product is wrong'
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
           if($bulkqty==null)$bulkqty=[];
            $validator =Validator::make(array_merge($request->input(),$bulkqty,$madewith, $isservice), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $isupdate=false, $forbulkqty=true){
        //$request->request->add(['variable' => 'value']); 
        //dd($request);

        
        $newid=($isupdate)? $request->input($this->fprimarykey)  : $this->model->max($this->fprimarykey)+1;
      
       if(config('ccms.backend')=='lst')
       {
         $newid = $this->model->max($this->fprimarykey);
         if(empty($newid)) $newid=191;
         else $newid+=1;
         
         $newid=($isupdate)? $request->input($this->fprimarykey)  : $newid;
         
       }

        $inv_date=!empty($request->input('inv_date'))?date("Y-m-d", strtotime($request->input('inv_date'))):date("Y-m-d");
        if(!$isupdate)
        {
            $yearid=$this->model->whereRaw('YEAR(inv_date)='.date('Y'),strtotime($inv_date))->max('yearid')+1;
            $vat_id=$this->model->whereRaw('mainvat>0')->max('vat_id');
            if(!empty($vat_id))
            $vat_id+=1;
        }
        
        
        
        $due_date=!empty($request->input('due_date'))?date("Y-m-d", strtotime($request->input('due_date'))):date("Y-m-d");
      
       
          $subform = setSubform($request, new Product, $this->fprimarykey, 'pchd_id', $newid, $this->dflang, $forbulkqty);
          
        

        $subtableData=[];
        if ($request->has('subpd_id'))
        {
            $subpd_id = $request->input('subpd_id');
            $numrecord=count($subpd_id);
            if($numrecord>1)
            {
                $numrecord-=1;
                array_pop($subpd_id);
                $subtotal=0;
                for($i=0; $i<$numrecord; $i++)
                {
                    $qty = $subform['subtable'][$i]['qty'];
                    $qty_tostock = $subform['subtable'][$i]['qty_tostock'];
                    $qty_total = $subform['subtable'][$i]['qty_total'];
                    
                    $subdiscount = !empty($request->input('subdiscount')[$i])?$request->input('subdiscount')[$i]:0;
                    $subdiscounttype = !empty($request->input('subdiscounttype')[$i])?$request->input('subdiscounttype')[$i]:1;
                    $subdiscount = abs($subdiscount)*$subdiscounttype;

                    $subcost = !empty($request->input('subcost')[$i])?$request->input('subcost')[$i]:0;
                    
                    $subvat = !empty($request->input('subvat')[$i])?$request->input('subvat')[$i]:0;
                    $amount = calAmount($subcost, $qty_total ,$subdiscount, $subvat);
                    $subnote = !empty($request->input('subnote')[$i])?$request->input('subnote')[$i]:'';
                    $subtotal= $subtotal + $amount[1];


                    $record = [
                                
                        'pchd_id' => 0, //!empty($request->input('subid')[$i])?$request->input('subid')[$i]:0,
                        'pch_id' => $newid,
                        'pd_id' => !empty($subpd_id[$i])?$subpd_id[$i]:0,
                        'description' => !empty($request->input('description')[$i])?$request->input('description')[$i]:'',
                        'qty' => $qty,
                        'qty_tostock' => $qty_tostock,
                        'qty_total' => $qty_total,
                        'cost' => $subcost,
                        'subdiscount' => $subdiscount,
                        'subvat' => $subvat,
                        'cycle' => !empty($request->input('cycle')[$i])?(string)$request->input('cycle')[$i]:'false',
                        'ordering' =>0,
                        'amount' => $amount[1],
                        'subnote' => $subnote,
                        'batch'=> $subform['subtable'][$i]['batch'],
                        'product_expdate'  => $subform['subtable'][$i]['product_expdate'],
                                
                    ];
                     
                    array_push($subtableData, $record);
                }
            }
        }
      
        $maindiscount = !empty($request->input('maindiscount'))?$request->input('maindiscount'):0;
        $maindiscounttype = !empty($request->input('maindiscounttype'))?$request->input('maindiscounttype'):1;
        $maindiscount = abs($maindiscount)*$maindiscounttype;
        $mainvat = !empty($request->input('mainvat'))?$request->input('mainvat'):0;
        $gtotal = calAmount($subtotal, 1 ,$maindiscount, $mainvat);


        #setup table data
        
        $tags=['cashier_id', 'voidnote','pay_amountusd', 'pay_amountnative','invtime'];
        $tag= [];
        foreach ($tags as $field) 
        {

            $tag[$field]=!empty($request->input($field))?$request->input($field):'';
                
        }

        $tableData = [
            
                $this->fprimarykey => $newid,
                'yearid' => $yearid??0,
                'vat_id' => $vat_id??0,
                'supplier_id' => !empty($request->input('supplier_id'))?$request->input('supplier_id'):0,
                'accno_id' => !empty($request->input('accno_id'))?$request->input('accno_id'):0,
                'branch_id'   => !empty($this->args['userinfo']['branch_id'])?$this->args['userinfo']['branch_id']:0,
                'title' =>$request->input('title') ?? 'General Purchase',
                'stage' =>0,
                /*0->normal, 1->returned*/
                'inv_date' => $inv_date,
                'due_date' => $due_date,
                'fter_note' => !empty($request->input('fter_note'))?$request->input('fter_note'):'',
                'prv_note' => !empty($request->input('prv_note'))?$request->input('prv_note'):'',
                'sale_id' => !empty($request->input('sale_id'))?$request->input('sale_id'):$this->args['userinfo']['id'],
                'mainvat' => $mainvat ,
                'maindiscount' => $maindiscount,
                'accno_discount' => !empty($request->input('accno_discount'))?$request->input('accno_discount'):0,
                'inv_cycle' => !empty($request->input('inv_cycle'))?$request->input('inv_cycle'):1,
                'po_id' => !empty($request->input('qoute_id'))?$request->input('qoute_id'):0,
                'trash' =>'no',
                'tags' => json_encode($tag),
                'xch_rate' =>json_encode(
                    [   'ccy_id'=>config('currencyinfo.ccy_id'), 
                        'subccy_id'=>config('currencyinfo.subccy_id'),
                        'rateinuse'=>config('currencyinfo.rateinuse'), 
                        'rateoutuse'=>config('currencyinfo.rateoutuse')
                    ]),
                'gtotal' => $gtotal[1],
                'paid' =>0,
                'add_date' => date("Y-m-d"),
                'blongto' => $this->args['userinfo']['id']
            
        ];

       
        if($isupdate)
        {
            $tableData = array_except($tableData, [$this->fprimarykey, 'xch_rate', 'yearid', 'vat_id', 'branch_id', 'trash', 'add_date', 'blongto']);
        }

        /*For Save POS*/
        $pay_amount =0;
        if($request->has('pay_amountusd'))
        {
                $pay_amountusd = (float)$request->input('pay_amountusd');
                $pay_amountnative = (float)$request->input('pay_amountnative')??0;
                if(!empty($pay_amountnative))
                {
                    $pay_amountnative = $pay_amountnative/config('currencyinfo.rateinuse'); 
                    $pay_amountusd = $pay_amountusd + $pay_amountnative;
                }
                $pay_amount =$pay_amountusd;
                 
        }

        return ['tableData' => $tableData, 'subtableData' => $subtableData, 'id'=>$newid, 'pay_amount'=>$pay_amount];
        

    }/*../function..*/


    public function delete(Request $request, $id=0)
    {
    
        $obj_info=$this->obj_info;
        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $input = $this->model->where($this->fprimarykey, (int)$editid)->where('trash', '!=', 'yes')->get(); 

        $route=redirect()->back()->getTargetUrl();

        if($input->isEmpty())
        {
            return [
                    'act' => false,
                    'url' => $route,
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid'),
                                    'id' => $editid
                                ]
                ]; 
        }
        

        $input = $input->toArray()[0];

        

        if((float) $input['paid']>0)
        {
            return [
                    'act' => false,
                    'url' => $route,
                    'passdata' => [
                                    'errors' => __('ccms.cannotvoid'),
                                    'id' => $editid
                                ]
                ]; 
        }
      
        /*check stock and delete*/
        /*if stock is use...cannot delete this purchase*/
        $get_addstockinfo = $this->addstock->model->where('pchase_id', (int)$editid)->select('*');
        if($get_addstockinfo){
          $with_tracking = $get_addstockinfo;
          $get_stocktoarray = $get_addstockinfo->get()->toArray();
          $with_tracking = $with_tracking->join('pos_stockadds', 'pos_stockadd.asm_id', '=', 'pos_stockadds.asm_id')
          ->join('pos_stocktracking', 'pos_stockadds.as_id', '=', 'pos_stocktracking.as_id');
          $with_tracking = $with_tracking->get()->toArray();
          
          if(count($with_tracking)>0){
            return [
                    'act' => false,
                    'url' => $route,
                    'passdata' => [
                                    'errors' => __('ccms.stockisused'),
                                    'id' => $editid
                                ]
                ]; 
          }
        }
        
        


        $tags = $input['tags'];
        $tags = json_decode($tags, true);
        $tnote = $request->input('tnote');
        $tags['voidnote'] = $tnote;
        $tags = json_encode($tags);
        
        $delete = $this->model->where($this->fprimarykey, (int)$editid)->where('branch_id', $this->args['userinfo']['branch_id'])->update(['trash'=>'yes', 'tags'=>$tags]); 
       
        //return redirect()->back()->with('success', 'delete oK');
        if(!$delete)
        {

            return [
                    'act' => $delete,
                    'url' => $route,
                    'passdata' => [
                                    'errors' => __('ccms.rqnvalid'),
                                    'id' => $editid
                                ]
                ]; 


        }
      
        //deleteStock
        if(!empty($get_stocktoarray)){
//           $get_stocktoarray = $get_stocktoarray[0];
//           $asm_id = $get_stocktoarray['asm_id'];
//           $deletestock = $this->addstock->model->where('asm_id',$asm_id)->delete();
//           $deletesubstock = $this->addstock->submodel->where('asm_id',$asm_id)->delete();
          $updatestock = $this->addstock->model->where('pchase_id', (int)$editid)->update(['trash'=>'yes']);
        }
        
        // GJ
        $gj_del = gj_totrash('pch', $editid);
        
                
               
        return [
                    'act' => $delete,
                    'url' => $route,
                    'passdata' => [
                                    'success' => __('ccms.suc_void'),
                                    'id' => $editid
                                ]
                ]; 

    } /*../function..*/

    public function restore(Request $request, $id=0)
    {
        return null;

    } /*../function..*/


    public function destroy(Request $request, $id=0)
    {
        return null;

    } /*../function..*/


    public function duplicate(Request $request, $id=0)
    {
        return null;

    } /*../function..*/ 

    

    public function invlookup(Request $request,$internalid=0)
    {
        $qry = $request->input('query');
        $findid = $request->input('findid');
        $callback = $request->input('callback')??'gettingIdTitle';
      

        if(!empty($internalid)) $qry = $internalid;
        
        $inv_info = [

                            'pch_id'        => '', 
                            'branch_id'     => 0,
                            'accno_id'      => 0,
                            'accno_discount'=> 0,
                            'inv_date'      => '',
                            'gtotal'        => formatMoney(0),
                            'paid'          => '',
                            'invbalance'    => formatMoney(0),
                            'cm_code'       => '',
                            'supplier_id'         => 0,
                            'latinname'     => '',
                            'nativename'    => '',
                            'pay_amount'    =>formatMoney(0),
                            'refund_amount' => formatMoney(0),
                            'stage'         => '',
                            'trash'         => '',
                            'mainvat'  => 0,
                            'maindiscount'  => 0,
                            'maindiscount_original' => 0,
                            'gtotalnoformat'        => 0,

        ];
        //dd($qry);
        if(!empty($qry))
        {
            $qry = (int)$qry??0;
            $results = $this->listingModel();
            $results = $results->where($this->tablename.'.'.$this->fprimarykey, '=', $qry)->get()->toArray();

                //dd($results);
                if(!empty($results))
                {
                    $results = $results[0];
                    $balance = formatMoney($results['balance']);
                    $inv_info = [
                            'pch_id'        => $results['id'], 
                            'branch_id'     => $results['branch_id'],
                            'accno_id'      => $results['accno_id'],
                            'accno_discount'=> $results['accno_discount'],
                            'inv_date'      => date("d-m-Y", strtotime($results['inv_date'])),
                            'gtotal'        => formatMoney($results['gtotal']),
                            'paid'          => $results['paid'],
                            'invpaid'       => formatMoney($results['paid']),
                            'invbalance'    => $balance,
                            'cm_code'       => $results['cm_code'],
                            'supplier_id'   => $results['supplier_id'],
                            'latinname'     => $results['latinname'],
                            'nativename'    => $results['nativename'],
                            'pay_amount'    => $balance,
                            'refund_amount' => formatMoney($results['paid']),
                            'stage'         => $results['stage'],
                            'trash'         => $results['trash'],
                            'mainvat'  => $results['mainvat'],
                            'maindiscount'  => $results['maindiscount'],
                            'maindiscount_original' => $results['maindiscount'],
                            'gtotalnoformat'        => $results['gtotal'],

                        ];
                }
            
            


        }
        


        

        $return = [
                    'callback' => $callback,
                    'container' => '',
                    'data' => $inv_info,
                    'message' => ''
                ];

        return json_encode($return);
    }


    public function RetrieveSubForm($model, $editid, $dflang)
    {
        /*Subform ===> Shall be a function*/
        $input = [];
        $products = $model
        ->leftJoin('cms_product','pos_purchases.pd_id', '=', 'cms_product.pd_id')
        ->leftJoin('cms_unit', 'cms_unit.unt_id', '=', 'cms_product.unt_id')
        ->selectRaw(
            "pchd_id as subid,
            pch_id,
            pos_purchases.pd_id as id,
            imginfo,sizes,
            colors,madewith,
            description as title,
            pos_purchases.qty as qty,
            qty_tostock,
            qty_total,
            cost,
            subdiscount,
            subvat,
            cycle,
            pos_purchases.ordering,
            amount,
            subnote,
            batch,
            product_expdate,
            JSON_UNQUOTE(cms_unit.title->'$.".$dflang."') AS unit
            "
        )->where('pch_id',$editid)->orderby('pos_purchases.pchd_id', 'asc')->get()->toArray();
        
        //dd($products);
        if(empty($products)) return [];
        foreach ($products as $record) {
            
            $subproduct[] = (object)$record;

            $subid[] = $record['subid'];
            $subpd_id[] = $record['id'];

            $description[] = $record['title'];
             $unit[] = $record['unit'];
            $cost[] = $record['cost'];
            if($record['subdiscount']<0){
                $subdiscounttype[] = -1;
            }
            else{
                $subdiscounttype[] = 1;
            }
            $subdiscount[] = $record['subdiscount'];

            $subvat[] = $record['subvat'];
            $cycle[] = $record['cycle'];
            $subnote[] = $record['subnote'];
            $qty_tostock[] =$record['qty_tostock'];
            $qty_tostock_input = json_decode($record['qty_tostock'], true);
            $qty_total[] =$record['qty_total'];
            foreach($qty_tostock_input as $key => $val){
              $input['txt_qty'.$record['id'].'-'.$key] = $val;
            }

        }
        
        $input['subproduct'] = $subproduct;
        $input['subid'] = $subid;
        $input['subpd_id'] = $subpd_id;
        $input['description'] = $description;
        $input['subunit'] = $unit;
        $input['unit'] = $unit;
        $input['subdiscount'] = $subdiscount;
        $input['subdiscounttype'] = $subdiscounttype;
        $input['subvat'] = $subvat;
        $input['cycle'] = $cycle; 
        $input['subnote'] = $subnote; 
        $input['subcost'] = $cost; 
        $input['qty_tostock'] = $qty_tostock;
        $input['qty_total'] = $qty_total;
        return $input;
    }


    public function preview(Request $request, $id=0){

        $obj_info=$this->obj_info;
        $default=$this->default();

        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];
      
        $users=$this->users->listingModel($this->dflang[0])->pluck('name', 'id')->where('trash', '!=', 'yes');
        $users = json_decode(json_encode($users), true);
        
        #Retrieve Data#
        if (empty($id))
        {
            $requestid = $this->args['routeinfo']['id'];
        }
        else
        {
            $requestid = $id;
        }



        $data = [
            'pageinfo' => General::find(1)->toArray(), // for whole page setting like header or footer
        ];
      
        $dbname = config('ccms.backend');
        $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.'.$dbname.'_voucher';
        if (!view()->exists($blade))
        {
            $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.voucher';
        }

        $tableData = [];
        $returntype = '';
        if($request->session()->has('input'))
        {

            $input = $request->session()->get('input');
            $tableData = $input['tableData'];
            $tableData['id'] = $tableData['pch_id'];
            $tableData['balance'] = $tableData['gtotal'] - $tableData['paid'];
            $cm_id = $tableData['cm_id'];
            
            $customer = Customer::where('cm_id', $cm_id)->first()??[];
            if(!empty($customer))
            $customer = $customer->toArray();
            $tableData = array_merge($tableData, $customer);
            
            //dd($tableData);
            $returntype = $request->session()->get('returntype');
           


        }
        elseif($request->has('requestid') || $requestid)
        {
            $invoiceid = $request->input('requestid');
            $invoiceid = empty($invoiceid )?$requestid : $invoiceid;
            $results = $this->listingmodel()->where($this->fprimarykey, $invoiceid)->get()->toArray()??false;

            if($results)
            {
                $tableData = $results[0];
                $subform = $this->RetrieveSubForm($this->submodel, $invoiceid, $this->dflang[0]);
                
                if(isset($subform['subproduct'])){
                    foreach ($subform['subproduct'] as $key => $value) {

                        $products[] = (array)$value;
                    }
                    $input['subtableData'] = $products;
                  
                }
                
            }
                
        }

        if(($requestid || $returntype=='previewpos') && !$request->has('invpreview')) /*../invoice/preview/$requestid---*/
        {
            $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.'.$dbname.'_receipt'; 
           if (!view()->exists($blade))
            {
                $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.receipt';
            }
        }
        
        $previewdata = '';
        if(!empty($tableData))
        {
            $previewdata= view($blade)
            ->with(['tableData' => (object)$tableData])
            ->with(['subtableData' => $input['subtableData']??[]])
            ->with(['pageinfo' => $data['pageinfo']])
            ->with(['allsizes' => $allsizes])
            ->with(['allcolors' => $allcolors])
            ->with(['users'  => $users])
            ->with(['preview'=>'html'])
            ->with(['returntype' => $returntype])
            ;

          /*
          |befor Save Preview
          */
          if($returntype=='previewpos' || $returntype== 'previewinv')
          {
              $path = resource_path('views/backend/v'.$this->obj_info['name'].'/previewdoc/'.$dbname.'_invoicepre.html');
              \File::put($path,$previewdata);
              
              $data = [
                    'id' => $id,
                    'onlypreview' => 'yes', /*no need to load browser PRINT Screen*/
                    'preview' =>url_builder($obj_info['routing'],[$obj_info['name'],'iframepreview/'],[])
                  ];
      
              $return = [
                          'callback' => 'savesuccess',
                          'container' => '',
                          'data'  => $data,
                          'message' => '',
                          'success' => ''
                      ];

              return json_encode($return);
            
            }
            /*..end/if..*/
            
            /*
            |after Saved Preview
            */
            return $previewdata;
          
            
        }
       


        $errors = __('ccms.rqnvalid');
        if ($request->session()->has('errors')) {
              $errors = $request->session()->get('errors');
        }
      
        return view('backend.widget.noaction',
                    compact('obj_info')
                )->with(
                    [
                        
                        'caption' => $errors
                    ]
                ); 
        
        

    }

    public function iframepreview()
    {
        $dbname = config('ccms.backend');
        $path = resource_path('views/backend/v'.$this->obj_info['name'].'/previewdoc/'.$dbname.'_invoicepre.html');
        if(\File::exists($path)){
            return \File::get($path);
        } else {  echo "not exists";   }
            
    }

    public function pdfgenerate(Request $request, $id=0){

        #Retrieve Data#
        if (empty($id))
        {
            $requestid = $this->args['routeinfo']['id'];
        }
        else
        {
            $requestid = $id;
        }
        
        $obj_info=$this->obj_info;
        $default=$this->default();

        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];
        
        $users=$this->users->listingModel($this->dflang[0])->pluck('name', 'id')->where('trash', '!=', 'yes');
        $users = json_decode(json_encode($users), true);
        
        $data = [
            'pageinfo' => General::find(1)->toArray(), // for whole page setting like header or footer
        ];

        $dbname = config('ccms.backend');

        $pdftitle = config('sysconfig.inv').formatid($requestid);
        if($request->has('type'))
        {
            $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.'.$dbname.'_receipt'; 
           if (!view()->exists($blade))
            {
                $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.receipt';
            }

            $format = config('sysconfig.pdfformat2');
            $format['format'] = explode(',', $format['format']);
            if(empty($format))
            $format = [
                                'mode'                 => '',
                                'format'               => array(80,1440),
                                'orientation'          => 'P',
                                'margin_left'          => 10,
                                'margin_right'         => 10,
                                'margin_top'           => 0,
                                'margin_bottom'        => 20,
                                'margin_header'        => 0,
                                'margin_footer'        => 10,
                               
                                
                        ];

            $format['title'] = $pdftitle;

        }
        else
        {
           $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.'.$dbname.'_voucher'; 
           if (!view()->exists($blade))
            {
                $blade = 'backend.v'.$this->obj_info['name'].'.printingdoc.voucher';
            }

            $format = config('sysconfig.pdfformat0');
            if(empty($format))

            $format = [
                                'mode'                 => ''
                                //'format'               => array(80,1440),
                               
                                
                        ];
            $format['title'] = $pdftitle;
        }
        

        

        $results = $this->listingmodel()->where($this->fprimarykey, $requestid)->get()->toArray()??false;
        $products = [];
        if($results)
        {
            $results = $results[0];
            $subform = $this->RetrieveSubForm($this->submodel, $requestid, $this->dflang[0]);
            
            foreach ($subform['subproduct'] as $key => $value) {
                
                $products[] = (array)$value;
            } 

            //start pdf
            //dd($products);
                $data = array_merge($data,
                    ['tableData' => (object)$results], 
                    ['subtableData'=>$products],
                    ['allsizes' => $allsizes],
                    ['allcolors' => $allcolors],
                    ['users' => $users],
                    ['preview'=>'pdf']
                );
                //dd($data);
                //dd($format);
                $pdf = PDF::loadView($blade, $data,[],$format);

                $option = $request->input('option');

               if($option==1)
                    return $pdf->stream($pdftitle.'.pdf');
                else
                    return $pdf->download($pdftitle.'.pdf');
                
            //end
        }
        
        return 'NO DATA';


        
        
    }/*../function..*/ 


    public function validerror(Request $request)
    {
        if ($request->session()->has('errors')) {
            $errors = $request->session()->get('errors');
        }

        
        
        $return = [
                    'callback' => '',
                    'container' => '',
                    'data' => '',
                    'message' => '',
                    'errors' => $errors
                ];

        return json_encode($return);
    }/*../function..*/


    public function saveposnext(Request $request)
    {
      
        $obj_info = $this->obj_info;
        if ($request->session()->has($this->fprimarykey)) {
            $id = $request->session()->get($this->fprimarykey);
            $success = __('ccms.suc_save');
            
        }
        elseif($request->has('invid'))
        {
          $id = $request->input('invid');
          $success = '';
        }
        $data = [
                  'id' => $id,
                  'preview' =>url_builder($obj_info['routing'],[$obj_info['name'],'preview/'.$id],['option'=>1, 'type'=>'rc'])
            ];
      
        $return = [
                    'callback' => 'savesuccess',
                    'container' => '',
                    'data'  => $data,
                    'message' => '',
                    'success' => $success
                ];

        return json_encode($return);


    }/*../function..*/
  
  
  function checkstock($request, $obj_info, $preview, $possave,$filifo, $productinfo)
  {
    if($filifo['lastorderqty']>0 && $productinfo['avgcost']==1)
    {

        $pname = $productinfo['title'];
        /*
        | For Preview
        */
        if($preview){
          $routing=url_builder($obj_info['routing'],[$obj_info['name'],'preview']);
          return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => ['errors' => $pname.', '.__('ccms.nostock')]
                ];
         }

        /*
        | For POS
        */
        if ($possave) 
        {
          $routing=url_builder($obj_info['routing'],[$obj_info['name'],'validerror']);
          return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => ['errors' => $pname.', '.__('ccms.nostock')]
                ];
        }

        /*
        | For Invoice
        */
        $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
        $subpd_id = $request->input('subpd_id');
        $prodctsarray=[];
        if(count($subpd_id)>1)
        {
            array_pop($subpd_id);
            $prodctsarray=productsubform($subpd_id, new Product, $this->dflang); 
        }                 

        return [
                  'act' => false,
                  'url' => $routing,
                  'passdata' => [
                                  'errors' => $pname.', '.__('ccms.nostock'),
                                  'input' => $request->input(),
                                  'products' => $prodctsarray,
                                  'submitto' => 'create'
                              ]
              ];

        /*
        | END
        */


    }
    return 'go';
     
    
  }/*../function..*/
  
  
   public function indexapi(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $args = $this->args;


        #DEFIND MODEL#
        $results = $this->listingmodel();

        $sfp = $this->sfp($request, $results);
         
        $results = $sfp['results'];
          //dd($results);
        $total =$totalpaid=$totalbalance=0;
        $statuscolor ='';
        foreach($results as $row){

            #gtotal
            $gtotal = $row->gtotal;
            $gtotalformat = formatmoney($gtotal, true);

            #paid
            $paid = $row->paid;
            $paidformat = formatmoney($paid, true);	
            
            #balance
            $balance = $row->balance;
            $balanceformat = formatmoney($balance, true);	

            $row->idformat = config('sysconfig.inv').formatID($row->id);
            $row->inv_date = date("d-m-Y", strtotime($row->inv_date));
            $row->title = html_entity_decode(html_entity_decode($row->title));
            $row->latinname = $row->latinname??__('ccms.retailer');
            $row->gtotalformat = $gtotalformat;
            $row->paidformat = $paidformat;
            $row->balanceformat = $balanceformat;
            
            if($row->balance>0 && $row->trash!='yes')
            {
              $status = __('ccms.unpaid');
              $statuscolor ='0xFFF89406';
            }
            elseif($row->balance==0 && $row->trash!='yes')
            {
              $status = __('ccms.paid');
              $statuscolor ='0xFF82AF6F';
            }
            elseif($row->trash=='yes')
            {
              $status = __('ccms.void');
              $statuscolor ='0xFFD15B47';
            }
					  $row->status = $status;		
            $row->statuscolor = $statuscolor;
            
            if($row->trash!='yes'){
              $total+= $row->gtotal;
              $totalpaid+= $row->paid;
              $totalbalance+=$row->balance;
            }
            
            



          }
       
          return response()->json([
              'userinfo'  => array_except($args['userinfo'],['pwd']),
              'obj_info' => $obj_info,
              'results' => $results
          ]);


    } /*../function..*/
    
    public function reportdownload(Request $request, $id)
    {
        if (empty($id))
        {
            $requestid = $this->args['routeinfo']['id'];
        }
        else
        {
            $requestid = $id;
        }
      
        $obj_info=$this->obj_info;
        $default=$this->default();        

        #DEFIND MODEL#
        $data = [];
        $branch_cond = branch_validate_query($this->args['userinfo'], $this->tablename.'.branch_id');
        $master = $this->model->where($this->fprimarykey, (int)$id)
          ->whereRaw("trash <> 'yes' $branch_cond")->first();
        $subMaster = $this->RetrieveSubForm($this->submodel, $master->{$this->fprimarykey}??0, $this->dflang[0]);
        
        $data = array_merge($data,
           ['master' => $master], 
           ['subMaster' => $subMaster]
        );
        $filename =$obj_info['title'].".xls";
       
        $blade = get_view_by_db_name($this->obj_info['name'], 'download');
        return view($blade)
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($data)
                ->with(['caption' => $filename]);
      
    } /*../function..*/
  
    /*============================= Start Import Stuff*/
    public function airimport(Request $request)
    {
        $obj_info=$this->obj_info;

        $default=$this->default();
        $js_config = $default['js_config'];
        
        return view('backend.v'.$this->obj_info['name'].'.import',
                    compact('obj_info',
                            'js_config'
                            )


                )->with(
                    [
                        'submitto'  => 'storeimport',
                        'fprimarykey'     => $this->fprimarykey,
                        'caption' => __('ccms.import')
                    ]
                );
    } /*../function..*/
  
    public function loadimportdata(Request $request){
      if ($request->isMethod('post'))
      {
        $obj_info=$this->obj_info;
        $validator = Validator::make($request->all(), [
            'file_import' => 'required|mimes:xls,xlsx,xlsm'
        ]);
        if ($validator->fails()) {
          return response()->json([
                      'act' => false,
                      'obj_info' => $obj_info,
                      'input' => $request->input(),
                      'errors' => $validator->errors()->first(),
                  ]);
        }
        
        $default=$this->default();
        $js_config = $default['js_config'];
        
        $args = $this->args;
        $data = $this->importsetinfo($request);
        $supplier_id = $data['tableData']['supplier_id'];
        $supplier = Supplier::where('supplier_id', $supplier_id)->select('*')->first()->toArray();
        
        //Try validation here
        $validator = $this->importvalidation($data);
         if ($validator->fails()) {
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'import']);
                return [
                    'act' => false,
                    'url' => $routing,
                    'errors' => $validator->errors()->first(),
                ];
          }
        //

        return view('backend.v'.$this->obj_info['name'].'.importlist')->with([
          'results'=> $data['spreadsheet'],
          'tableData' => $data['tableData'],
          'id' => $data['id'],
          'supplier' => $supplier,
          'args' => $args
        ]); 
      }
    }
  
    /**/
    public function storeimport(Request $request){
      if ($request->isMethod('post'))
      {
        $obj_info=$this->obj_info;
        $validator = Validator::make($request->all(), [
            'file_import' => 'required|mimes:xls,xlsx,xlsm'
        ]);
        if ($validator->fails()) {
          return response()->json([
                      'act' => false,
                      'obj_info' => $obj_info,
                      'input' => $request->input(),
                      'errors' => $validator->errors()->first(),
                  ]);
        }

        $data = $this->importsetinfo($request);
        //Try validation here
        $validator = $this->importvalidation($data);
         if ($validator->fails()) {
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'import']);
                return [
                    'act' => false,
                    'url' => $routing,
                    'errors' => $validator->errors()->first(),
                ];
          }
        $tableData = $data['tableData'];
        
        $savedata = false;
        if(!empty($tableData)){
          //$savedata = $this->model->insert($tableData);
        }
        $success_ms = __('ccms.suc_save');

        if($savedata){
          
           $products = Product::all();
           if($products->count()){
             foreach($products as $product){
               
              foreach (config('ccms.multilang') as $lang)
              {
                \DB::table('cms_productdetail')->insert([
                  'pd_id' => $product->pd_id,
                  'lg_code' => $lang[0],
                  'translate' => json_encode([
                    'des' => '',
                    'metatitle' => '',
                    'metakeyword' => '',
                    'metades' => '',
                  ])
                ]);
              }
               
             }
           }
          
           $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
           $return = [
                  'callback' => 'reloadImportData',
                  'status' => $savedata,
                  'container' => '',
                  'success' => $success_ms,
                  'url' => $routing,
              ];

          return json_encode($return);
        }
        
        return response()->json([
            'act' => false,
            'obj_info' => $obj_info,
            'input' => $request->input(),
            'errors' => $validator->errors()->first(),
        ]); 
      }

    }
  
  /**/
  
  public function importvalidation($data){
      
        $tableData = $data['tableData'];
        $add_ondata = ['wh_id' => (int)$this->args['userinfo']['wh_id']??0];
        
        $rules = [
                        'title'      => 'required',
                        'inv_date'   => 'required',
                        'supplier_id' => 'required',
                        'accno_id' => 'required|numeric',
                        'wh_id'     => 'required|numeric',                        
                    ];
    
    
        $subtableData = $data['subtableData'];
    
        $count_record = count($subtableData);
        for($i=0; $i<$count_record; $i++){
          
          $rules[$i.'.description'] = 'required';
          $rules[$i.'.pd_id'] = 'required|numeric|gt:0';
          $rules[$i.'.qty_total'] ='required|numeric|gt:0';
          $rules[$i.'.cost'] ='required|numeric|gt:0';
          $rules[$i.'.product_expdate'] ='required';
           
        }
        
        $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                  
                'required' => 'The :attribute '. __('ccms.fieldreqire'),
                 'distinct' => __('ccms.fielddistinct').'aa'.'The :attribute field can not be blank.',
                'unique' => __('ccms.fieldunique').'The :attribute field can not be blank.',
                  'numeric' => 'The :attribute field can not be numeric.',
                'gt' => 'The :attribute field can not be gt.'
            ];
        
        $validator =Validator::make(array_merge($tableData,$add_ondata, $subtableData), $rules, $validatorMessages);
        return $validator;
    }
    
    /**/
  
    public function importsetinfo($request){
        
        #loading and convert data to array from excel file
        /*
        $spreadsheet = IOFactory::load($request->file('file_import'));
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $collectSheet = collect(array_values($sheetData));
        */
        
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('file_import'));
        $reader->setLoadSheetsOnly(["Purchase"]);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file_import'));
        //$spreadsheet->getSheet(0);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $collectSheet = collect(array_values($sheetData));
      
        $collectSheet = $collectSheet->forget(0);// remmove first row
        $collectSheet = collect($collectSheet->values()->all()); // refill array key index
        $collectSheet = $collectSheet->where('A', '!=', null);
      
        
        #table data format
        $main_data = $collectSheet->first();
        $tableData = [];
        $newid = $this->model->max($this->fprimarykey)+1;
        $inv_date=(isset($main_data['D']) && !empty($main_data['D'])) ? date('Y-m-d', strtotime($main_data['D'])) : date('Y-m-d');
        $yearid=$this->model->whereRaw('YEAR(inv_date)='.date('Y'),strtotime($inv_date))->max('yearid')+1;
        $vat_id=$this->model->whereRaw('mainvat>0')->max('vat_id');
        if(!empty($vat_id)) $vat_id+=1;
       
        
        
        $due_date = (isset($main_data['E']) && !empty($main_data['E'])) ? date('Y-m-d', strtotime($main_data['E'])) : date('Y-m-d');
      
        $tableData = [
            
                $this->fprimarykey => $newid,
                'yearid' => $yearid??0,
                'vat_id' => $vat_id??0,
                'supplier_id' => $main_data['B'] ?? 0,
                'accno_id' => (int)$main_data['C'] ?? 0,
                'branch_id'   => !empty($this->args['userinfo']['branch_id'])?$this->args['userinfo']['branch_id']:0,
                'title' => $main_data['A'] ?? 0,
                'stage' =>0,
                /*0->normal, 1->returned*/
                'inv_date' => $inv_date,
                'due_date' => $due_date,
                'fter_note' => '',
                'prv_note' => '',
                'sale_id' => $main_data['F'] ?? 0,
                'mainvat' => 0 ,
                'maindiscount' => 0,
                'accno_discount' => 0,
                'inv_cycle' => 1,
                'po_id' => 0,
                'trash' =>'no',
                'tags' => '',
                'xch_rate' =>json_encode(
                    [   'ccy_id'=>config('currencyinfo.ccy_id'), 
                        'subccy_id'=>config('currencyinfo.subccy_id'),
                        'rateinuse'=>config('currencyinfo.rateinuse'), 
                        'rateoutuse'=>config('currencyinfo.rateoutuse')
                    ]),
                'gtotal' => 0,
                'paid' =>0,
                'add_date' => date("Y-m-d"),
                'blongto' => $this->args['userinfo']['id']
            
              ];
            
        
        $collectSheet = $collectSheet->forget(0);
        $collectSheet = $collectSheet->forget(3);
        $subtableData=[];
        if($collectSheet->count() > 0){
          $subpd_id = [];
          $subsize = [];
          $subcolor = [];
          $subqty = [];
          $subcost = [];
          foreach($collectSheet as $row){
            $pd_id = $row['A']??0;
            $qty = $row['C']??0;
            $cost = $row['D']??0;
            array_push($subpd_id, $pd_id);
            array_push($subsize, 0);
            array_push($subcolor, 0);
            array_push($subqty, $qty);
            array_push($subcost, $cost);
           
            //txt_qty1-0-0
            $request->request->add(['txt_qty'.$pd_id.'-0-0' => $qty]);
          }
          array_push($subpd_id, 0);
          $request->request->add(
            [
              'subpd_id'=>$subpd_id,
              'subsize' => $subsize, 
              'subcolor'=>$subcolor, 
              'subqty'=>$subqty,
              'subqty'=>$subqty,
              'subcost' => $subcost
            ]
            );
          $subform = setSubform($request, new Product, $this->fprimarykey, 'pchd_id', $newid, $this->dflang, true);
          
         
          $subtotal=0;
          $i=0;
          foreach($collectSheet as $row){
            $qty = $subform['subtable'][$i]['qty'];
            $qty_tostock = $subform['subtable'][$i]['qty_tostock'];
            $qty_total = $subform['subtable'][$i]['qty_total'];

            $subdiscount = $row['E']??0;
            
            $subcost = $row['D']??0;

            $subvat = 0;
            $amount = calAmount($subcost, $qty_total ,$subdiscount, $subvat);
            $subnote ='';
            $subtotal= $subtotal + $amount[1];
            $record = [
                                
                        'pchd_id' => 0, //!empty($request->input('subid')[$i])?$request->input('subid')[$i]:0,
                        'pch_id' => $newid,
                        'pd_id' => $row['A']??0,
                        'description' => $row['B'] ?? 0,
                        'qty' => $qty,
                        'qty_tostock' => $qty_tostock,
                        'qty_total' => $qty_total,
                        'cost' => $subcost,
                        'subdiscount' => $subdiscount,
                        'subvat' => $subvat,
                        'cycle' => 'false',
                        'ordering' =>0,
                        'amount' => $amount[1],
                        'subnote' => $subnote,
                        'batch'=> $row['F']??'',
                        'product_expdate'  => (isset($row['G']) && !empty($row['G'])) ? date('Y-m-d', strtotime($row['G'])) : date('Y-m-d')
                                
                    ];
            array_push($subtableData, $record);
            $i++;
          }
          
          
          $gtotal = calAmount($subtotal, 1 ,0, 0);
          $tableData['gtotal'] = $gtotal[1];
        }
        
        #end table data format
        //dd($tableData);
        //return ['tableData' => $tableData, 'subtableData' => $subtableData, 'id'=>$newid, 'pay_amount'=>$pay_amount];
        return [
          'tableData' => $tableData,
          'subtableData' => $subtableData,
          'id' => $newid,
          'pay_amount' => 0,
          'main_data' => $main_data,
          'spreadsheet' => $collectSheet,
        ];
    }
  
    
}