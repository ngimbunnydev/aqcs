function openMediaPanel(categoryid){

	//$('#media_center').modal();
	$("#media_center").modal("show").on('shown.bs.modal', function () {

		if ($('#setting_center').is(':visible') || $('#modal_windows').is(':visible')) {
		    var idx = $('.modal:visible').length;
    		$(this).css('z-index', 1040 + (10 * idx));
		}


    	
    });
    $('#txt_chked_tmp').val('');
    buildFileCategoryTree();
	  getNewPage(categoryid,1,'');

    
	
}//end

/**
* Generate Tree of File category 
*/
function buildFileCategoryTree(){
	$("#file-category-tree").jstree("destroy");
	var jsonData=ajaxGetFileCategoryTree();
	$('#file-category-tree')
			  // listen for event
			.on('select_node.jstree', function (e, data) {
			    $('#filecategory').val(data.node.id);
			    getNewPage(data.node.id,1,'');
			})
			.on('create_node.jstree', function(e, data) {
		            console.log('saved');
		            //alert("ma em");
		     })
			.on('rename_node.jstree', function(e, data) {
					var $id=data.node.id;
					var $newName=data.node.text.trim();
					var $oldname=data.node.original.text.trim();

					if($newName!=$oldname){
						var $newName=renameFileCategory($id,$newName);
						if ($newName!='update') {
							var tree = $('#file-category-tree').jstree();
							tree.edit(data.node);
						}
					}else{
						data.node.text=$oldname;
					}
					
		            console.log('saved');
		     })
			.jstree({
				'plugins': ["contextmenu"],
				'contextmenu': {
			        'select_node': false,
			        'items': treeContextMenu
			    },
			    'core': {
		          'check_callback': true,
		          'data':jsonData
		        }

	    	});

}/*end*/

function treeContextMenu(node) {
  //alert('Node id ' + node.id);
  // build your menu depending on node id
  var tree = $('#file-category-tree').jstree();
  return {
    createItem : {
      "label" : "New",
      "action" : function(obj) { 
      			var $data=createNewFileCategory(node.id);
      			var $node = tree.create_node(node,{"id": $data.id,"text": $data.name},'last');
                tree.edit($node);
      			},
      "_class" : "class"
    },

    renameItem : {
      "label" : "Rename",
      "action" : function(obj) { 
      				if(node.id!=0)
      				tree.edit(node);
      			}
    }
  };  
}/*end*/

function createNewFileCategory(nodeid){
	var $rt=$.ajax({
	  		url:filemanagerSetting.ajax_url,
			type: 'POST',
			dataType: 'html',
			async: false,
			data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filecategory',
				ajaxact:'storecategory',
				permission:'no',
				nodeid:nodeid,
				setting:filemanagerSetting
			},
			error: function (response, status, e)
			{
				//alert('createNewFileCategory:'+response);
			}
            
            
	}).responseText;	
	clearconsole();
	/*end $.ajax*/
	return $.parseJSON($rt);
    
}/*end fun*/

function renameFileCategory(nodeid,newName){
	var $rt=$.ajax({
	  		url:filemanagerSetting.ajax_url,
			type: 'POST',
			dataType: 'html',
			async: false,
			data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filecategory',
				ajaxact:'updatecategory',
				permission:'no',
				nodeid:nodeid,
				newName:newName
			},
			error: function (response, status, e)
			{
				alert(response);
			}
            
            
	}).responseText;
	clearconsole();	
	/*end $.ajax*/
	return $rt;
    
}/*end fun*/


function ajaxGetFileCategoryTree(){
	var $getjson=$.ajax({
	  		url:filemanagerSetting.ajax_url,
			type: 'POST',
			dataType: 'html',
			async: false,
			data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filecategory',
				ajaxact:'index',
				permission:'no',
				setting:filemanagerSetting
			},
			error: function (response, status, e)
			{

				alert('ajaxGetFileCategoryTree: ' + response);
			}
            
            
	}).responseText;
	clearconsole();	
	/*end $.ajax*/
	return $.parseJSON($getjson);
    
}/*end fun*/

