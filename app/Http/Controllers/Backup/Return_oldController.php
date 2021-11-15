<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
use PDF;
//use Illuminate\Validation\Rule;

use App\Models\Backend\Rpayment;
use App\Models\Backend\Paymentmethod;
use App\Models\Backend\Accountno;
use App\Models\Backend\Invoices;
use App\Models\Backend\Adjuststock;
use App\Models\Backend\Adjuststocks;
use App\Models\Backend\Addstock;
use App\Models\Backend\Addstocks;

use App\Models\Backend\Stock;
use App\Models\Backend\Product;
use App\Models\Backend\Stocktracking;

use App\Models\Backend\Size;
use App\Models\Backend\Color;


use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\InvoiceController;

class Return_oldController extends Controller
{
    private $args;
    private $model;
    private $fprimarykey='rp_id';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'return','title'=>'Invoice Return','routing'=>'admin.controller','icon'=>'<i class="fa fa-undo" aria-hidden="true"></i>'];

    private $protectme;

    private $users;
    private $invoice;

    private $tablename;
    private $invoices;
    private $adjuststock;
    private $addstock;


    public function __construct(array $args){ //public function __construct(Array args){
        $this->obj_info['title'] = __('label.lb90');
        $this->protectme = [  config('ccms.protectact.create')

                        ];

        $this->args = $args;
        $this->model = new Rpayment;
        $this->tablename = $this->model->Gettable();
        $this->dflang = config('ccms.multilang')[0];

        $this->users = new UserController($args);
        $this->invoice = new InvoiceController($args);
        $this->invoices = new Invoices;

        $this->adjuststock = new AdjuststockController($args);
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
        

        $users=$this->users->listingModel($this->dflang[0])->pluck('name', 'id')->where('trash', '!=', 'yes');
        $users = json_decode(json_encode($users), true);

        $paymentmethod = Paymentmethod::getlist($this->dflang[0]);

        $accountno = Accountno::where('trash', '!=', 'yes')->select('accno_id', 'title')->pluck('title', 'accno_id')->toArray();
        
        $sizes=Size::getsize($this->dflang[0])->pluck('title', 's_id');
        $colors=Color::getcolor($this->dflang[0])->pluck('title', 'cl_id');
        
    

        $js_config = [
            
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $js_config = array_merge($js_config);

        
        return ['js_config'=>$js_config, 
        'users' => $users,  
        'paymentmethod' => $paymentmethod,
        'accountno' => $accountno,
        'sizes' =>$sizes, 'colors'=>$colors
    ];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        
        return $this->model->selectRaw($this->fprimarykey." AS id,inv_id, tnote, pay_date, pay_amount, discount, receipt_no, tra_fee, trash");
    } /*../function..*/

    public function sfp($request, $results)
    {
        
    } /*../function..*/

    public function index(Request $request, $condition=[], $setting=[])
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
        $users = $default['users'];
        $allsizes = $default['sizes'];
        $allcolors = $default['colors'];
       
        
        $paymentmethod = $default['paymentmethod'];
        $accountno = $default['accountno'];
        $js_config = $default['js_config'];

        $args = $this->args;

        
        $requestinvid=false;
        $input=[];
        $subform = [];
        $inputaftervalidation=[];
        if($request->has('requestinvid')){
            $internalid = $request->input('requestinvid'); 
            $requestinvid=true;
        }
        elseif ($request->session()->has('input')) 
        {
            $inputaftervalidation = $request->session()->get('input');

            $internalid = $inputaftervalidation['inv_id'];
            $request->session()->forget('input');
        }
        

        
        $invinfo = $this->invoice->invlookup($request,$internalid);
        $invinfo = json_decode($invinfo,true);
        $input = $invinfo['data'];
        
        $subform = $this->invoice->RetrieveSubForm($this->invoices, $internalid, $this->dflang[0]);

        if(empty($subform))
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
        }


        
        $input = array_merge($input, $subform, $inputaftervalidation); 
        //dd($input);

        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'users',
                            'paymentmethod',
                            'accountno',
                            'args',
                            'input',
                            'requestinvid',
                            'allsizes',
                            'allcolors'
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
            $validator = $this->validation($request);
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);

            if ($validator->fails()) {
            
                
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];

            } 

            $data=$this->setinfo($request);

            

            /*Do more validation*/
            $inv_id = $data['tableData']['inv_id'];
            
            $invinfo = $this->invoice->invlookup($request,$inv_id);
            $invinfo = json_decode($invinfo,true);
            $invinfo = $invinfo['data'];

            $refund_amount = $data['tableData']['pay_amount'];

            if(empty($invinfo)){
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => __('ccms.invnotvalid'),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];
            }
            elseif($invinfo['branch_id']!=$this->args['userinfo']['branch_id']){

               return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => __('ccms.paidzero'),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ]; 
            }
            elseif($invinfo['paid']==0){

               return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => __('ccms.paidzero'),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ]; 
            }

            elseif($refund_amount>$invinfo['paid']){

               return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => __('ccms.rfgtpaid'),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ]; 
            }

            elseif($invinfo['trash']=='yes'){
                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => __('ccms.invnotvalid'),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];
            }

