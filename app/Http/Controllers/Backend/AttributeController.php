<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;
use Illuminate\Validation\Rule;

use App\Models\Backend\Attribute;
use App\Models\Backend\Datalist;



class AttributeController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='ab_id';
    private $tbltranslate='';
    private $dflang;
    private $request;
    private $rcdperpage=0; #record per page#
    private $obj_info=['name'=>'attribute','title'=>'Attribute','routing'=>'admin.controller','icon'=>'<i class="fa fa-tags" aria-hidden="true"></i>'];


	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
		$this->model = new Attribute;
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
        $datalists=Datalist::getparent($this->dflang[0])->pluck('title', 'dl_id');
        //$datalists = json_decode(json_encode($datalists), true);
        
        return ['datalists'=>$datalists];
    } /*../function..*/

     public function listingModel()
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, parent_id, attribute, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        );
    } /*../function..*/

    public function sfp($request, $results)
    {
        #Sort Filter Pagination#

        // CACHE SORTING INPUTS
        $allowed = array('title', 'c_id', 'ordering', 'add_date'); // add allowable columns to sort on
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'ordering'; // if user type in the url a column that doesnt exist app will default to id
        $order = $request->input('order') === 'asc' ? 'desc' : 'asc'; // default desc
        $results = $results->orderby($sort, $order);


        // FILTERS
        $appends = []; #set its elements for Appending to Pagination#
        $querystr = [];

        if ($request->has('title')) 
        {
            $qry=$request->input('title');
            $results = $results->where('title', 'like', '%'.$qry.'%');
            array_push($querystr, 'title='.$qry);
            $appends = array_merge ($appends,['title'=>$qry]);
        }


        

        #no need to send default sort and order to Blade#
        if($sort==$this->fprimarykey && $order=='desc')
        {
            $sort = '';
            $order = '';
        }
        

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

        //dd($sfp['results']->items());
        //$categories=$results->get();
        $categories = $sfp['results']->items();
        $categories = json_decode(json_encode($categories), true);
        $cat_tree=buildArrayTree($categories,['id','parent_id'],0);
        //dd($cat_tree);
        return view('backend.v'.$this->obj_info['name'].'.index')
                ->with(['obj_info' => $obj_info])
                ->with($sfp)
                ->with(['caption' => __('ccms.active')])
                ->with(['cat_tree' => $cat_tree])
                ->with($setting);

    } /*../function..*/


    /*public function trash(Request $request)
    {
        

    } /*../function..*/

    public function create(Request $request)
    {

        $obj_info=$this->obj_info;
        $default=$this->default();
        $datalists = $default['datalists'];
        


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'datalists'
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

        $obj_info=$this->obj_info;
       
        if ($request->isMethod('post'))
        {
            $validator = $this->validation($request);
            if ($validator->fails()) {

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);

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
                $saveparent = $this->model->insert($data['parent_data']);
                $savedata = $this->model->insert($data['tableData']);
                

                if($saveparent && $savedata )
                {
                    
                    $savetype=strtolower($request->input('savetype'));
                    $success_ms = __('ccms.suc_save');
                    $id = array_column($data['tableData'], $this->fprimarykey);
                    //$id = array_push($id, $data['id']);
                    
                    switch ($savetype) {
                        case 'save':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms)
                            #->with($this->fprimarykey , $data['id']);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'input' => $request->input(),
                                                        $this->fprimarykey => $data['id'],
                                                        'id' => $data['id'].','.implode(',', $id)
                                                    ]
                                    ];
                            break;

                        case 'new':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'create']);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id'].','.implode(',', $id)
                                                    ]
                                    ];
                            break;

                        case 'apply':
                            # code...
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$data['id']]);
                            #return \Redirect::to($routing)
                            #->with('success', $success_ms);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id'].','.implode(',', $id)
                                                    ]
                                    ];
                            break;

                        default:
                            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
                            #return \Redirect::to($routing);
                            return [
                                        'act' => $savedata,
                                        'url' => $routing,
                                        'passdata' => [
                                                        'success' => $success_ms,
                                                        'id' => $data['id'].','.implode(',', $id)
                                                    ]
                                    ];
                            break;

                    }
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

                //dd($validator->errors()->first());

                $routing=url_builder($obj_info['routing'],[$obj_info['name'],'edit/'.$request->input('parent_id')]);
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

                $tableData = array_except($data['parent_data'], ['parent_id', 'blongto']);
                $updatedata = $this->model->where($this->fprimarykey,$data['parent_data'][$this->fprimarykey])
                                            ->update($tableData);

                /*$updatedata = $this->model->where($this->fprimarykey,$data['id'])
                                            ->update($data['tableData']);*/

                foreach ($data['tableData']as $row) {

                    if(empty($row[$this->fprimarykey]))
                    {
                        $newid = $this->model->max($this->fprimarykey)+1;
                        $row[$this->fprimarykey] = $newid;
                        $savedata = $this->model->insert($row);
                    }
                    else
                    {
                        $editid = $row[$this->fprimarykey];
                        $tableData = array_except($row, [$this->fprimarykey, 'parent_id', 'ordering', 'blongto']);
                        $updatedata = $this->model->where($this->fprimarykey,$editid)
                                            ->update($tableData);
                    }
                    

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

        #prepare for back to url after SAVE#
        if (!$request->session()->has('backurl')) {
            $request->session()->put('backurl', redirect()->back()->getTargetUrl());
        }

        $obj_info=$this->obj_info;
        $default=$this->default();
        $datalists = $default['datalists'];

        $input = null;
        if ($request->session()->has('input')) 
        {
           #No need to retrieve data becoz already set by Form#
            $editid=session('input')[$this->fprimarykey][0];
            //goto skip;
        }
        
        #Retrieve Data#
        if (empty($id))
        {
            $editid = $this->args['routeinfo']['id'];
        }
        else
        {
            $editid = $id;
        }

        $parent = $this->model->where(
                    [
                        $this->fprimarykey => (int)$editid,
                        'parent_id' => 0
                    ]
                )->get(); 


        if($parent->isEmpty())
        {
            $routing=url_builder($obj_info['routing'],[$obj_info['name'],'index']);
            return \Redirect::to($routing)
            ->with('errors', __('ccms.rqnvalid'));

        }

        $parent = $parent->toArray()[0];

        $input = $this->model->where('parent_id', (int)$parent[$this->fprimarykey])->orderby('ordering')->get(); 

        $input = $input->toArray();

        $input_tmp=[];
        foreach ($input as $row) {
            #extract title#
            $data_title = json_decode($row['title'], TRUE);
            $title=[];
            foreach ($data_title as $key => $value) {
                $input_tmp['title-'.$key][]=$value;
            }

            foreach ($row as $key => $value) {
                $input_tmp[$key][]=$value;
            }
        }

        //dd($input_tmp);
        
        $input=$input_tmp;

        //parent data
        $parent_title = json_decode($parent['title'], TRUE);
        $input['parent_id'] = $parent[$this->fprimarykey];
        $input['formname'] = $parent_title[$this->dflang[0]];
        skip:


        return view('backend.v'.$this->obj_info['name'].'.create',
                    compact('obj_info',
                            'datalists',
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
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $update_rules= [ $this->fprimarykey => 'required'];

            $numrecord=count($request->input($this->fprimarykey));

            $rules['title-'.$this->dflang[0].'.*'] = "distinct";

            for($i=0; $i<$numrecord; $i++)
            {
               #$rules['title-'.$this->dflang[0].'.'.$i]       = 'required';
               #need to check all record

                if($isupdate)
               {
                    $rules['formname']       = ['required','distinct',Rule::unique($this->model->gettable(),'title->>"$.'.$this->dflang[0].'"')->where(function ($query) use ($request,$i) {

                            $parent_id = empty($request->input('parent_id'))?-1:$request->input('parent_id');
                            #user parent_id becoze parent_id store an id of formname
                            return $query
                                ->where('parent_id',0)
                                ->whereNotIn($this->fprimarykey, [$parent_id]);

                        })];

                    $rules['title-'.$this->dflang[0].'.'.$i]       = ['required','distinct',Rule::unique($this->model->gettable(),'title->>"$.'.$this->dflang[0].'"')->where(function ($query) use ($request,$i) {

                            $parent_id = empty($request->input('parent_id'))?-1:$request->input('parent_id');

                            return $query
                                ->where('parent_id',$parent_id)
                                ->whereNotIn($this->fprimarykey, [$request->input($this->fprimarykey)[$i]]);

                        })];
                }
                else{

                    $rules['formname']       = ['required',Rule::unique($this->model->gettable(),'title->>"$.'.$this->dflang[0].'"')->where(function ($query) use ($request,$i) {


                            return $query
                                ->where('parent_id',0);

                        })];

                    $rules['title-'.$this->dflang[0].'.'.$i]       = ['required','distinct',Rule::unique($this->model->gettable(),'title->>"$.'.$this->dflang[0].'"')->where(function ($query) use ($request,$i) {

                            $parent_id = empty($request->input('parent_id'))?-1:$request->input('parent_id');

                            return $query
                                ->where('parent_id',$parent_id);

                        })];
                }


            }

            


            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire'),
                'unique' => __('ccms.fieldunique'),
                'distinct' => __('ccms.fielddistinct')
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
            $validator =Validator::make($request->input(), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $isupdate=false){

        $parent_id=($isupdate)? $request->input('parent_id') : $this->model->max($this->fprimarykey)+1;
        $title=[];
        foreach (config('ccms.multilang') as $lang)
            {
                $title[$lang[0]]=$request->input('formname');

            } #./foreach#
        $parent_data = [
            
                $this->fprimarykey => $parent_id,
                'parent_id' => 0,
                'attribute' => '',
                'title' => json_encode($title),
                'dl_id' => 0,
                'width' => '',
                'ordering' => 0,
                'display' => '',
                'tag' => '',
                'trash' => 'no',
                'blongto' => $this->args['userinfo']['id']
            
            ];



        $newid = $parent_id +1;

        /*For translate*/
        $title=[];
        $tableData=[];
        $numrecord=count($request->input($this->fprimarykey));

        for($i=0; $i<$numrecord; $i++)
        {
            foreach (config('ccms.multilang') as $lang)
            {
                $title[$lang[0]]=$request->input('title-'.$lang[0])[$i];

            } #./foreach#

            


            if($isupdate)
            {
                $newid=!empty($request->input($this->fprimarykey)[$i])? $request->input($this->fprimarykey)[$i]  : 0;

            }
            else
            {
                $newid = $newid+$i;
            }
            $record = [
            
                $this->fprimarykey => $newid,
                'parent_id' => $parent_id,
                'attribute' => (int)$request->input('attribute')[$i],
                'title' => json_encode($title),
                'dl_id' => (int)$request->input('dl_id')[$i],
                'width' => '',
                'ordering' => 0,
                'display' => $request->input('display')[$i],
                'tag' => '',
                'trash' => 'no',
                'blongto' => $this->args['userinfo']['id']
            
            ];

            array_push($tableData, $record);

        }

        return ['tableData' => $tableData, 'parent_data' => $parent_data, 'id'=>$parent_id];
        

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
        $parent_id = $this->model->where($this->fprimarykey, (int)$editid)->value('parent_id');

        $delete = $this->model->where($this->fprimarykey, (int)$editid)->orWhere('parent_id', (int)$editid)->delete(); 
        //$delete = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 
        //return redirect()->back()->with('success', 'delete oK');
        return [
                    'act' => $delete,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => __('ccms.suc_delete'),
                                    'id' => $editid
                                ]
                ]; 

    } /*../function..*/

    /*public function restore(Request $request, $id=0)
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

        $restore = $this->model->where($this->fprimarykey, (int)$editid)->update(['trash'=>'no']); 
        //return redirect()->back();

        return [
                    'act' => $restore,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => 'restore ok'
                                ]
                ];

    } /*../function..*/


    /*public function destroy(Request $request, $id=0)
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

        $destroy = $this->model->where($this->fprimarykey, (int)$editid)->delete(); 

        //return redirect()->back();
        return [
                    'act' => $destroy,
                    'url' => redirect()->back()->getTargetUrl(),
                    'passdata' => [
                                    'success' => 'destroy ok'
                                ]
                ];

    } /*../function..*/


    public function duplicate(Request $request, $id=0)
    {
        return null;
    } /*../function..*/


    /********************************/
    public function editlist(Request $request){
        $listdata=$request->input('listdata');
        $listdata = html_entity_decode($listdata);
        
        if(!empty($listdata))
        {
            $json = json_decode($listdata, true);
            $level = 1;
            foreach ($json as $element)
            {
                $update_child = $this->model->where('ab_id', $element['id'])->update(['ordering'=>$level]);
                $level+= 1;
            }
            
        }

    }/*../function..*/

    public function generateform(Request $request)
    {
        $parent_id=(int)$request->input('parent');
        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, parent_id, attribute, dl_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where('parent_id',$parent_id)
        ->orderBy('ordering');
        if($results){
            $datalists = new Datalist;
            return view('backend.v'.$this->obj_info['name'].'.attribute')
            ->with([
                'results' => $results->get()->toArray(),
                'datalists' => $datalists
            ]
            );
        }
    }/*../function..*/


    public function generateformdata(Request $request, $parent_id)
    {
        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, parent_id, attribute, dl_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where('parent_id',$parent_id)
        ->orderBy('ordering');
        $data=[];

        if($results){
            $results = $results->get()->toArray();

            foreach ($results as $row) {
                $attr_name = 'attr-'.$row['id'];
                switch ((int)$row['attribute']) {
                    case 1:
                        $data[$attr_name] = $request->input($attr_name);
                        break;

                    case 2:
                        $title = [];
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $title[$lang[0]]=$request->input($attr_name.'-'.$lang[0]);

                        } #./foreach#
                        $data[$attr_name] = $title;
                        break;

                    case 3:
                        $data[$attr_name] = $request->input($attr_name);
                        break;

                    case 4:
                        $data[$attr_name] = $request->input($attr_name);
                        break;
                    
                    
                } #./switch
            }

        }

        return $data;
    }/*../function..*/


    public function extractformdata($source, $parent_id)
    {
        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, parent_id, attribute, dl_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where('parent_id',$parent_id)
        ->orderBy('ordering');
        $data=[];

        if($results){
            $results = $results->get()->toArray();

            foreach ($results as $row) {
                $attr_name = 'attr-'.$row['id'];
                switch ((int)$row['attribute']) {
                    case 1:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;

                    case 2:
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $data[$attr_name.'-'.$lang[0]] = isset($source[$attr_name][$lang[0]]) ? $source[$attr_name][$lang[0]] : '';

                        } #./foreach#
                        
                        break;

                    case 3:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;

                    case 4:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;
                    
                    
                } #./switch
            }

        }

        return $data;
    }/*../function..*/

    
}