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
			$("#btnExportToExcel").on('click', function(e){
				e.preventDefault();
		
				var form = document.createElement("form");
				var elToken = document.createElement("input");
				form.method = "POST";
				form.action = "{{ url_builder($obj_info['routing'],[$obj_info['name'],'ptoexcel']) }}";  
				form.target = "_blank";

				elToken.value='{{ csrf_token() }}';
				elToken.name="_token";
				elToken.type="hidden";
				form.appendChild(elToken);

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
				<button class="btn btn-white btn-success btn-bold btn-sm btnact_w" id="btnExportToExcel">
					<i class="ace-icon fa fa-file-export bigger-120 green"></i><br>
					@lang('label.export')
				</button>	
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->

	<!--DRAW Content -->

		<div class="row">
			
			<div class="col-xs-12">
				<div style="padding:10px 0px; text-align: center; font-size: 16px">
					សេចក្តីជូនដំណឹង
 ស្តីពី លទ្ធផលសន្ទស្សន៍គុណភាពខ្យល់(AQI)នៅក្នុងរាជធានីភ្នំពេញ និងតាមបណ្តាខេត្តត្រឹម {{date('d F Y H:i:s')}}
 <br>
Announcement
The result of Air Quality Index in Phnom Penh and provinces on {{date('d F Y H:i:s')}}
					
				</div>
				<table id="dynamic-table" class="table table-striped table-bordered table-hover">			
					<thead>
						<th>ទីតាំង</th>
						<th>សន្ទស្សន៍គុណភាពខ្យល់ (AQI)</th>
						<th>ការវាយតម្លៃ</th>
						<th>ការពិពណ៌នា</th>
					</thead>
					<tbody>
						@foreach ($results as $row)
						
						<tr>
							<td>
								{{ $location[$row['location_id']]??""}}
							</td>

							<td>
								{{$row['result']['qty']}}
							</td>
								
							<td style="background-color:{{$row['result']['color']??'#fff'}}; text-align:center">
								<span style="color: #fff">{{$row['result']['evaluate']}}</span>
							</td>

							<td>
								{!!$row['result']['text']!!}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>			
			</div>			
		</div>

	<!--/. draw content -->

@stop


								