//             /*check Warehouse*/

//             $sadj_id =0;
//             $wh_id = 0;
//             $oldadjuststock = Adjuststock::where($this->invoice->fprimarykey,$inv_id)->get()->toArray()[0];
//             $sadj_id = $oldadjuststock['sadj_id'];
//             $wh_id = $oldadjuststock['wh_id'];

//             if((int)$wh_id!=$this->args['userinfo']['wh_id'])
//             {
//                 return [
//                         'act' => false,
//                         'url' => $route,
//                         'passdata' => [
//                                         'errors' => __('ccms.rqnvalid'),
//                                         'id' => $inv_id
//                                     ]
//                     ]; 
//             }

            /*check Return QTY*/
            $returnqtyall = $data['refund'];
            $subinvoice = $this->invoice->RetrieveSubForm($this->invoices, $inv_id, $this->dflang[0]);
            $subid = $subinvoice['subid'];
            $num = count($subid);

            for ($i=0; $i < $num; $i++) { 
                $oldqty = abs($subinvoice['subqty'][$i]);
                $returnqty = abs($returnqtyall[$subid[$i]]);
                if($returnqty>$oldqty){
                    return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => __('ccms.rtgtord'),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];
                }
            }
          
            $invoices = $subinvoice['subproduct'];
            $update= false;
            $stocktracking=[];
            
            foreach($invoices as $products)
            {
              
              
              $madewith = json_decode($products->madewith, true);
              $stock = json_decode($products->costdetail, true);
              $sub_id = $products->subid;
              $subqty = $products->subqty;
              $returnqty = abs($returnqtyall[$sub_id]);
              $costdetail[$sub_id]=$products->costdetail;
              //$cost[$sub_id] = $products->cost;
              
              /*
              
              |To do more
              -- cose for Service 
              --  cost for Madewith
              -- deduct madewith qty;
              
              */
            
               $restqty = $subqty - $returnqty;
               $costperitem = $products->cost/$subqty;
               $cost[$sub_id] = $costperitem *$restqty;
             
              
              foreach($stock as $product)
              {
                if(empty($product) || $returnqty<=0) break;
                $grandtotalcost=0;
                //if(config('sysconfig.costmethod')!='lifo')
                
                $product = array_reverse($product);
                
                foreach($product as $ind =>$row)
                {
                  //cost =2
                  //totalcost = 3
                  
                  list($key, $value) = array_divide($row);
                  if(!empty($madewith))
                  {
                    $madewith_key = $value[5].'-'.$key[1];
                    
                    $madewith_qty = $madewith[$madewith_key]??0;
                    $returnqty = $returnqty * $madewith_qty;
                  }
                  
                  
                 
                   if($value[1]>$returnqty)
                  {
                   
                     $updateto = $returnqty; //updateto is BACK-To-Stock
                     
                     $totalcost = $returnqty * $value[2];
                     $row[$key[1]] = $value[1]-$returnqty;
                     $row[$key[3]] = $value[3]-$totalcost;
                     $product[$ind] = $row;
                     
                     
                     $returnqty =0;
                  }
                  elseif($value[1]==$returnqty)
                  {
                    
                    $updateto = $returnqty;
                    $totalcost = $returnqty * $value[2];
                    $returnqty =0;
                    unset($product[$ind]);
                  }
                  else
                  {
                    
                    $returnqty = $returnqty - $value[1];
                    $updateto = $value[1];
                    $totalcost = $value[1] * $value[2];
                    unset($product[$ind]); 
                    
                  }
                  //
                  $update = Addstocks::where('as_id',$value[0])
                        ->update(['qty_inhand->'.$key[1]=>\DB::raw("JSON_EXTRACT(qty_inhand, '$.\"".$key[1]."\"')+".$updateto), 
                                  'qtytotal_inhand'=>\DB::raw("qtytotal_inhand+".$updateto)
                                 ]);
                  
                  $invformat = config('sysconfig.inv').formatid($inv_id);
                  array_push($stocktracking, 
                            [
                              'sttracking_id'=>0,
                              'as_id' => $value[0],
                              'pd_id' => $value[5],
                              'qty' => json_encode([$key[1]=>$updateto]),
                              'note' => json_encode(['actedby'=>'return', 'ref'=>$invformat]),
                              'type' => '1', /*0=deduct, 1= add*/
                              'track_date' => date('Y-m-d H:i:s'),
                              'blongto' => $this->args['userinfo']['id']
                            ]
                            );
                  //
                  $grandtotalcost+=$totalcost;
                  if($returnqty==0)
                  {
                    break;
                  }
                 
                  //

                }
                
                /*last foreach*/
                //update sub-invoice here
                //if(config('sysconfig.costmethod')!='lifo')
                $product = array_reverse($product);
                
                $costdetail[$sub_id] = json_encode([$product]);
                
                if(empty($madewith))
                {
                  
                  $cost[$sub_id] = $products->cost-$grandtotalcost;
                }
                
              }
              
              

            }
            
            if(!empty($stocktracking))
            {
              $savetrakcing = Stocktracking::insert($stocktracking);
            }
            
            
