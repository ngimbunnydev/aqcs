function actSave(frm,savetype){
  
	
  $("#formactionbutton").find('button').each(function(){
   $(this).attr('disabled', true);
  });
  frm=$("#"+frm);
	frm.append('<input type="hidden" name="savetype" value="'+savetype+'" /> ');
	frm.submit();
}

function actCancel(url_back,url_index){
	$(location).attr('href',url_back);
}

function actNew(url_new){
	$(location).attr('href',url_new);
}


function testAjax(token){
	$.ajax({
               
               url:'/ccms/admin/ajax',
               type:'POST',
               secureuri:false,
			   dataType: 'html',
			   data:{_token:token,obj:'howareuvvv',act:''},
               success:function(data){
                  alert(data);
               }
            });
}

function actSaveWithObject(frm,otherobject,savetype){
  
	
  $("#formactionbutton").find('button').each(function(){
   $(this).attr('disabled', true);
  });
  frm=$("#"+frm);
  frm.attr('action', otherobject);
	frm.append('<input type="hidden" name="savetype" value="'+savetype+'" /> ');
	frm.submit();
}

function actUpdateWithObject(frm,otherobject,savetype){
  
  $("#formactionbutton").find('button').each(function(){
   $(this).attr('disabled', true);
  });
  frm=$("#"+frm);
  frm.attr('action', otherobject);
	frm.append('<input type="hidden" name="savetype" value="'+savetype+'" /> ');
	frm.submit();
}