<?php
namespace App\Plugins\Filemanager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Config;
use App\Plugins\Filemanager\Filemanager;
use Image;
use Validator;
use File;

class FilemanagerController extends Controller
{
    private $objtable;
    private $fprimarykey='f_id';
    private $request;
    private $directorypath;
    private $obj_info=['name'=>'article','title'=>'Article','icon'=>'<i class="fa fa-file-text-o" aria-hidden="true"></i>'];

    private $filesetting;
    private $args;


	public function __construct(array $args){
      //dd($args);
        $this->args = $args;
        $this->objtable=new Filemanager;
        $this->directorypath=resource_path('filelibrary');
        
	}

    public function setinfo($request,$media_type,$media)
    {
        $temptable= new $this->objtable;
        $temptable->f_id=$temptable->max($this->fprimarykey)+1;
        $temptable->fc_id=(int)$request->input('filecategory');
        $temptable->media_type=$media_type;
        $temptable->media=$media;
        $temptable->mwidth=$request->input('mwidth');
        $temptable->mheight=$request->input('mheight');
        $setting= $request->input('setting');
        $setting = html_entity_decode($setting);
        $setting=json_decode($setting, true);
        $temptable->blongobj=$setting[0]['calledby'];
        $temptable->blongto=$this->args['userinfo']['id']; /*NEED TO UPDATE*/
        $temptable->timestamps = false;
        
        return $temptable;
    }

	public function index(Request $request)
    {
        /*try {
    	   $xx=$this->objtable->popular()->get();
           foreach($xx as $record){
                echo $record["f_id"];
             // ....
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }*/
		
        $categoryid=$request->input('categoryid');
        $pageindex=$request->input('pageindex');
        $setting= $request->input('setting');
        $recentlyadd= $request->input('recentlyadd');
        $perpage=$setting['numperpage'];
        $offset = !empty($pageindex) ? ($pageindex - 1) * $perpage : 0;

        /***check file extendsion to preview **/
        if (empty($setting['filetype'])) {
            $condition = [];
        } else {
            $extension=[];
            $file_extensions=config('ccms.fileextension');
            $chk_filter=explode(",",$setting['filetype']);
            $cnt_filter=count($chk_filter);
            for($i=0;$i<$cnt_filter;$i++){
                $extension=array_merge($extension,array_keys($file_extensions, $chk_filter[$i]));
            }
            $condition = $extension;
        }
        $filedata=$this->objtable->popular($condition,$categoryid,$setting['calledby']);
       
        
        $getdata=$filedata->offset($offset)->take($perpage)->get();
        $total = totalfoundRows();
        if($total>0){
            $filepreview= view('Filemanager.vfilepreview',
                    ["setting"=>$setting, "filedata"=>$getdata->toArray(), "recentlyadd"=>$recentlyadd]
                );


            $filepreview = str_replace("<", "@", $filepreview);
            $filepreview = str_replace("'", "::", $filepreview);

            $return= [

                        'filepreview'=>$filepreview,
                        'totalrecord' => $total
                ];

        //return $filepreview;

        }else{
           $return= [

                        'filepreview'=>'',
                        'totalrecord' =>0
                ]; 
        }
        
        return json_encode($return);

    }