//             /*Start INSERT*/
//                 $savedata = $this->model->insert($data['tableData']);

//              /*REturn STOCK*/
//                 $request->request->add(['title' => 'Return-'.config('sysconfig.inv').formatid($inv_id)]);
//                 $request->request->add(['inv_id' => $inv_id]); 
//                 $request->request->add(['note_status' => 2]); 
//                 $request->request->add(['subqty' => $request->input('returnqty')]); 
                
//                 $master = $this->addstock->setmasterinfo($request, false);
                
//                 /*@helpers*/
//                 $subform = setSubform($request, new Product, 'asm_id', 'as_id', $master['newid'], $this->dflang, false);
//                 $subtableData = $subform['subtable'];
//                 $costtableDat = $subform['costtable'];
//                 $id_tostocks  = $subform['idtostock'];

//                 $proceedaddstock = [
//                     'subtableData' => $subtableData,
//                     'id_tostocks' => $id_tostocks,
//                 ];

//                 //dd($subtableData);
//                 /*strart*/
                
//                 /*
//                 $saveaddstock = $this->addstock->model->insert($master['tableData']);
//                 if($saveaddstock)
//                 {

//                     $saveaddstocksub = Addstocks::insert($subtableData);
//                     #@helpers
//                     stockProceed($proceedaddstock, $this->args, new Stock);
                    
