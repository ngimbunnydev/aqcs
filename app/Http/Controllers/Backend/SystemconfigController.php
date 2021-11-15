<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Image;
use Validator;

use App\Models\Backend\Systemconfig;
use App\Models\Backend\Product;

use App\Models\Backend\Paymentmethod;
use App\Models\Backend\Accountno;
use App\Models\Backend\Pcategory;


class SystemconfigController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='sycog_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'systemconfig','title'=>'System Setting','routing'=>'admin.controller','icon'=>'<i class="fa fa-sliders-h" aria-hidden="true"></i>'];

    private $protectme;


	public function __construct(array $args){ //public function __construct(Array args){
    $this->obj_info['title'] = __('label.lb38');
        $this->protectme = [  ['index', 'index','Update'],
                                ['update', 'index','Update']
                        ];

        $this->args = $args;
		$this->model = new Systemconfig;
        $this->dflang = config('ccms.multilang')[0];
      
        $editid = 1;

        $input = $this->model->where($this->fprimarykey, (int)$editid)->get(); 
        if($input->isEmpty())
        {
            $this->model->truncate();
            $herder_text = ['inv'=>'INV', 'qt'=>'QT', 'pch'=>'PCH', 'po'=>'PO', 'dl'=>'DL', 'iddigit'=>"6", 'rp'=>'AR', 'pp'=>'AP', 'exp'=>'EXP','addstock' => 'ADDS', 'adjuststock' => 'ADJS', 'je'=>'JE', 'rt'=>'RTN', 'rtpch' => 'RTN-PCH'];

            $info_tag = ['invtheme' => 't1', 'postheme'=>'t1','paymentinfo'=>'' ,'pospmethod_id'=>០, 'posaccno_id'=>1, 'acc_pcy'=>0, 'acc_re'=>0,'acc_salediscount'=>0, 'poswelcometext' => 'Welcome to i-POS', 'companyname'=>'Your Company', 'productnum'=>50, 'whnum'=>2, 'branchnum'=> 2 , 'posfor'=>'pos', 'catlabo'=>'', 'catimagery' => '', 'catservice' => ''];
            $yesnotag = ['p2ptransfer'=>'manual','madewithstock'=>'resource','usingextraprice'=>'no', 'df_dis'=>1, 'costmethod'=>'average', 'lowstock' => 5, 'requiredchkin'=>'no', 'ciosize'=>'a4', 'cioproduct'=>'yes', 'restaurant'=>'no', 'withcosting'=>'yes', 'customermode'=> 'input', 'madewithtocart'=>'main', 'holdmode'=>'separate'];
            $pdfformat=[    
                                'format'               => ['A4', 'A5', '80,1440'],
                                'orientation'          => ['P','L','P'],
                                'margin_left'          => ['10','10','10'],
                                'margin_right'         => ['10','10','10'],
                                'margin_top'           => ['10','10','10'],
                                'margin_bottom'        => ['10','10','10'],
                                'margin_header'        => ['10','10','10'],
                                'margin_footer'        => ['10','10','10'],
                    ];
          
           $syssetting=[ 
             'lang' => ['en','kh'],
             
             ];

            $savedata = $this->model->insert([
                    $this->fprimarykey=>1,
                  'generalcus' => '',
                  'generalsplier' => '',
                  'pdfwatermask' => 'I P O S',
                  'pdfformat'   => json_encode($pdfformat),
                  'usingsizecolor' => 'yes',
                  'sub_input' => '1',
                  'cus_level' => 0,
                  'splier_level' => 0,
                  'info_tag' => json_encode($info_tag),
                  'yesnotag' => json_encode($yesnotag),
                  'herder_text' => json_encode($herder_text),
                  'dctype' => 0,
                  'syssetting'  =>  json_encode($syssetting),
                  'blongto' => 1
            ]);
        }

	} /*../function..*/

    public function __destruct(){

        $input = $this->retrieveData();
        //dd($calunit);

        config(['sysconfig' => $input]);
        $text = '<?php return ' . var_export(config('sysconfig'), true) . ';';
        $databaseName = config('ccms.backend');
        file_put_contents(config_path($databaseName.'_sysconfig.php'), $text);


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
        $js_filemanagersetting=array(   'displaymode' => '1',
                                        'filetype'   =>'',
                                        'givent_txtbox'=>'txt_scrshot',
                                        'calledby'=>'public', 
                                        'numperpage'=>12, 
                                        'ajax_url'=>config('ccms.js_env.ajaxpublic_url'), 
                                        'objtable'=>'cms_articlefile', 
                                        'idvalue'=>0
                                    );

        $js_config = [
            'filemanagerSetting'    => $js_filemanagersetting,
            'jsmessage'             =>array('df_confirm'=>__('ccms.df_confirm'))
            
        ];

        $paymentmethod = Paymentmethod::where('trash', '!=', 'yes')->select('pmethod_id', 'title')->pluck('title', 'pmethod_id')->toArray();

        //$accountno = Accountno::where('trash', '!=', 'yes')->select('accno_id', 'title')->pluck('title', 'accno_id')->toArray();
        
        /* pcategory */
        $categories=Product::getcategory($this->dflang[0])->get();
        $category_list = $categories->pluck('title','c_id')->toArray();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['c_id','parent_id'],0);
      
        $accountno = Accountno::where('trash', '!=', 'yes')
          ->where('acctype_id',2)
          ->select(\DB::raw("accno_id, concat(code,'-',title) as title"))
          ->pluck('title', 'accno_id')->toArray();
      
        $account_discount = Accountno::where('trash', '!=', 'yes')
          ->where('acctype_id',11)
          ->select(\DB::raw("accno_id, concat(code,'-',title) as title"))
          ->pluck('title', 'accno_id')->toArray();

          $account_eqt = Accountno::where('trash', '!=', 'yes')
          ->where('acctype_id',8)
          ->select(\DB::raw("accno_id, concat(code,'-',title) as title"))
          ->pluck('title', 'accno_id')->toArray();
        
        return [
          'js_config'=>$js_config,
          'paymentmethod' => $paymentmethod,
          'accountno' => $accountno,
          'cat_tree'=>$cat_tree, 
          'category_list' => $category_list,
          'account_discount' => $account_discount,
          'account_eqt' => $account_eqt,
          
        ];
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        );
    } /*../function..*/


    public function trash(Request $request)
    {
        return null;

    } /*../function..*/

    public function create(Request $request)
    {
       return null;
    } /*../function..*/


    public function store(Request $request)
    {
        return null;

        
    } /*../function..*/

    public function update(Request $request)
    {
        $obj_info=$this->obj_info;

        if ($request->isMethod('post'))
        {
            
           $data=$this->setinfo($request, true);
            $updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);
                

                ############
                $success_ms = __('ccms.suc_save');
                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        $this->fprimarykey => $data['id']
                                                    ]
                                    ];


        } /*../if POST..*/

    } /*../end fun..*/

    public function index(Request $request, $id=1)
    {

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }
      
         #Retrieve Data#
        $codermode= 'hide';
        if(isset($this->args['routeinfo']['id']) && !empty($this->args['routeinfo']['id'])){
            $codermode = '';
        }
       
        

        $obj_info=$this->obj_info;
        $default=$this->default();
        $js_config = $default['js_config'];
        $paymentmethod = $default['paymentmethod'];
        $accountno = $default['accountno'];
        $account_discount = $default['account_discount'];
        $account_eqt = $default['account_eqt'];
        $cat_tree = $default['cat_tree'];

        $pages=$this->model->getpages($this->dflang[0])->pluck('p_name', 'p_id');
        $pages = json_decode(json_encode($pages), true);

        $input = null;
        #Retrieve Data#
        $input = $this->retrieveData();
     
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'paymentmethod',
                            'accountno',
                            'account_discount',
                            'account_eqt',
                            'cat_tree',
                            'codermode',
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

    public function retrieveData(){
        $editid = 1;
        $input = $this->model->where($this->fprimarykey, (int)$editid)->get();
        $input = $input->toArray()[0];
        //dd(json_decode($input), true);
        #extract generalcus#
        $data_generalcus = json_decode($input['generalcus'], TRUE);
        $generalcus=[];
        if(!empty($data_generalcus))
        foreach ($data_generalcus as $key => $value) {
            $generalcus[$key]=$value;
        }

        #extract generalcus#
        $data_generalsplier = json_decode($input['generalsplier'], TRUE);
        $generalsplier=[];
        if(!empty($data_generalsplier))
        foreach ($data_generalsplier as $key => $value) {
            $generalsplier[$key]=$value;
        }

        #extract herder_text#
        $data_herder_text = json_decode($input['herder_text'], TRUE);
        $herder_text=[];
        if(!empty($data_herder_text))
        foreach ($data_herder_text as $key => $value) {
            $herder_text[$key]=$value;
        }

        #extract herder_text#
        $data_info_tag = json_decode($input['info_tag'], TRUE);
        $info_tag=[];
        if(!empty($data_info_tag))
        foreach ($data_info_tag as $key => $value) {
            $info_tag[$key]=$value;
        }
      
        #extract herder_text#
        $data_yesno_tag = json_decode($input['yesnotag'], TRUE);
        $yesno_tag=[];
        if(!empty($data_yesno_tag))
        foreach ($data_yesno_tag as $key => $value) {
            $yesno_tag[$key]=$value;
        }

        #pdf
        $pdfformat = json_decode($input['pdfformat'], TRUE);
        $count = count($pdfformat['format']);
        $fomat=[];
        for($i=0; $i<$count; $i++)
        {
            foreach ($pdfformat as $key => $value) {
                $fomat['pdfformat'.$i][$key] = $value[$i]; 
            }
        }
        


        $input = array_merge($input, $generalcus);
        $input = array_merge($input, $generalsplier);
        $input = array_merge($input, $info_tag);
        $input = array_merge($input, $yesno_tag);
        $input = array_merge($input, $herder_text);
        $input = array_merge($input, $pdfformat);
        $input = array_merge($input, $fomat);

        return $input;
    }

    public function validation($request, $isupdate=false){
       return true;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){

        $newid=1;

        $customer = $request->input('customer') ?? '';
        $cm_id =  $request->input('cm_id') ?? '';
        $supplier = $request->input('supplier') ?? '';
        $supplier_id = $request->input('supplier_id') ?? '';

        $herder_texts=['inv', 'qt', 'pch', 'po', 'dl', 'iddigit', 'rp', 'pp', 'exp', 'addstock', 'adjuststock', 'je', 'rt', 'rtpch'];

        $herder_text=[];
        foreach ($herder_texts as $field) 
        {
            $herder_text[$field]=$request->input($field)??'';
                
        }


        $info_tags = ['invtheme', 'postheme','paymentinfo', 'pospmethod_id', 'posaccno_id', 'acc_pcy', 'acc_re', 'acc_salediscount','poswelcometext','companyname', 'productnum', 'whnum', 'branchnum', 'posfor', 'catlabo', 'catimagery', 'catservice'];
        $info_tag=[];
        foreach ($info_tags as $field) 
        {
            $info_tag[$field]=$request->input($field)??'';
                
        }
      
        $yesnotags = ['p2ptransfer','madewithstock','usingextraprice', 'df_dis', 'ct_id', 'costmethod','lowstock', 'requiredchkin', 'ciosize', 'cioproduct', 'restaurant', 'withcosting', 'customermode', 'madewithtocart', 'holdmode'];
        $yesnotag=[];
        foreach ($yesnotags as $field) 
        {
            $yesnotag[$field]=$request->input($field)??'';
                
        }

        #PDF Format
        $pdfformat=[    
                                'format'               => $request->input('format') ?? ['A4', 'A5', '80,1440'],
                                'orientation'          => $request->input('orientation') ?? ['P','L','P'],
                                'margin_left'          => $request->input('margin_left') ?? ['10','10','10'],
                                'margin_right'         => $request->input('margin_right') ?? ['10','10','10'],
                                'margin_top'           => $request->input('margin_top') ?? ['10','10','10'],
                                'margin_bottom'        => $request->input('margin_bottom') ?? ['10','10','10'],
                                'margin_header'        => $request->input('margin_header') ?? ['10','10','10'],
                                'margin_footer'        => $request->input('margin_footer') ?? ['10','10','10'],
                    ];
      
      
      $syssetting=[ 
             'lang' => ['en','kh'],
             
             ];

        
        $tableData = [
                
                'generalcus' => json_encode(['cm_id'=>$cm_id, 'customer' =>$customer]),
                  'generalsplier' => json_encode(['supplier_id' => $supplier_id, 'supplier' => $supplier]),
                  'pdfwatermask' => $request->input('pdfwatermask') ?? 'I P O S',
                  'pdfformat'   => json_encode($pdfformat),
                  'usingsizecolor' => $request->input('usingsizecolor') ?? 'yes',
                  'sub_input' => $request->input('sub_input') ?? 1,
                  'cus_level' => $request->input('cus_level') ?? 0,
                  'splier_level' => $request->input('splier_level') ?? 0,
                  'info_tag' => json_encode($info_tag),
                  'yesnotag'  => json_encode($yesnotag),
                  'herder_text' => json_encode($herder_text),
                  'dctype' => $request->input('dctype') ?? 0,
                  'syssetting'  =>  json_encode($syssetting),
            
        ];


        return ['tableData' => $tableData, 'id'=>$newid];
        

    }/*../function..*/



    public function delete(Request $request, $id=0)
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
	
    
}