function ajaxMediaUpload(categoryid,frmID) {
    
    /**/
        var form = $("#"+frmID); 
        var fd = new FormData(form[0]);                 
        
        fd.append('_token',env.token);
        fd.append('ajaxpath','ajax_plugin');
        fd.append('objpath','Filemanager');
        fd.append('ajaxobj','Filemanager');
        
        fd.append('ajaxact','storefile');
        fd.append('permission','no');

        var objArr = [];
		objArr.push(filemanagerSetting);

        fd.append('setting',JSON.stringify( objArr ));

        //alert(fd.get('chk_uc'));
        
 
    /**/
    
    $.ajax({
        url: filemanagerSetting.ajax_url,
		type: 'POST',
		secureuri: false,
		dataType: 'html',
		data: fd,
		cache: false,
        processData: false,
        contentType: false,
        
		success: function(data, status, e) {
			var json = $.parseJSON(data);
			$("#filemanagermsg").html("<div class='" + json.uploadinfo.cssclass + "'>" + json.uploadinfo.text + "</div><br>");
			/*Reset form data*/
			form[0].reset();
			form.find('input[type=file]').ace_file_input('reset_input_ui');

			/*-load file to panel-*/
			if(json.uploadinfo.status=='success'){
				$('#pagination').twbsPagination('destroy');
				getNewPage(categoryid,1, json.preview.preview_info);


				/*upload and use */
				if(fd.get('chk_uc')=='yes'){
					objGetFiles('no',json.preview.preview_info,filemanagerSetting.objtable,filemanagerSetting.idvalue,0);
				}
			}

			
		},
		error: function(data, status, e) {
			//alert(JSON.stringify(data));
		}
        
        
	});
    clearconsole();
} /*end function*/

/** initalize ***/
$('#f_media').ace_file_input({
					style: 'well',
					btn_choose: 'Drop files here or click to choose',
					btn_change: null,
					no_icon: 'ace-icon fa fa-cloud-upload bigger-300',
					droppable: true,
					thumbnail: 'small'//small | large | fit
					//,icon_remove:null//set null, to hide remove/reset button
					/**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
					/**,before_remove : function() {
						return true;
					}*/
					,
					preview_error : function(filename, error_code) {
						//name of the file that failed
						//error_code values
						//1 = 'FILE_LOAD_FAILED',
						//2 = 'IMAGE_LOAD_FAILED',
						//3 = 'THUMBNAIL_FAILED'
						//alert(error_code);
					}
			
				}).on('change', function(){
					//console.log($(this).data('ace_input_files'));
					//console.log($(this).data('ace_input_method'));
});

/*************After upload********************/
function getNewPage(categoryid,pageIndex, recently_added) {


	$.ajax({
		url:filemanagerSetting.ajax_url,
		type: 'POST',
		secureuri:false,
		dataType: 'html',
		//async: false,
		data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filemanager',
				ajaxact:'index',
				permission:'no',
				categoryid:categoryid,
				pageindex:pageIndex,
				setting:filemanagerSetting,
				recentlyadd:recently_added
		},
		success: function(data, status) {

			var json = $.parseJSON(data);
			var $totalrecords=json.totalrecord;
			var $numperpage=filemanagerSetting.numperpage;
			var $filepreview=json.filepreview;
	
			$('#filepreview').html($filepreview.replace(/@/gi, "<").replace(/::/gi, "'"));
			if ($totalrecords > $numperpage) {
						$(function () {

					        var obj = $('#pagination').twbsPagination({
					            totalPages: Math.ceil($totalrecords/$numperpage),
					            startPage:1,
					            visiblePages: 5,
					            initiateStartPageClick: false,
					            onPageClick: function (event, page) {
								       getNewPage(categoryid, page,'');
								   
					            }
					        });
					        
					    });
			}/*endif*/
			else{
				$('#pagination').twbsPagination('destroy');
			}
		},
		error: function(data, status, e) {
			//alert(data);
		}
	}); 
    clearconsole();
    //end $.ajax

    


} //end function