    public function storefile(Request $request)
    {

        $medias=array();
        if ($request->isMethod('post'))
        {
            
            //$validator = Validator::make(array('mwidth'=> 'required'));
            $validator = Validator::make($request->all(), [
                'mwidth' => 'required|numeric',
                'mheight' => 'required|numeric',
            ]);
            
            
            /***check file extendsion to upload **/
            $filerules = array('f_media' => 'required|'.config('ccms.uploadable'));

            $setting= $request->input('setting');
            $setting = html_entity_decode($setting);
            $setting=json_decode($setting, true);
            if (!empty($setting[0]['filetype']))  {
                $extension=[];
                $file_extensions=config('ccms.fileextension');
                $chk_filter=explode(",",$setting[0]['filetype']);
                $cnt_filter=count($chk_filter);
                for($i=0;$i<$cnt_filter;$i++){
                    $extension=array_merge($extension,array_keys($file_extensions, $chk_filter[$i]));
                }
                $filerules = array('f_media' => 'required|mimes:'.implode(',',$extension));
            }

            
            /*************************
            /* Save upload media
            /************************/

           if($files=$request->file('f_media')){
                foreach($files as $file){
                    
                    
                    $filevalidator = Validator::make(array('f_media'=> $file), $filerules);

                    if($filevalidator->passes() && $validator->passes()){
                        $getMimeType=substr($file->getMimeType(),0,5);
                        $name=sanitize_filename($file->getClientOriginalName());
                        $name = preg_replace_callback('/\.\w+$/', function($m){
                           return strtolower($m[0]);
                        }, $name);
      

                        if (File::exists($this->directorypath.'/'.$name))
                        {
                            $name=(string)(date("jnY")).time()."_".$name;

                        }
                        $status=$file->move($this->directorypath,$name);

                        if(!empty($status)){

                            /***************
                                Thumnail Image 
                            /***************/
                           if($getMimeType=='image') {
                                /*
                                * create default thumbnail widht=150
                                */
                                /*
                                $newpath=$this->directorypath.'/_150';
                                File::isDirectory($newpath) or File::makeDirectory($newpath, 0777, true, true);
                                $img = Image::make($this->directorypath.'/'.$name)->fit(150, null);
                                $img->save($newpath.'/'.$name);
                                

                                $thumbnailsize=config('ccms.thumbnailsize');
                                $ind=1;
                                foreach ($thumbnailsize as $key => $sizes) {
                                    $directory = "thumb" . $ind;
                                    $newpath=$this->directorypath.'/' . $directory;
                                    File::isDirectory($newpath) or File::makeDirectory($newpath, 0777, true, true);
                                    $img = Image::make($this->directorypath.'/'.$name)->fit($sizes[0], $sizes[1]);
                                    $img->save($newpath.'/'.$name);
                                    $ind++;
                                }
                                */
                                $this->generate_thumb($name);

                                
                            } 

                            /***************
                                Save to DB 
                            /***************/

                            $tmptable=$this->setinfo($request,'internal',$name);
                            $tmptable->save();
                            $medias[]=$tmptable->f_id;
                            
                        }
                        

                        
                    }
                    
                }

            }
            
            /*************************
            /* Save internal media with webcam
            /************************/
            if(!empty($request->input('webcam'))){
                $encoded_data  = $request->input('webcam');
                $binary_data = base64_decode($encoded_data);
                $filename = (string)(date("jnY")).time().'_webcam.jpg';
                $status = file_put_contents($this->directorypath.'/'.$filename, $binary_data );
                if(!empty($status)){
                    $this->generate_thumb($filename);
                    $tmptable=$this->setinfo($request,'internal',$filename);
                    $tmptable->save();
                    $medias[]=$tmptable->f_id;
                }
            }

            /*************************
            /* Save external media
            /************************/
            if(!empty($request->input('txt_media'))){
                $tmptable=$this->setinfo($request,'external',$request->input('txt_media'));
                $tmptable->save();
                $medias[]=$tmptable->f_id;
            }

          
        }/*end if POST*/

        if(!empty($medias))
        {
            $return= [
                'uploadinfo'=> [

                    'status'=>'success', 
                    'cssclass'=>'alert alert-success pding-5', 
                    'text'=>__('ccms.upload_success')
                ],
                
                'preview'   => [

                    'preview_info'=>implode(",", $medias),
                    'totalmedia' => $this->objtable->get()->count()
                ]
            ];
        }
        else
        {
            $return= [
                'uploadinfo'=>[
                    'status'=>'fail', 
                    'cssclass'=>'alert alert-danger pding-5', 
                    'text'=>'<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.__('ccms.upload_fail')
                ]
            ];
        }

        return json_encode($return);

    }/*end function*/	


