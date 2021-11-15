<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Image;
use Validator;

//use App\Models\Backend\Article;
use App\Models\Backend\Datalist;
use App\Models\Backend\Module;

use App\Http\Controllers\Backend\ModulesController;
use App\Http\Controllers\Backend\ArticleController;

class ArticlebuilderController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='a_id';
    private $tbltranslate='cms_articledetail';
    private $tblfile='cms_articlefile';
    private $dflang;
    private $request;
    private $rcdperpage=-1; #record per page, set zero to get all record#
    private $thisobj = 'articlebuilder';
    private $modelmodule;
    private $moduleinfo;
    private $obj_info=['name'=>'articlebuilder','title'=>'Article Builder','routing'=>'admin.controller','icon'=>'<i class="fa fa-file-text-o" aria-hidden="true"></i>'];

    private $modulescontroller;
    private $articlecontroller;


	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
		//$this->model = new Article;
        $this->modelmodule = new Module;
        $this->dflang = config('ccms.multilang')[0];
        

        $module = $this->modelmodule->select(\DB::raw("md_id AS id,modulename, icon, description, meta, setting, acategory, media,
                                                    JSON_UNQUOTE(moduletitle->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )->where('modulename',$this->args['routeinfo']['obj']);
        $this->moduleinfo = $module->get()->toArray()[0];
        $this->obj_info['name'] = $this->moduleinfo['modulename'];
        $this->obj_info['title'] = $this->moduleinfo['title'];
        $this->obj_info['icon'] = html_entity_decode($this->moduleinfo['icon']);

        $this->modulescontroller = new ModulesController($args);
        $this->articlecontroller = new ArticleController($args);


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
        return $this->articlecontroller->default();
    } /*../function..*/

    public function listingModel()
    {
        #DEFIND MODEL#
        return $this->articlecontroller->model->select(\DB::raw(   $this->fprimarykey." AS id, c_id, status, ordering,att_ele,
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )->where('md_id',$this->moduleinfo['id']);


    } /*../function..*/

    public function sfp($request, $results)
    {
        return $this->articlecontroller->sfp($request, $results);
    } /*../function..*/

	public function index(Request $request, $condition=[], $setting=[])
    {
        

        $obj_info=$this->obj_info;
        $default=$this->default();
        $cat_tree = $default['cat_tree'];
        $category_list = $default['category_list'];
        $mudules = $this->modulescontroller->model->select(\DB::raw($this->modulescontroller->fprimarykey." AS id, md_id, attribute, dl_id,JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
            )
            ->where('md_id',$this->moduleinfo['id'])
            ->orderBy('ordering');

        #DEFIND MODEL#
        $results = $this->listingmodel();
        if(empty($condition))
        {
            $results = $results->where('trash', '!=', 'yes');
        }
        else
        {
            //
        }

        $sfp = $this->sfp($request, $results);

        $columns = $this->modulescontroller->generatecolumn($this->moduleinfo['id']);
        $datalist = new Datalist;
        $datalists=$datalist->getalldatalist($this->dflang[0])->pluck('title', 'dl_id');
       
    	return view('backend.v'.$this->thisobj.'.index', compact('cat_tree', 'category_list'))
                ->with(['act' => 'index'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['columns'=>$columns])
                ->with(['caption' => __('ccms.active')])
                ->with(['datalists' => $datalists])
                ->with($setting);


    } /*../function..*/


    public function trash(Request $request)
    {
        $obj_info=$this->obj_info;

        $default=$this->default();
        $cat_tree = $default['cat_tree'];
        $category_list = $default['category_list'];

        #DEFIND MODEL#
        $results = $this->listingmodel();
        $results = $results->where('trash', '=', 'yes');
        $sfp = $this->sfp($request, $results);

        return view('backend.v'.$this->thisobj.'.index', compact('cat_tree', 'category_list'))
                ->with(['act' => 'trash'])
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['trash'=>true])
                ->with(['caption' => __('ccms.bin')]);

    } /*../function..*/

    public function create(Request $request)
    {
        $obj_info=$this->obj_info;

        $default=$this->default();
        $js_config = $default['js_config'];
        $cat_tree = $default['cat_tree'];

        $pages=$this->articlecontroller->model->getpages($this->dflang[0])->pluck('p_name', 'p_id');

        /****** Delet media *****/
        if (!$request->session()->has('input')) 
        {
           deleteDataTable($this->tblfile,['obj_id'=>0,'blongto'=>$this->args['userinfo']['id']]);
        }

        return view('backend.v'.$this->thisobj.'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'pages'
                            )


                )->with(
                    [
                        'submitto'  => 'store',
                        'fprimarykey'     => $this->fprimarykey,
                        'caption' => __('ccms.new'),
                        'moduleinfo' => $this->moduleinfo
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

            if ($validator->fails()) {
                //$errors = $validator->errors();
                //foreach ($errors->all() as $message) {
                    //echo $message;
                //}

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                #return \Redirect::to($routing)
                #->with('errors', $validator->errors()->first())
                #->with('input' , $request->input())
                #->with('submitto', 'create');

               

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input(),
                                    'submitto' => 'create'
                                ]
                ];

            } else {
                $data=$this->setinfo($request);

                $savedata = $this->articlecontroller->insertintotable($data);
                
                if($savedata)
                {
                    
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');

                    $arr_savetype=[
                        "save"=>"index", 
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
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
                                                    ]
                                    ];

                }/*../if savedata==true..*/
            }
        } /*../if POST..*/

        
    } /*../function..*/

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

                return [
                    'act' => false,
                    'url' => $routing,
                    'passdata' => [
                                    'errors' => $validator->errors()->first(),
                                    'input' => $request->input()
                                ]
                ];

            } else {
                $data=$this->setinfo($request, true);
                $updatedata = $this->articlecontroller->updatedata($data);

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

                                if(stripos($routing, $obj_info['name'])===false)
                                {
                                    $routing=url_builder($obj_info['routing'],[$obj_info['name']]);
                                }
                                #return \Redirect::to($routing)
                                #->with('success', $success_ms)
                                #->with($this->fprimarykey , $data['id']);

                                return [
                                        'act' => $updatedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
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
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
                                                    ]
                                    ];
                            }
                            
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
                                                        'id' => $data['id'],
                                                        $this->fprimarykey => $data['id']
                                                        
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

        $beforedit = $this->articlecontroller->dobeforedit($request, $id);
        
        if(!is_array($beforedit))
        {
            $routing=url_builder($this->obj_info['routing'],[$this->obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));
        }
        


        foreach($beforedit as $key => $value) {
           $$key = $value;
        }
        $obj_info = $this->obj_info;
        return view('backend.v'.$this->thisobj.'.create',
                    compact('obj_info',
                            'js_config',
                            'cat_tree',
                            'pages',
                            'input'
                            )


                )->with(
                    [
                        'submitto'      => 'update',
                        'fprimarykey'   => $this->fprimarykey,
                        'caption' => __('ccms.edit'),
                        'moduleinfo' => $this->moduleinfo
                    ]
                );        
    } /*../end fun..*/

    public function validation($request, $isupdate=false){
        return $this->articlecontroller->validation($request,$isupdate);

    }/*../function..*/

    public function setinfo($request, $isupdate=false){

        return $this->articlecontroller->setinfo($request,$isupdate);

    }/*../function..*/



    public function delete(Request $request, $id=0)
    {
        
        return $this->articlecontroller->delete($request, $id);
    } /*../function..*/

    public function restore(Request $request, $id=0)
    {
        return $this->articlecontroller->restore($request, $id);

    } /*../function..*/


    public function destroy(Request $request, $id=0)
    {
        return $this->articlecontroller->destroy($request, $id);

    } /*../function..*/


    public function duplicate(Request $request, $id=0)
    {
        return $this->articlecontroller->duplicate($request, $id);

    } /*../function..*/

    public function edit_field(Request $request)
    {
        $obj_info=$this->obj_info;
        if ($request->has('datainfo'))
        {
            $datainfo = $request->input('datainfo');
            $datainfo = html_entity_decode($datainfo);
            $datainfo = json_decode($datainfo, true);

            $field = $datainfo['field'];
            $id = $datainfo['id'];
            $newvalue = $datainfo['newdata'];
            $datainfo = [$field => $newvalue];
            $updatedata = $this->model->where($this->fprimarykey,$id)
                                            ->update($datainfo);

            return [
                    'act' => $updatedata,
                    'url' => '',
                    'passdata' => [
                                    
                                    'id' => $id
                                ]
                ];
        }
        
    }/*../function..*/
	
    
}