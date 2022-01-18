@php
@endphp
@extends('backend.layout')
@section('header_import')
<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datetimepicker.min.css') }}" />
<style>
    #net-worth {
        background: #2b7dbc;
        color: #fff;
        border-radius: 4px;
        padding: 8px;
    }
    #new-worth-num{
      margin-top: 0px;
      border-bottom: 1px solid #fff;
      padding-bottom: 15px;
      font-size: 17px;
      margin-bottom: 65px;
    }
    .summary-item{display:flex;line-height: 2;}
    .summary-item-text{flex: 0 0 45%;}
    #cash-flow, #sales-echart, .dashboard-echart{width: auto;height: 250px;}
    #expense-category-echart{width:auto;height:250px;}
    #new-worth-text{margin-bottom: 55px;text-transform: capitalize;}
    .summary-item-num{text-transform: capitalize;}
    @media only screen and (max-width: 736px){
        #cash-flow {
            margin-top: 30px;
        }
    }
  </style>
@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
	<script src="{{asset('resources/views/backend/lib/js/localStorage.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/moment.min.js')}}"></script>
  	<script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datepicker.min.js')}}"></script>
  	<script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datetimepicker.min.js')}}"></script>


<script>

	$("#btnreset").click(function() {
		location.href="{{url()->current()}}";
	});
	$( document ).ready(function() {
		
			/* export */
			$(".btnb2excel").on('click', function(e){
				e.preventDefault();
				let exportType = $(this).data('export-type');
				if(exportType == 'pdf'){
					//airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'reportdatetime', ajaxact:'generatepdf', ajaxnext : 'ajaxreturn', 'imgBase64': downloadImgSrc},'','Test',false);
					//return;
				}
				//set text
				//$("#b2excel-text").text($(this).text());
				var form = document.createElement("form");
				var elToken = document.createElement("input");
				var elExportType = document.createElement("input"); 
				var elLocation = document.createElement("input"); 
				var elDevice = document.createElement("input"); 
				var elFromdate = document.createElement("input");
				var elTodate = document.createElement("input");
				var	elAirtype = document.createElement("input");
				var	elDatatype = document.createElement("input");
				var elPage = document.createElement("input");

				form.method = "POST";
				form.action = "{{ url_builder($obj_info['routing'],[$obj_info['name'],'ptoexcel']) }}";   

				elToken.value='{{ csrf_token() }}';
				elToken.name="_token";
				elToken.type="hidden";
				form.appendChild(elToken); 

				elExportType.type="hidden";
				elExportType.value=exportType;
				elExportType.name="exportType";
				form.appendChild(elExportType);

				//Location
				elLocation.type="hidden";
				elLocation.value=getUrlParameter('location');
				elLocation.name="location";
				form.appendChild(elLocation);  

				
				// Device
				elDevice.type="hidden";
				elDevice.value=getUrlParameter('device');
				elDevice.name="device";
				form.appendChild(elDevice);

				//FromDate
				elFromdate.type="hidden";
				elFromdate.value=getUrlParameter('fromdate');
				elFromdate.name="fromdate";
				form.appendChild(elFromdate);

				//Todate
				elTodate.type="hidden";
				elTodate.value=getUrlParameter('todate');
				elTodate.name="todate";
				form.appendChild(elTodate);

				//Air Type
				elAirtype.type="hidden";
				elAirtype.value=getUrlParameter('airtype');
				elAirtype.name="airtype";
				form.appendChild(elAirtype);

				//DataType
				elDatatype.type="hidden";
				elDatatype.value=getUrlParameter('datatype');
				elDatatype.name="datatype";
				form.appendChild(elDatatype);

				//Page
				elPage.type="hidden";
				elPage.value=getUrlParameter('page');
				elPage.name="page";
				form.appendChild(elPage);

				document.body.appendChild(form);

				form.submit();
			});

	});
</script>
@stop	


@section('app')
	<div class="page-header" data-spy="affix" data-offset-top="60">
		<div class="row">
			<div class="col-sm-6">
			<h1>
				{!! $obj_info['icon'] !!}
				<a href="{{url_builder($obj_info['routing'],[$obj_info['name'],'index'])}}">
					{!!$obj_info['title']!!}
				</a>
				<small>
					<i class="ace-icon fa fa-angle-double-right"></i>
					{{$caption}}
				</small>
			</h1>
			</div>
			<div class="col-sm-6 text-right">
				
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->

	<!--DRAW Content -->

		<div class="row">
			
			<div class="col-xs-12">
								


								
			</div>			
		</div>

	<!--/. draw content -->

@stop


								