function deleteFile(selector,fileid){
	$.ajax({
	  		url:filemanagerSetting.ajax_url,
			type: 'POST',
			dataType: 'html',
			//async: false,
			data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filemanager',
				ajaxact:'destroyfile',
				permission:'no',
				setting:filemanagerSetting,
				fileid:fileid
			},

			success: function(data, status) {
				var json = $.parseJSON(data);
				//$("#filemanagermsgtop").addClass(json.deleteinfo.cssclass);
				$("#filemanagermsgtop").html("<div class='" + json.deleteinfo.cssclass + "'>" + json.deleteinfo.text + "</div>");
				if(json.deleteinfo.status=='success'){
					selector.remove();
				}

				
			},
			error: function (response, status, e)
			{
				alert(response);
			}
            
            
	});
	clearconsole();	
	/*end $.ajax*/

	return false;
    
}/*end fun*/


function checkedStat(chkBox){

    var tmpFile=$('#txt_chked_tmp').val();
    var tmpFiles=new Array;
    if(tmpFile!=""){tmpFiles=tmpFile.split(",");}
    if(chkBox.is(':checked')){
        tmpFiles.push(chkBox.val());
    }else{
        var a = tmpFiles.indexOf(chkBox.val());
        tmpFiles.splice(a,1);
    }
    $('#txt_chked_tmp').val(tmpFiles.toString()); 

}/*end fun*/



function objGetFiles(existingfile,newfiles,tblObjectFile,objIdVal,categoryid) {
	/* existingfile yes|no => if YES, get files from object which added before*/
	/* newfiles String '1,2,3..,n' of id file you wish to add more to object.*/

	$.ajax({
		url:filemanagerSetting.ajax_url,
		type: 'POST',
		secureuri:false,
		dataType: 'html',
		//async: false,
		data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filemanager',
				ajaxact:'objgetfile',
				permission:'no',
				
				existingfile:existingfile,
				newfiles:newfiles,
				tblfileobject:tblObjectFile,
				objid:objIdVal,
				categoryid:categoryid,
				setting:filemanagerSetting
		},
		success: function(data, status) {
			//alert(data);
			var json = $.parseJSON(data);
			//var $totalrecords=json.totalrecord;
			//var $numperpage=filemanagerSetting.numperpage;
			var $filepreview=json.filepreview;

			if(filemanagerSetting.displaymode==2)
			{
				

				if(filemanagerSetting.givent_txtbox== "object")
				{
					//givent_txtbox is a global Variable.
          if(givent_txtbox.prev().is('img')){
              //Do something
            $(givent_txtbox.prev()).attr("src",filemanagerSetting.url+"/"+$filepreview);
          }
					givent_txtbox.val($filepreview);
				}else
				{
					$('#'+filemanagerSetting.givent_txtbox).val($filepreview);
				}

				$('#media_center').modal('toggle');
			}
			else if(filemanagerSetting.displaymode==3){
				//alert($filepreview.path);
				editorContext.invoke('editor.insertText', '@widget("img",["fname"=>"'+$filepreview+'"])');
				//editorContext.invoke('editor.insertImage', $filepreview.path, $filepreview.file);
				//var imgNode = $('<img>').attr('src', $filepreview).attr('width', '100%')[0];
  				//editorContext.invoke('editor.insertNode', imgNode);
				$('#media_center').modal('toggle');
			}
      else if(filemanagerSetting.displaymode==4){
        givent_txtbox.children('.ace-file-input').remove();
        givent_txtbox.append($filepreview.replace(/@/gi, "<").replace(/::/gi, "'"));
        $('#media_center').modal('toggle');
      }
			else
			{
				$('#file_container li.border-recent').removeClass('border-recent');
				$('#file_container').prepend($filepreview.replace(/@/gi, "<").replace(/::/gi, "'"));
				if(existingfile!='yes')
				$('#media_center').modal('toggle');
			}
			

			
		},
		error: function(data, status, e) {
			alert(JSON.stringify(data));
		}
	}); 
    clearconsole();
    //end $.ajax

    


} //end function


