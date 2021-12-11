<?php
return [
    'realedit' => true,
    'backend'=>'', /*change this, you need to change ajax_path too*/
    'js_env'=>[

        /*to run compser we need to comments thes two lines*/
        'ajaxpublic_url'=>'/ajax/ajax_access',
        'ajaxadmin_url'=>'/ajax_access', /*some ajax need authentication by admin login*/

        /*'ajaxpublic_url'=>Config('app.url').'/ajax/ajax_access',
        'ajaxadmin_url'=>Config('app.url').'/admin/ajax_access', /*some ajax need authentication by admin login*/
        'token'=>''
    ],

    'ajax_paths' => [
        "ajax_plugin" => "App\Plugins",
        "ajax_obj" => "App\Http\Controllers\Backend"
    ],

    'trackingact' => ['store','update','duplicate','delete','restore','destroy','remove','change', 'edit_field', 'storequotation'],

    'bankendlang_old' => [
        'en' => 'en',
        'kh' => 'kh'
    ],
  
    'bankendlang' => [
        'en' =>  ['en','English','English','en.gif'],
        'kh' => ['kh','Khmer','ខ្មែរ','kh.gif']
    ],

    'multilang' => [
        
        ['en','English','English','en.gif'],
        ['kh','Khmer','ខ្មែរ','kh.gif'],
        //['cn','Chinese','Chinese','kh.gif'],
        
    ],
  
    


    /*-Article-*/

    'linktype'=>[
        '1'=>'Customize',
        '2'=>'http://',
        '3'=>'https://',
        '4'=>'mailto:',
        '5'=>'Page'
    ],

    'linktarget'=>[
        /*'_none'=>'_none','_blank'=>'_blank','_new'=>'_new','_parent_self'=>'_parent_self','_top'=>'_top'*/
        '_self'=>'_self','_blank'=>'_blank'
    ],

    'permission'=>[
        '1'=>'Public',
        '2'=>'Private',
        '3'=>'Password'
    ],

    /*-File policy and upload setting-*/

    'uploadable'=>'mimes:png,gif,jpg,jpeg,txt,pdf,doc,docx,xls,xlsx,zip,rar',

    'fileextension'=>[
            "jpg"=>"image",
            "jpeg"=>"image",
            "gif"=>"image",
            "png"=>"image",
            "ico"=>"image",
            "swf"=>"swf",
            "flv"=>"flasvideo",
            "mov"=>"quicktime",
            "wmv"=>"wmedia",
            "youtube"=>"youtube",
            "mp3"=>"sound",
            "pdf"=>"docs",
            "xls"=>"docs",
            "xlsx"=>"docs",
            "doc"=>"docs",
            "docx"=>"docs",
            "pptx"=>"docs",
            "zip"=>"zip",
            "rar"=>"zip",
            "txt"=>"docs"
        
    ],

    'allowtags' => '<a><table><tr><td><th><img><embed><ul><ol><li><span><b><i><u><font><hr><strike><sub><sup><br><br/><strong><p><div>',

//     'thumbnailsize'=> [
//         [120,null],[180,null]
//     ],
  
  'thumbnailsize'=> [
       
    ],

    

    'rpp'=> 15, #record per page#
    'perpage'=> ['15'=>15, '30'=>30, '50'=>50, '100'=>100], #record per page#

    'datalist' => [
        'size' => 2,
        'color' => 1
    ],

     'attribute'=>[
        '1'=>'Textbox',
        '2'=>'Translate Textbox',
        '3'=>'Select',
        '4'=>'Multi-Select',
        '5'=>'Textarea',
        '6'=>'Translate Textarea',
        '7'=>'Date',
        '8'=>'Date-Time',
        '9'=>'Image',
        '10'=>'Translate Image',
        '11'=>'File',
        '12'=>'Translate File'

    ],

    

    //'calunit'=>[],

    'protectact' => [
                'index'     =>  ['index', 'index','Index'],
                'create'    =>  ['create', 'create','Create'],
                'duplicate' =>  ['duplicate', 'create','Create'],
                'store'     =>  ['store', 'create','Create'],
                'edit'      =>  ['edit', 'edit','Edit'],
                'update'    =>  ['update', 'edit','Edit'],
                'delete'    =>  ['delete', 'delete','Delete'],
                'restore'   =>  ['restore', 'restore','Restore'],
                'destroy'   =>  ['destroy', 'destroy','Destroy'],
                'duplicate' =>  ['duplicate', 'duplicate','Duplicate'],

            ],

    'device_status' => ['on'=>'Online', 'off'=>'Offline'],
    'datatype' => ['minute'=>'Minute', 'hour' => 'Hour', 'week' => 'Week'],
  
  

];

?>