<?php
namespace App\Plugins\Filemanager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use App\Plugins\Filemanager\Filecategory;


class FilecategoryController extends Controller
{
    private $args;
    private $objtable;
    private $fprimarykey='fc_id';
    private $request;
    private $obj_info=['name'=>'article','title'=>'Article','icon'=>'<i class="fa fa-file-text-o" aria-hidden="true"></i>'];

	public function __construct(array $args){
        $this->args = $args;
        $this->objtable=new Filecategory;
	}

	public function index(Request $request)
    {
        $calledby='public';
        $setting=[];
        if($request->input('setting')){
           $setting= $request->input('setting'); 
           $calledby=$setting['calledby'];
        }
        
        
        $root_cat[0]=["id" => "0", "parent" => "#", "text" => "Root","state" => [ "opened" => true, "selected" => true ] ];

        $categories=$this->objtable->getall($calledby)->get()->toArray();
        $cat_tree=buildArrayTree($categories,['fc_id','parent_id'],0);
        $generate_cat=$this->buildFileCategoryTree($cat_tree,$root_cat);

        return json_encode($generate_cat);

    	/*echo '[
           { "id" : "ajson2", "parent" : "#", "text" : "Root node 2","state" : { "opened" : true, "selected" : true } },
           { "id" : "ajson3", "parent" : "ajson2", "text" : "Child 1" },
           { "id" : "ajson4", "parent" : "ajson2", "text" : "Child 2" }
        ]';*/
    }

    public function storecategory(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $calledby='public';
            $setting=[];
            if($request->input('setting')){
               $setting= $request->input('setting'); 
               $calledby=$setting['calledby'];
            }

            $parentId=$request->input('nodeid');
            $categories=$this->objtable->getchildname($parentId)->get()->toArray();
            $name=newName("New folder",$categories,'c_name');
            if(!empty($name)){
                $newid=$this->objtable->max($this->fprimarykey)+1;
                $this->objtable->fc_id=$newid;
                $this->objtable->parent_id=$parentId;
                $this->objtable->c_name=$name;
                $this->objtable->ordering=0;
                $this->objtable->blongobj=$calledby;
                $this->objtable->objid=0; /*NEED TO UPDATE*/
                $this->objtable->blongto=$this->args['userinfo']['id'];; /*NEED TO UPDATE*/
                $this->objtable->timestamps = false;
                $this->objtable->save();
            }
            return json_encode(['id'=>$newid,'name'=>$name]);
        }
    }

    public function updatecategory(Request $request){
        if ($request->isMethod('post'))
        {
            $validate=$request->validate(['newName' => 'required','nodeid' => 'required']);
            if($validate){
                $getrecord=$this->objtable->find((int)$validate['nodeid']);
                $condition = array('parent_id' => $getrecord->parent_id, 'c_name' => trim($validate['newName']));
                $chkname = $this->objtable->where($condition)->get()->toArray();
                
                if($chkname){
                     return $getrecord->c_name;
                }else{
                    try {
                        $getrecord->c_name = trim($validate['newName']);
                        $getrecord->timestamps = false;
                        $getrecord->save();
                    } catch (\Exception $e) {
                        return;
                    }
                    return 'update';
                   
                }
            }
        }
    }

    /**
     * @param Array elements[id,parent_id,name,...]
     * @param Int parentId
     * @return Array
     */
    function buildFileCategoryTree(array $elements,$tree = array()) {
        foreach($elements as $element)
        {
            array_push($tree,array( "id" => $element['fc_id'], "parent" => (string)$element['parent_id'], "text" => $element['c_name'] ));
            if(isset($element['children'])) {
                $tree=$this->buildFileCategoryTree($element['children'],$tree);
            }
        }

        return $tree;
    }/**@endfun**/


}