function removeFile(selector,tblObjectFile,fileid){
	$.ajax({
	  		url:filemanagerSetting.ajax_url,
			type: 'POST',
			dataType: 'html',
			//async: false,
			data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filemanager',
				ajaxact:'removefile',
				permission:'no',
				setting:filemanagerSetting,
				tblfileobject:tblObjectFile,
				fileid:fileid
			},

			success: function(data, status) {
				var json = $.parseJSON(data);
				//$("#filemanagermsgtop").addClass(json.deleteinfo.cssclass);
				//$("#filemanagermsgtop").html("<div class='" + json.deleteinfo.cssclass + "'>" + json.deleteinfo.text + "</div>");
				if(json.deleteinfo.status=='success'){
					selector.remove();
				}

				
			},
			error: function (response, status, e)
			{
				alert(JSON.stringify(response));
			}
            
            
	});
	clearconsole();	
	/*end $.ajax*/

	return false;
    
}/*end fun*/


function loadSettingmanager(tblObjectFile,file_edit) {

        $("#setting_center").find("input:text").val("");
        $("#setting_center").find("input:checkbox").prop( "checked", false );
        $('#setting_center').find("select").prop("selectedIndex",0);
        $("#setting_center").modal("show");

        getFileOfObj(tblObjectFile,file_edit);
        
}


function getFileOfObj(tblObjectFile,fileid) {

	$.ajax({
	  		url:filemanagerSetting.ajax_url,
			type: 'POST',
			dataType: 'html',
			//async: false,
			data:{_token:env.token,
				ajaxpath:'ajax_plugin',
				objpath:'filemanager',
				ajaxobj:'Filemanager',
				ajaxact:'getfileinfo',
				permission:'no',
				setting:filemanagerSetting,
				tblfileobject:tblObjectFile,
				fileid:fileid
			},

			success: function(data, status) {
				//alert(data);
				var json = $.parseJSON(data);
				$id = json.fileinfo.objf_id;
                $objid = json.fileinfo.obj_id;
                $filename = json.fileinfo.filename;
                $type = json.fileinfo.f_type;
                $w = json.fileinfo.fwidth;
                $h = json.fileinfo.fheight;
                $sname = json.fileinfo.scr_name;
                $cover = json.fileinfo.as_cover;
                $bg = json.fileinfo.as_bg;
                $order = json.fileinfo.ordering;
                $piccolor = json.fileinfo.piccolor;
                $tag = json.fileinfo.tag;
                
                
                $("#txt_objid").val($objid);
                $("#txt_objfid").val($id);
                $("#txt_getmedia").val($filename);
                $("#txt_mtype").val($type);
                $("#txt_w").val($w);
                $("#txt_h").val($h);
                $("#txt_scrshot").val($sname);
                if($cover=='yes'){
                    $("#chk_fcover").prop( "checked", true );
                }else{
                    
                    $("#chk_fcover").prop( "checked", false );
                }
                if($bg=='yes'){
                    $("#chk_fbg").prop( "checked", true );
                }else{
                    $("#chk_fbg").prop( "checked", false );
                }
                
                $("#txt_forder").val($order);
                $('#cmb_color').val($piccolor);
                 $("#txt_ftag").val($tag);
                
                //for Title
                if(json.fileinfo.title!=''){
                	$title = $.parseJSON(json.fileinfo.title);
                	$.each($title, function(key,value) {
	                  value=$("<div/>").html(value).text();
					  $("#txtfiletitle_" + key ).val(value);
					});
                }
                
              

				
			},
			error: function (response, status, e)
			{
				alert(JSON.stringify(response));
			}
            
            
	});
	clearconsole();	
	/*end $.ajax*/

	return false;
} //end fun

