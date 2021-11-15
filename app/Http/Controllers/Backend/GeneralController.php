<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Image;
use Validator;

use App\Models\Backend\General;



class GeneralController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='g_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set negetive to get all record#
    private $obj_info=['name'=>'general','title'=>'General','routing'=>'admin.controller','icon'=>'<i class="fa fa-info-circle" aria-hidden="true"></i>'];
    
    private $protectme;

	public function __construct(array $args){ //public function __construct(Array args){
    $this->obj_info['title'] = __('label.lb39');
    
    $this->protectme = [  ['index', 'index','Update'],
                                ['update', 'index','Update']
                        ];
    
        $this->args = $args;
		$this->model = new General;
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

        
        return ['js_config'=>$js_config];
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

        $obj_info=$this->obj_info;
        $default=$this->default();
        $js_config = $default['js_config'];

        $pages=$this->model->getpages($this->dflang[0])->pluck('p_name', 'p_id');
        $pages = json_decode(json_encode($pages), true);

        $input = null;
        # Delet media #
        if (!$request->session()->has('input')) 
        {
           deleteDataTable('cms_articlefile',['obj_id'=>0,'blongto'=>$this->args['userinfo']['id']]);
        }else{
            #No need to retrieve data becoz already set by Form#
            goto skip;
        }
        
        #Retrieve Data#
        $editid = 1;

        $input = $this->model->where($this->fprimarykey, (int)$editid)->get(); 
        if($input->isEmpty())
        {
            $this->model->truncate();
            $savedata = $this->model->insert([
                $this->fprimarykey=>1,
                'logo' => '',
                'icon' => '',
                'homepage' =>0,
                'contact' => '',
                'social' => '',
                'smtp' => '',
                'accountinfo' => '',
                'headerfooter' => '',
                'add_date' => date("Y-m-d H:i:s"),
                'exp_date' => 0,
                'blongto' => 1
            ]);

            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing);

        }

        $input = $input->toArray()[0];

        #extract contact#
        $data_contact = json_decode($input['contact'], TRUE);
        $contact=[];
        if(!empty($data_contact))
        foreach ($data_contact as $key => $value) {
            $contact[$key]=$value;
        }


        #extract social#
        $data_social = json_decode($input['social'], TRUE);
        $social=[];
        if(!empty($data_social))
        foreach ($data_social as $key => $value) {
            $social[$key]=$value;
        }


        #extract smtp#
        $data_smtp = json_decode($input['smtp'], TRUE);
        $smtp=[];
        if(!empty($data_social))
        foreach ($data_smtp as $key => $value) {
            $smtp[$key]=$value;
        }

        $input = array_merge($input, $contact);
        $input = array_merge($input, $social);
        $input = array_merge($input, $smtp);

        //dd($input);

        skip:
        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'js_config',
                            'pages',
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
       return true;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){

        $newid=1;

        /*setup table data*/
        
        $contacts=['rcvmail', 'address', 'phone', 'phone1', 'website', 'email', 'map', 'nativename', 'latinname', 'vat', 'theme', 'auth'];
        $contact=[];
        foreach ($contacts as $field) 
        {
            $contact[$field]=$request->input($field);
                
        }


        $socials=['facebook', 'youtube', 'twitter', 'linkedin', 'line', 'telegram'];
        $social=[];
        foreach ($socials as $field) 
        {
            $social[$field]=$request->input($field);
                
        }

        $smtps=['host', 'port', 'user', 'password', 'encryption', 'fromemail', 'fromname'];
        $smtp=[];
        foreach ($smtps as $field) 
        {
            $smtp[$field]=$request->input($field);
                
        }



        $add_date=!empty($request->input('add_date'))?date("Y-m-d H:i:s", strtotime($request->input('add_date'))):date("Y-m-d H:i:s");

        $exp_date=!empty($request->input('exp_date'))?strtotime($request->input('exp_date')):0;

        $tableData = [
                'logo' => $request->input('logo'),
                'icon' => $request->input('icon'),
                'homepage' =>!empty($request->input('homepage'))?$request->input('homepage'):1,
                'contact' => json_encode($contact),
                'social' => json_encode($social),
                'smtp' => json_encode($smtp),
                'accountinfo' => '',
                'headerfooter' => '',
                'add_date' => $add_date,
                'exp_date' => $exp_date,
                'blongto' => $this->args['userinfo']['id']
            
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