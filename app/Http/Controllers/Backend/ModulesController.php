<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use Validator;

use App\Models\Backend\Modules;
use App\Models\Backend\Datalist;


class ModulesController extends Controller
{
    private $args;
	private $model;
    private $fprimarykey='mds_id';
    private $tblsub='';
    private $foreignkey='';
    private $dflang;
    private $request;
    private $rcdperpage=0; #record per page#
    private $obj_info=['name'=>'modules','title'=>'Modules','routing'=>'admin.controller','icon'=>'<i class="fa fa-folder" aria-hidden="true"></i>'];

    private $parent_id = 'md_id';
	public function __construct(array $args){ //public function __construct(Array args){

        $this->args = $args;
		$this->model = new Modules;
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

     public function listingModel($md_id=0)
    {
        #DEFIND MODEL#
        return $this->model->select(\DB::raw(   $this->fprimarykey." AS id, attribute, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
                ->where($this->parent_id,$md_id);
    } /*../function..*/

    public function sfp($request, $results)
    {
        
    } /*../function..*/

	public function index(Request $request, $condition=[], $setting=[])
    {

        $obj_info=$this->obj_info;


    } /*../function..*/


    /*public function trash(Request $request)
    {
        

    } /*../function..*/

    public function create(Request $request)
    {

        
    } /*../function..*/


    public function store(Request $request)
    {
        

        
    } /*../function..*/

    public function update(Request $request)
    {
        

    } /*../end fun..*/

    public function edit(Request $request, $id=0)
    {

        
    } /*../end fun..*/

    public function validation($request, $isupdate=false){
        // validate
            // read more on validation at http://laravel.com/docs/validation
            $update_rules= [ $this->fprimarykey => 'required'];

            $numrecord=count($request->input($this->fprimarykey));

            for($i=0; $i<$numrecord; $i++)
            {
               $rules['title-'.$this->dflang[0].'.'.$i]       = 'required';
               #need to check all record
            }


            if($isupdate){
                $rules=array_merge($rules, $update_rules);
            }

            $validatorMessages = [
                /*'required' => 'The :attribute field can not be blank.'*/
                'required' => __('ccms.fieldreqire')
                
            ];

           /* $attribute = [
                'title-en' => 'First Name'
            ];*/
            
            /*$validator =Validator::make($request->input(), $rules, $validatorMessages, $attribute);*/
            $validator =Validator::make($request->input(), $rules, $validatorMessages);

            return $validator;

    }/*../function..*/

    public function setinfo($request, $parent_id=0, $isupdate=false){
        
        $title=[];
        $tableData=[];
        $numrecord=count($request->input($this->fprimarykey));
        $newid = $this->model->max($this->fprimarykey)+1;
        for($i=0; $i<$numrecord; $i++)
        {
            foreach (config('ccms.multilang') as $lang)
            {
                $title[$lang[0]]=$request->input('title-'.$lang[0])[$i];

            } #./foreach#

            


            if($isupdate)
            {
                $newid=$request->input($this->fprimarykey)[$i];

            }
            else
            {
                $newid = $newid+$i;
            }
            $record = [
            
                $this->fprimarykey => $newid,
                $this->parent_id => $parent_id,
                'attribute' => (int)$request->input('attribute')[$i],
                'title' => json_encode($title),
                'dl_id' => (int)$request->input('dl_id')[$i],
                'validator' => $request->input('validator')[$i],
                'as_column' => $request->input('as_column')[$i],
                'placeholder' => isset($request->input('placeholder')[$i])? $request->input('placeholder')[$i] : '',
                'display' => $request->input('display')[$i],
                'ordering' => 0,
                'tag' => '',
                'blongto' => $this->args['userinfo']['id']
            
            ];

            array_push($tableData, $record);

        }

        return ['tableData' => $tableData, 'id'=>$parent_id];
        

    }/*../function..*/



    public function delete(Request $request, $id=0)
    {
        

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
                $update_child = $this->model->where($this->fprimarykey, $element['id'])->update(['ordering'=>$level]);
                $level+= 1;
            }
            
        }

    }/*../function..*/



    public function generateform(Request $request,$parent_id=0)
    {
        if($parent_id==0){
            $parent_id=(int)$request->input('parent');
        }
        

        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, ".$this->parent_id.", attribute, dl_id, validator, placeholder, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where($this->parent_id,$parent_id)
        ->orderBy('ordering');
        if($results){
            $datalists = new Datalist;
            return view('backend.v'.$this->obj_info['name'].'.fielbuilder')
            ->with([
                'results' => $results->get()->toArray(),
                'datalists' => $datalists
            ]
            );
        }
    }/*../function..*/


    public function generateformdata(Request $request, $parent_id)
    {
        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, ".$this->parent_id.", attribute, dl_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where($this->parent_id,$parent_id)
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
                        $info = [];
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $info[$lang[0]]=$request->input($attr_name.'-'.$lang[0]);

                        } #./foreach#
                        $data[$attr_name] = $info;
                        break;

                    case 3:
                        $data[$attr_name] = $request->input($attr_name);
                        break;

                    case 4:
                        $data[$attr_name] = $request->input($attr_name);
                        break;

                    case 5:
                        //$data[$attr_name] = trim(nl2br(htmlentities($request->input($attr_name), ENT_QUOTES, 'UTF-8')));
                        $data[$attr_name] = $request->input($attr_name);
                        break;

                    case 6:
                        $info = [];
                        foreach (config('ccms.multilang') as $lang)
                        {
                            //$info[$lang[0]]=trim(nl2br(htmlentities($request->input($attr_name.'-'.$lang[0]))));
                            $info[$lang[0]]=$request->input($attr_name.'-'.$lang[0]);

                        } #./foreach#
                        $data[$attr_name] = $info;
                        break;

                    case 7:
                        $data[$attr_name] = $request->input($attr_name);
                        break;

                    case 8:
                        $data[$attr_name] = $request->input($attr_name);
                        break;
                    case 9:
                        $data[$attr_name] = $request->input($attr_name);
                        break;
                    case 10:
                        $info = [];
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $info[$lang[0]]=$request->input($attr_name.'-'.$lang[0]);

                        } #./foreach#
                        $data[$attr_name] = $info;
                        break;
                    case 11:
                        $data[$attr_name] = $request->input($attr_name);
                        break;
                    case 12:
                        $info = [];
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $info[$lang[0]]=$request->input($attr_name.'-'.$lang[0]);

                        } #./foreach#
                        $data[$attr_name] = $info;
                        break;
                    
                    
                } #./switch
            }

        }

        return $data;
    }/*../function..*/

    public function extractformdata($source, $parent_id)
    {
        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, ".$this->parent_id.", attribute, dl_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where($this->parent_id,$parent_id)
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
                    case 5:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;

                    case 6:
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $data[$attr_name.'-'.$lang[0]] = isset($source[$attr_name][$lang[0]]) ? $source[$attr_name][$lang[0]] : '';

                        } #./foreach#
                        break;

                    case 7:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;

                    case 8:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;

                    case 9:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;

                    case 10:
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $data[$attr_name.'-'.$lang[0]] = isset($source[$attr_name][$lang[0]]) ? $source[$attr_name][$lang[0]] : '';

                        } #./foreach#
                        break;

                    case 11:
                        $data[$attr_name] = isset($source[$attr_name]) ? $source[$attr_name] : '';
                        break;   

                    case 12:
                        foreach (config('ccms.multilang') as $lang)
                        {
                            $data[$attr_name.'-'.$lang[0]] = isset($source[$attr_name][$lang[0]]) ? $source[$attr_name][$lang[0]] : '';

                        } #./foreach#
                        break;
                    
                    
                } #./switch
            }

        }

        return $data;
    }/*../function..*/

    public function generatecolumn($parent_id){
        $results = $this->model->select(\DB::raw(   $this->fprimarykey." AS id, ".$this->parent_id.", attribute, dl_id, 
                                                    JSON_UNQUOTE(title->'$.".$this->dflang[0]."') AS title"
                                                )
                                        )
        ->where($this->parent_id,$parent_id)
        ->where('as_column','yes')
        ->orderBy('ordering');
        //dd($results->get()->toArray());
        $results = $results->get();
        return $results;
    }/*../function..*/


    public function validationrule(Request $request,$parent_id=0)
    {
        $attr_rules=[];
        if($parent_id==0){
            $parent_id=(int)$request->input('parent');
        }
        

        $results = $this->model->select(\DB::raw($this->fprimarykey." AS id, attribute, validator"))
        ->where($this->parent_id,$parent_id)
        ->where('validator','<>','')
        ->orderBy('ordering');
        if($results){
            $rows = $results->get()->toArray();
                    foreach ($rows as $key => $value) {
                        switch ($value['attribute']) { //1,3,4,5,7,8,9,11
                            case 1:
                            case 3:
                            case 4:
                            case 5:
                            case 7:
                            case 8:
                            case 9:
                            case 11:
                                $attr_rules['attr-'.$value['id']] = trim($value['validator']);
                                break;
                            
                            case 2:
                            case 6:
                            case 10:
                            case 12:
                                # code...
                                foreach (config('ccms.multilang') as $lang)
                                {
                                    $attr_rules['attr-'.$value['id'].'-'.$lang[0]] = trim($value['validator']);
                                }
                                break;
                        }
                        //dd('attr-'.$value['id'].'.*');
                        //array_push($rules, ['attr-'.$value['id'].'.*' => trim($value['validator'])]);
                        //$attr_rules['attr-'.$value['id']] = trim($value['validator']);
                        
                    }
        }

        return $attr_rules;
    }/*../function..*/


    

    
}