function saveFileSetting(frmID) {
    
    /**/
        var form = $("#"+frmID); 
        var fd = new FormData(form[0]);                 
        
        fd.append('_token',env.token);
        fd.append('ajaxpath','ajax_plugin');
        fd.append('objpath','Filemanager');
        fd.append('ajaxobj','Filemanager');
        
        fd.append('ajaxact','savesetting');
        fd.append('permission','no');

        var objArr = [];
		objArr.push(filemanagerSetting);

        fd.append('setting',JSON.stringify( objArr ));
    /**/
    
    $.ajax({
        url: filemanagerSetting.ajax_url,
		type: 'POST',
		secureuri: false,
		dataType: 'html',
		data: fd,
		cache: false,
        processData: false,
        contentType: false,
        
		success: function(data, status, e) {
			//$("#file_container div.tags .as_cover").not('#cover-'+file_edit).remove();
			//alert(data);
			var json = $.parseJSON(data);

			$("#file_container div.tags .as_cover").html('');
			$("#file_container div.tags .as_bg").html('');
			if(json.cover!=''){
				$("#file_container div.tags #cover-"+json.cover).html('<span class="label label-success arrowed">Cover</span>');
			}

			if(json.bg!=''){
				$("#file_container div.tags #bg-"+json.bg).html('<span class="label label-danger arrowed">Background</span>');
			}

			$("#setting_center").modal("toggle");
			
			
		},
		error: function(data, status, e) {
			alert(e);
		}
        
        
	});
    clearconsole();
} /*end function*/


/*88888888888888888888888888888888 LISTENER 8888888888888888888888888888888888888*/
$( document )
.ajaxStart(function() {
	$("#fileloading,#filesettingloading,#span-media-submit").show();
})
.ajaxComplete(function() {
	$("#fileloading,#filesettingloading,#span-media-submit").hide();
});

$('#media_center').on('hidden.bs.modal', function (){
   // do something ...
   $('#filecategory').val(0);
   $('#filepreview').html('');
   $('#filemanagermsgtop').html('');
   $('#filemanagermsg').html('');
   $('#pagination').twbsPagination('destroy');
   closeSnapshot();
 });



/**-Upload Button-**/
$("#btn-media-submit").click(function(e){
        e.preventDefault();
        categoryid=$('#filecategory').val();
        ajaxMediaUpload(categoryid,'frm_mediacenter');
});

/**-File item select-wait until Ajax complete just we can detect some event bcoz this selector just coming by ajax**/
$( document ).ajaxComplete(function() {

  	$(".filteitem").click(function(e){
        //alert($(this).attr('id'));
        var selected_file=$('#txt_chked_tmp').val();
    	if(selected_file=="")
    	{
    		selected_file=$(this).attr('id')
		}

        objGetFiles('no',selected_file,filemanagerSetting.objtable,filemanagerSetting.idvalue,0);
        e.stopImmediatePropagation();
        e.preventDefault();
	});

	$(".chk_multiadd").click(function(e){
		//alert($(this).val());
        checkedStat($(this));
       // e.stopImmediatePropagation();
        //e.preventDefault();
	});



	$(".filteitem-del").click(function(e) {
		//alert($(this).attr('id'));
		
		var filteitem=$(this);
		bootbox.confirm({

			size: "small",
		    message: jsconfig.jsmessage.df_confirm,
		    buttons: {
		        confirm: {
		            label: 'Yes',
		            className: 'btn btn-white btn-success btn-sm'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn btn-white btn-danger btn-sm'
		        }
		    },
		    callback: function (result) {
		        if(result==true){
		        	var selector=filteitem.parent( "div" ).parent("li");
	        		deleteFile(selector,filteitem.attr('id'));
		        }
		    }
		});

		
        e.stopImmediatePropagation();
        e.preventDefault();
	});


});