    public function destroyfile(Request $request)
    {
      $f_id=empty($request->input('fileid'))?0:$request->input('fileid');

      $file = $this->objtable->find($f_id);
      if($file)
      {
        $name=$file->media;
        if (File::exists($this->directorypath.'/'.$name))
        {
            File::delete($this->directorypath.'/'.$name);
            $newpath=$this->directorypath.'/_150';
            File::delete($newpath.'/'.$name);

            $thumbnailsize=config('ccms.thumbnailsize');
            $ind=1;
            foreach ($thumbnailsize as $key => $sizes) {
                $directory = "thumb" . $ind;
                $newpath=$this->directorypath.'/' . $directory;
                File::delete($newpath.'/'.$name);
                $ind++;
            }

            /**delete from table*/
            $file->delete();

            $return= [
                'deleteinfo'=> [

                    'status'=>'success', 
                    'cssclass'=>'alert alert-success pding-5', 
                    'text'=>__('ccms.df_success')
                ]
            ];



        }else{
            //do s.th here
            $return= [
                'deleteinfo'=>[
                    'status'=>'fail', 
                    'cssclass'=>'alert alert-danger pding-10', 
                    'text'=>'<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.__('ccms.df_faile')
                ]
            ];
        }

        return json_encode($return);

      }


    }   /*end function*/


    public function objgetfile(Request $request)
    {
        /*try {
           $xx=$this->objtable->popular()->get();
           foreach($xx as $record){
                echo $record["f_id"];
             // ....
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }*/
        

        $existingfile=$request->input('existingfile');
        $newfiles= $request->input('newfiles');
        $tblfileobject=$request->input('tblfileobject');
        $objid=$request->input('objid');
        $objid= explode(',', $objid);
        $categoryid=$request->input('categoryid');
        $setting= $request->input('setting');
        $filedata=[];
        $data_insert=[];
        $data_preview=[];

        if($existingfile=='yes')
        {
            try {
               $filedata=$this->objtable->Getfileofobj($tblfileobject,$objid,$categoryid)->get();
            } catch (\Exception $e) {
                return "error";
            }

            $array_data= $filedata->toArray();

            if(!empty($array_data)){
                

                foreach ($array_data as $record) {
                   
                    array_push($data_preview, 
                        [
                            'objf_id'   =>  $record->objf_id,
                            'file_name' =>  $record->file_name,
                            'f_type'    =>  $record->f_type,
                            'as_cover'  =>  $record->as_cover,
                            'as_bg'     =>  $record->as_bg
                        ]
                    );
                }

                $filepreview= view('Filemanager.vfilepreviewobj',
                        ["setting"=>$setting, "filedata"=>$data_preview, "recentlyadd"=>'']
                    );


                $filepreview = str_replace("<", "@", $filepreview);
                $filepreview = str_replace("'", "::", $filepreview);

                $return= [

                            'filepreview'=>$filepreview
                        ];

                

            }else{
               $return= [

                            'filepreview'=>''
                        ]; 
            }

        }
        else
        {
            //Getfiletoobj
            $arr_newfiles=explode(',', $newfiles);
            try {
               $filedata=$this->objtable->Getfiletoobj($arr_newfiles,0,$setting['calledby'])->get();
            } catch (\Exception $e) {
                return "error";
            }

            /********************************************************/
            $array_data= $filedata->toArray();

            if(!empty($array_data)){

                foreach ($array_data as $record) {
                        array_push($data_insert, 
                            [
                                'objf_id'   =>  0,
                                'obj_id'    =>  0,
                                'fc_id'    =>  0,
                                'file_name' =>  $record['media'],
                                'f_type'    =>  $record['media_type'],
                                'fwidth'    =>  (int)$record['mwidth'],
                                'fheight'   =>  (int)$record['mheight'],
                                'scr_name'  =>  '',
                                'as_cover'  =>  '',
                                'as_bg'     =>  '',
                                'title'     =>  '',
                                'ordering'  =>  0,
                                'piccolor'     =>  0,
                                'tag'       =>  '',
                                'blongto'   =>  $this->args['userinfo']['id']
                            ]
                        );

                }/*end foreach*/
                
                if($setting['displaymode']==2)
                {
                    $filepreview=$record['media'];
                }
                elseif($setting['displaymode']==3)
                {
                    //$filepreview=asset('/resources/filelibrary/'.$record['media']);

                    $filepreview=$record['media'];


                    //$filepreview=['path'=>resource_path('filelibrary/'), 'file'=>$record['media']];
                    //storage_path('/resources/filelibrary/_150/'.$record['media']);
                    //resource_path('filelibrary/').$record['media'];
                    //URL::asset('/resources/filelibrary/_150/'.$record['media']);
                }
                elseif($setting['displaymode']==4)
                {
                  $recentlyadd='';
                    foreach ($data_insert as $record) {
                        array_push($data_preview, 
                            [
                                'objf_id'   =>  0,
                                'file_name' =>  $record['file_name'],
                                'f_type'    =>  $record['f_type'],
                                'as_cover'  =>  '',
                                'as_bg'     =>  ''
                            ]
                        );


                    }

                    $filepreview= view('Filemanager.vfilepreviewobj_list',
                            ["setting"=>$setting, "filedata"=>$data_preview, "recentlyadd"=>"yes"]
                        );



                    $filepreview = str_replace("<", "@", $filepreview);
                    $filepreview = str_replace("'", "::", $filepreview);
                }

                else
                {
                    

                    $save = $this->objtable->Inserfiletoobj($tblfileobject,$data_insert);

                    $recentlyadd='';
                    foreach ($data_insert as $record) {
                        array_push($data_preview, 
                            [
                                'objf_id'   =>  $save++,
                                'file_name' =>  $record['file_name'],
                                'f_type'    =>  $record['f_type'],
                                'as_cover'  =>  '',
                                'as_bg'     =>  ''
                            ]
                        );


                    }

                    $filepreview= view('Filemanager.vfilepreviewobj',
                            ["setting"=>$setting, "filedata"=>$data_preview, "recentlyadd"=>"yes"]
                        );



                    $filepreview = str_replace("<", "@", $filepreview);
                    $filepreview = str_replace("'", "::", $filepreview);
                }


                

                $return= [

                            'filepreview'=>$filepreview
                        ];

                

            }else{
               $return= [

                            'filepreview'=>''
                        ]; 
            }
        
        }
        /**/

        
        
        //return $return;
        return json_encode($return);

    }/*end fun*/