//                 }
//                 */
//                 /*end*/
//             /*end*/

            /*Start Update invoice*/
                $subtotal=0;

               for ($i=0; $i < $num; $i++) { 
                    $id = $subid[$i];
                    $oldqty = abs($subinvoice['subqty'][$i]);
                    $returnqty = abs($returnqtyall[$id]);
                    $newqty = $oldqty - $returnqty;
                 
                    if($newqty<$oldqty)
                    {
                      $unitprice = abs($subinvoice['unitprice'][$i]);
                      $subdiscount = $subinvoice['subdiscount'][$i];

                      $subvat = abs($subinvoice['subvat'][$i]);
                      $amount = calAmount($unitprice, $newqty ,$subdiscount, $subvat);
                      $subtotal= $subtotal + $amount[1];

                      //if($newqty==0)
                      //$deletesubinv = $this->invoices->where('invd_id',$id)->delete();
                      //else
                      $updatesubinv = $this->invoices->where('invd_id',$id)
                        ->update(['subqty'=> $newqty, 
                                  'amount'=>$amount[1],
                                  'costdetail' => $costdetail[$id],
                                  'cost' => $cost[$id]
                                 ]);
                      
                    }
                    

               }

            $maindiscount = !empty($invinfo['maindiscount'])?$invinfo['maindiscount']:0;
            $mainvat = !empty($invinfo['mainvat'])?$invinfo['mainvat']:0;
            $gtotal = calAmount($subtotal, 1 ,$maindiscount, $mainvat);

            $updatefields= [
                    'stage' => 1,
                    'gtotal' => $gtotal[1],
                    'paid'=> $invinfo['paid']  - $refund_amount
                ];
            if($gtotal[1]==0){
                $updatefields['trash'] ='yes';
            }

            $updatepaid = $this->invoice->model->where('inv_id',$inv_id)->update($updatefields);
            
            /*End update*/

           

                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');

                     #when use ajax to SAVE
                    if ($request->session()->has('ajax_access')) {
                        $routing=url_builder($obj_info['routing'],[$obj_info['name'],$request->input('ajaxnext')]);
                        return [
                                        'act' => true,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id'],
                                                        'inv_id' => $inv_id,
                                                        'invgtotal' => $gtotal[1],
                                                        'invpaid' => ($invinfo['paid']  - $refund_amount),
                                                        'invbalance' => ($gtotal[1]-($invinfo['paid']  - $refund_amount)),
                                                        'invstatus' => empty($gtotal[1])?__('ccms.void'):'Unpaid'
                                                    ]
                                    ];
                    }
                    #end ajax SAVE



        } /*../if POST..*/

        
    } /*../function..*/


    public function validation($request, $isupdate=false){
        //dd($request->input());
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $update_rules= [ $this->fprimarykey => 'required'];
            $request->request->add(['branch_id' => $this->args['userinfo']['branch_id']??0]); 
            
            $rules = [
                        'inv_id'      => 'required|numeric|gt:0',
                        'refund_amount'      => 'required|numeric',
                        'pmethod_id'   => 'required|numeric|gt:0',
                        'pay_date'   => 'required',
                        'branch_id'   => 'required|numeric|gt:0',
                        
                        
                    ]; 
            

            /*SUB*/
            $subform = $request->input('subid');
            $numrecord = count($subform);
            for($i=0; $i<$numrecord; $i++)
            {
                    
                    
                    $rules['returnqty.'.$i] = 'required|numeric';
                         
                    
            }

            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'max' => __('ccms.madewith'),
                'gt' =>  __('ccms.fieldreqire'),//__('ccms.gt')
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
            $validator =Validator::make($request->input(), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){
        //$request->request->add(['variable' => 'value']); 
        //dd($request->input());
        $currencyinfo = $this->default['currencyinfo'];
        
        $newid=($isupdate)? $request->input($this->fprimarykey)  : $this->model->max($this->fprimarykey)+1;

        $pay_date=!empty($request->input('pay_date'))?date("Y-m-d", strtotime($request->input('pay_date'))):date("Y-m-d");
       
        $tableData = [
            
                $this->fprimarykey => $newid,
                'inv_id'    => $request->input('inv_id')??0,
                'branch_id' => $this->args['userinfo']['branch_id']??0,
                'pay_date' => $pay_date,
                'pay_amount' => abs($request->input('refund_amount'))??0,
                'discount' => !empty($request->input('discount'))?$request->input('discount'):0,
                'receipt_no' => $request->input('receipt_no')??'',
                'tra_fee' => $request->input('tra_fee')??0,
                'pmethod_id' => $request->input('pmethod_id')??0,
                'accno_id' => $request->input('accno_id')??0,
                'ccy_id' => config('currencyinfo.ccy_id')??0,
                'xchrate' => config('currencyinfo.rateoutuse'),
                'tnote' => !empty($request->input('tnote'))?$request->input('tnote'):'Refund, invoice '.config('sysconfig.inv').formatid($request->input('inv_id')),
                'rc_by' => $request->input('rc_by')??0,
                'approved_by' => $request->input('approved_by')??0,
                'type' =>1,
                /*0=>receive, 1 =>refund*/
                'add_date' => date("Y-m-d"),
                'trash' => 'no',
                'blongto' => $this->args['userinfo']['id']
            
        ];

        $refund=[];
        if ($request->has('subid'))
        {
            $subid = $request->input('subid');
            $numrecord=count($subid);

            for($i=0; $i<$numrecord; $i++)
            {
                $refund[$subid[$i]??"na.$i"] = !empty($request->input('returnqty')[$i])?$request->input('returnqty')[$i]:0;
            }
        }

        if($isupdate)
        {
            
            $tableData = array_except($tableData, [$this->fprimarykey,'branch_id', 'trash', 'blongto']);
        }

        
        return ['tableData' => $tableData, 'refund'=>$refund, 'id'=>$newid];
        

    }/*../function..*/


     public function ajaxreturn(Request $request){

        $newcustomer = [];
        if ($request->session()->has('rp_id')) {
            $id = $request->session()->get('rp_id');
            $inv_id = $request->session()->get('inv_id');
            $invgtotal = $request->session()->get('invgtotal');
            $invpaid = $request->session()->get('invpaid');
            $invbalance = $request->session()->get('invbalance');
            $invstatus = $request->session()->get('invstatus');
            $info = [
                'invgtotal'.$inv_id=>formatmoney($invgtotal,true),
                'invpaid'.$inv_id=>formatmoney($invpaid,true), 
                'invbalance'.$inv_id=>formatmoney($invbalance,true),
                'invstatus'.$inv_id=>$invstatus
            ];
            $success = $request->session()->get('success');
        }

        
        
        $return = [
                    'callback' => 'gettingIdTitle',
                    'container' => '',
                    'data' => $info,
                    'message' => $success
                ];

        return json_encode($return);
    }/*../function..*/



    
}