/**********************end ready***************/

/*88888888888888888888888888888888 LISTENER @ OBJECT SIDE 8888888888888888888888888888888888888*/


$( document ).ajaxComplete(function() {

	$(".filteitemobj-setting").click(function(e){
		
		var filteitem=$(this);
		loadSettingmanager(filemanagerSetting.objtable,filteitem.attr('id'));

		e.stopImmediatePropagation();
	    e.preventDefault();
		
	});

	/**-Save Button @ Setting-**/
	$("#btn-setting-submit").click(function(e){
	    saveFileSetting('frm_filesetting');

	    e.stopImmediatePropagation();
	    e.preventDefault();
	});

	$("#btn-browe-scrsh").click(function(e){
	    filemanagerSetting.displaymode=2;
	    filemanagerSetting.filetype='image';
	    categoryid=$('#filecategory').val();
		openMediaPanel(categoryid);
	    e.stopImmediatePropagation();
	    e.preventDefault();
	});



	$(".filteitemobj-del").click(function(e) {
			//alert($(this).attr('id'));

		var filteitem=$(this);
		bootbox.confirm({

			size: "small",
		    message: jsconfig.jsmessage.df_confirm,
		    buttons: {
		        confirm: {
		            label: 'Yes',
		            className: 'btn btn-white btn-success btn-sm'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn btn-white btn-danger btn-sm'
		        }
		    },
		    callback: function (result) {
		        if(result==true){
		        	var selector=filteitem.parent( "div" ).parent("li");
	        		removeFile(selector,filemanagerSetting.objtable,filteitem.attr('id'));
		        }
		    }
		});
			

			
	        e.stopImmediatePropagation();
	        e.preventDefault();
	});
  
  $(".deletfile").click(function(e) {
			//alert($(this).attr('id'));

		 e.stopPropagation();
		    	e.preventDefault();
          
				  var parent = $(this).parent().parent();
          var topparent = parent.parent();
           
          parent.remove();
          
          if(topparent.html().trim().length==0){
            
            topparent.html($("#dffilelisting").html());
          }
	});
 
	
});

/*
 * Take phote with Webcamjs
*/
$(document).ready(function(){
    $("#btnopencamera").on('click', function(){
        openWebcam();
        $(this).parent().hide();
    });
});


function openWebcam(webcamId = 'cameralive'){
    let webcamw = 400,
        webcamh = 266;
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
     webcamw = 320;
     webcamh = 240;
    }
    Webcam.set({
		width: webcamw,
		height: webcamh,
		dest_width: 1280,
        dest_height: 720,
		image_format: 'jpeg',
		jpeg_quality: 90
	});
	Webcam.attach( '#'+webcamId );
	$("#takestopbtncontrol").show();
}
function takeSnapshot(){
    var baseUrl = "https://www.soundjay.com/mechanical/";
    new Audio(baseUrl + 'sounds/camera-shutter-click-03.mp3').play();
    Webcam.snap( function(data_uri) {
        var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
        document.getElementById('webcam').value = raw_image_data;
    } );
    Webcam.freeze();
    $("#resumebtncontrol").show();
    $("#takestopbtncontrol").hide();
}
function resumeSnapshot(){
    //  var baseUrl = "https://www.soundjay.com/button/";
    // new Audio(baseUrl + 'sounds/button-20.mp3').play();
   Webcam.unfreeze();
   document.getElementById('webcam').value = '';
    $("#resumebtncontrol").hide();
    $("#takestopbtncontrol").show(); 
}
function closeSnapshot(){
    Webcam.reset();
    document.getElementById('cameralive').style = '';
    document.getElementById('webcam').value = '';
    $("#webcam-open-box").show();
    $("#takestopbtncontrol").hide();
    $("#resumebtncontrol").hide();
}









