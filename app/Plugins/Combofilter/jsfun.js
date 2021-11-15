function changeSelectItem(){
		$.ajax({
	  		url:env.ajax_path,
			type: 'POST',
			secureuri:false,
			dataType: 'html',
			data:{_token:env.token,
				ajaxobj:'Combofilter',
				ajaxact:'index',
				permission:'no'
			},
			success: function (response, status)
			{	
                alert(response);
			},
			error: function (response, status, e)
			{
				alert(status);
			}
            
            
	});	//end $.ajax
    //clearconsole();
}//end