    public function getfileinfo(Request $request)
    {
      $tblfileobject=$request->input('tblfileobject');
      $f_id=empty($request->input('fileid'))?0:$request->input('fileid');

      $file = $this->objtable->Getfileinfo($tblfileobject,$f_id)->get()->toArray();
      if($file)
      {
        
        $return= [

                            'fileinfo'=>$file[0]
                        ];

      }
      else{
            
      }/*end else*/

        return json_encode($return);


    }   /*end function*/


    public function removefile(Request $request)
    {
      $tblfileobject=$request->input('tblfileobject');
      $f_id=empty($request->input('fileid'))?0:$request->input('fileid');

      $file = $this->objtable->Removefileobj($tblfileobject,$f_id);
      if($file)
      {
        /**delete from table*/

            $return= [
                'deleteinfo'=> [

                    'status'=>'success', 
                    'cssclass'=>'alert alert-success pding-5', 
                    'text'=>__('ccms.df_success').$file
                ]
            ];
        

      }
      else{
            //do s.th here
            $return= [
                'deleteinfo'=>[
                    'status'=>'fail', 
                    'cssclass'=>'alert alert-danger pding-10', 
                    'text'=>'<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.__('ccms.df_faile').$file
                ]
            ];
        }/*end else*/

        return json_encode($return);


    }   /*end function*/


