@php 
	$header_contact_info = json_decode(html_entity_decode($pageinfo['contact']),true); 
@endphp

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			@page {
				header: page-header;
				footer: page-footer;
			}
			body,table {
				font-family: arial;
                font-size: 10pt;
            }
			/* header */
			#header-wrapper{
				float:left; 
				width:100%;​ 
				text-align: center; 
				border:0px solid green;
				padding-bottom: 8px;

			}
			#header-wrapper-img{
				float:left; 
				width:100%; 
				border:0px solid red;
        text-align: left;
			}
			#header-wrapper-info{
				float:right; 
				width:100%; 
				text-align: left; 
				border:0px solid blue;
			}
			#header-title{
				padding-bottom: 8px;
				font-size: 13pt;
			}
			/* footer */
			#footer-wrapper{
				border-top: 2px solid #c21301; 
				font-size: 9pt; 
				text-align: center; 
				padding-top: 3mm; 
			}
			/* more */
			.mg-0{
				margin: 0px;
			}
			.pd-0{
				padding: 0px;
			}

			.fsize-pt-8{
				font-size: 8pt;
			}
			.fsize-pt-9{
				font-size: 9pt;
			}
			.fsize-pt-10{
				font-size: 10pt;
			}
			.fsize-pt-11{
				font-size: 11pt;
			}
			.fsize-pt-12{
				font-size: 12pt;
			}
			.separator{
				float: left;
				width: 100%;
				border-top: 2px solid #c21301; 
				padding-top: 3mm; 
			}
			.collapsed{
				border: 1px solid #ddd;
                border-collapse: collapse;
			}
			.text-center{
				text-align: center;
			}
			.text-left{
				text-align: left;
			}
			.text-right{
				text-align: right;
			}
			.middle{
				vertical-align: middle;
			}
			.item-heading-title{
				font-size: 12pt;
			}


			.rowtitle{
				float: left; width: 39%; text-align: left; border: 0px solid red;
			}

			.rowvalue{
				float: left; width: 59%; border: 0px solid blue; text-align: left;
			}

			.insidetitle{
				float: left; width: 40%; text-align: left; border: 1px solid red;
			}

			.indsidevalue{
				float: left; width: 60%; border: 1px solid blue; text-align: right;
			}



		</style>
		<!-- BEGIN PAGE LEVEL PLUGINS -->
		    @stack('customcss')
		<!-- END PAGE LEVEL PLUGINS -->
	</head>

	<body class="no-skin">
		
		<htmlpageheader name="page-header">
			<div id="header-wrapper">                    
	            <div id="header-wrapper-img">
                @if($preview=='pdf')
                <img src="{{base_path('resources/filelibrary/'.$pageinfo['logo'])}}" width="180"/>
                @else
                <img src="{{ URL::asset('/resources/filelibrary/'.$pageinfo['logo']) }}" width="180"/>
                @endif
	            </div>
	            
	    </div>
      
      <div style="float:left; width:100%">
  <div style="float:left; width:100%;">
                    	

                    	<div class="rowvalue">
                    		<span>លេខអត្តសញ្ញាណកម្ម អតប: K004-901700382</span>
                    		
                    	</div>
                    	
     </div><!--a row-->
  
  <div style="float:left; width:100%;">
                    	
                    	<div class="rowvalue">
                    		
                    			{{$header_contact_info['address']}}
                    		  <span>Phone: {{$header_contact_info['phone']}}</span>
                    		
                    	</div>
                    	
   </div><!--a row-->
  
     <div style="float:left; width:100%;">
                    	
                    	<div class="rowvalue">
                    		<span>Email: {{$header_contact_info['email']}}</span>
                    		
                    	</div>
                    	
     </div><!--a row-->
  
</div>
			
		</htmlpageheader>
		<!-- PAGE CONTENT BEGINS -->
		@yield('app')
		<!-- PAGE CONTENT ENDS -->
		@if($preview=='pdf')
		<htmlpagefooter name="page-footer">
			<div id="footer-wrapper">
                Page {PAGENO} of {nb}
            </div>
		</htmlpagefooter>
		@endif
	</body>
</html>