    public function savesetting(Request $request)
    {

        if ($request->isMethod('post'))
        {
            /*** SET INFO ***/
            $objid= $request->input('txt_objid');
            $field_id = $request->input('txt_objfid');
            $scr_name = $request->input('txt_scrshot');
            $fwidth = $request->input('txt_w');
            $fheight = $request->input('txt_h'); 
            $as_cover = null !==$request->input('chk_fcover')?$request->input('chk_fcover'):""; 
            $as_bg = null !==$request->input('chk_fbg')?$request->input('chk_fbg'):""; 
            $ordering = $request->input('txt_forder'); 
            $piccolor = null !==$request->input('cmb_color')?$request->input('cmb_color'):0; 
            $tag =null !==$request->input('txt_ftag')?$request->input('txt_ftag'):"";   

            $setting= $request->input('setting');
            $setting = html_entity_decode($setting);
            $setting=json_decode($setting, true);
            
            /*for translate field*/
              $translate=[];
              foreach (config('ccms.multilang') as $lang)
              {
                /*$lg_code=$lang[0];
                $translate.=$lg_code.config('ccms.v_separate').$request->input('txtfiletitle_'.$lg_code).config('ccms.f_separate');*/

                $translate[$lang[0]]=$request->input('txtfiletitle_'.$lang[0]);
              } 
            /*end for translate field*/

            $updateinfo=[

                                'fwidth'    =>  (int)$fwidth,
                                'fheight'   =>  (int)$fheight,
                                'scr_name'  =>  $scr_name,
                                'as_cover'  =>  $as_cover,
                                'as_bg'     =>  $as_bg ,
                                'title'     =>  json_encode($translate, JSON_UNESCAPED_UNICODE),
                                'ordering'  =>  (int)$ordering,
                                'piccolor'     => (int)$piccolor,
                                'tag'       =>  $tag 
                            ];
            $tblfileobject=$setting[0]['objtable'];
            $fathertable=$setting[0]['fathertable'];
            $fatherid=$setting[0]['fatherid'];

            try {
                
                if($as_cover=='yes')
                {
                    $cv_bl=$this->objtable->Updateblank($tblfileobject,'as_cover',$objid);

                    /*if(!empty($fathertable))
                    {

                        $file = $this->objtable->Getfileinfo($tblfileobject,$field_id)->get()->toArray();
                        
                        $update = $this->objtable->Updatefatherimage($fathertable, $fatherid, $objid,['imginfo'=>json_encode($file[0])]);

                    }*/
                }

                if($as_bg=='yes')
                {
                    $bg_bl=$this->objtable->Updateblank($tblfileobject,'as_bg',$objid);
                }

                /**** UPDATE DATA **/
                $updatestatus = $this->objtable->Updatefileinfo($tblfileobject,$field_id,$updateinfo);

                $cover_now=$this->objtable->Seekcover_bg($tblfileobject,'as_cover',$objid);
                $bg_now=$this->objtable->Seekcover_bg($tblfileobject,'as_bg',$objid);



                $return=[
                    'id'=>$field_id,
                    'cover'=> $cover_now,
                    'bg' => $bg_now,
                ];
            
           } catch (\Exception $e) {
                $return=['err'=>$e];
            }
            
            
        }

        return json_encode($return);

    }/*end function*/   
    
    private function generate_thumb($filename){
        /*
        * create default thumbnail widht=150
        */
        $newpath=$this->directorypath.'/_150';
        File::isDirectory($newpath) or File::makeDirectory($newpath, 0777, true, true);
        $img = Image::make($this->directorypath.'/'.$filename)->fit(150, null);
        $img->save($newpath.'/'.$filename);
        /*end*/

        $thumbnailsize=config('ccms.thumbnailsize');
        $ind=1;
        foreach ($thumbnailsize as $key => $sizes) {
            $directory = "thumb" . $ind;
            $newpath=$this->directorypath.'/' . $directory;
            File::isDirectory($newpath) or File::makeDirectory($newpath, 0777, true, true);
            $img = Image::make($this->directorypath.'/'.$filename)->fit($sizes[0], $sizes[1]);
            $img->save($newpath.'/'.$filename);
            $ind++;
        }
    }
	
    
}