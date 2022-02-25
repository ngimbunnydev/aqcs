@php
$using_airtype = $airtype[$airtype_id];
$airtype_combo = [];
foreach ($airtype as $item){

	$airtype_combo[$item['airtype_id']]= $item['title'];
}
if((null!==request()->get('location') && !empty(request()->get('location')))){
	$location_id  = request()->get('location');
	$filtered2 = array_filter( $device, function( $v ) use($location_id){ 
		return $v['location_id'] == $location_id; 
	} );
	$device_combo = [];
	foreach ($filtered2 as $item) {
		$device_combo[$item['device_id']] = $item['title'];
	}

}

$legend = [];
$x_axis_data = [];
$data = [];
$dateformat = 'Y-m-d H:i';
if(request()->get('datatype')=='hour'){
	$dateformat = 'Y-m-d H';
}
elseif(request()->get('datatype')=='day') {
	$dateformat = 'Y-m-d';
}
foreach ($results as $row){
	$x_axis_data[] = date($dateformat, strtotime($row->record_datetime));
	$data[]= $row->qty;

}
$x_axis_data = array_reverse($x_axis_data);
$data = array_reverse($data);

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.8.0/echarts.min.js"></script>
<script>
	var downloadImgSrc;
	@isset($device)
				var devices = {!!json_encode($device)!!};
	@endisset
	var cashFlowEchart = echarts.init(document.getElementById('cash-flow'));
	var option;

	option = {
    tooltip: {
		trigger: 'axis'
	},
	
	legend: {
		data: ["{{$using_airtype['title']}}"]
	},
	toolbox: {
		feature: {
			saveAsImage: {
					show: true,
					title: "Save As Image"
					}
		}
	},
	xAxis: {
		type: 'category',
		data: [{!!"'".implode("','", $x_axis_data)."'"!!}],
		axisLabel: {
			interval: 0,
			rotate: 10 //If the label names are too long you can manage this by rotating the label.
		}
	},
	yAxis: {
		type: 'value',
		max:"{{(int)$using_airtype['standard_qty']+10}}"
	},
	series: [
		{
		name: "{{$using_airtype['title']}}",
		type: 'line',
		stack: 'Total',

		lineStyle: {
						type: 'solid',
						width: 3,
						color: 'blue'
					},
		itemStyle: {
			color: 'blue',
			borderWidth: 3,
			opacity: 1
		},
		data: [{!!"'".implode("','", $data)."'"!!}],
			markLine: {
					symbol:"none",
					data: [{
						name: 'ស្តង់ដា', 
						yAxis: "{{(int)$using_airtype['standard_qty']}}",
						label: {
							formatter: '{b}',
							position: 'insideMiddleTop',
							color: 'red',
							fontStyle: 'italic',
							fontWeight: 'bold',
							fontSize: 14,
							//fontFamily: 'Helvetica',
							
							// lineHeight: 25,
							// width: 200,
							// height: 200,
							// tag: 'asdasdasdasdasd',
							
							
							
						}
					}],
					lineStyle: {
						color: 'red',
						type: 'solid',
						width: 2
					},
				}
		
		}
		
	]
};

	$(document).ready(function() {
		cashFlowEchart.setOption(option);
		setTimeout(function() {
			downloadImgSrc = cashFlowEchart.getDataURL({
			pixelRatio: 2,
			backgroundColor: '#fff'
		});
		var canvas_ctn = document.getElementById('cash-flow');
		//console.log(canvas_ctn.offsetHeight);
		//$('.showimage').html("<img src='"+downloadImgSrc+"' height='"+canvas_ctn.offsetHeight+"' width='"+canvas_ctn.offsetWidth+"'>");
		}, 1500);
		
	});
		

	window.onresize = function() {
		cashFlowEchart.resize();
	};


	// 
	$("select[name='location']").change(function() {
			
			var combo = $("select[name='device']")
			
			combo.find('option:not(:first)').remove();
			var getDevices;
			if(this.value==-1 || this.value==''){
				getDevices = devices;
			}
			else{
				getDevices = devices.filter(item => (item.location_id == this.value));
			}
			

			getDevices.forEach(element => {
				combo.append($("<option></option>")
				.attr("value",element.device_id)
				.text(element.title)); 
			});
			
	});

	$("#btnreset").click(function() {
		location.href="{{url()->current()}}";
	});
	$( document ).ready(function() {

			$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});

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
				@include('backend.widget.btnexportaqcs', [])
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->

	<!--DRAW Content -->
	@php
		$querytitle=url_builder($obj_info['routing'],[$obj_info['name'],'index'],array_merge(['sort=title'], $querystr));
	@endphp
		<div class="row">
			<div class="col-xs-12">
				
				<form action="" method="get" id="filter">

				    <!--/--><div class="row">
				    	<div class="form-row">
							

						    <div class="form-group col-md-2">

								<label class="frm-label" for="title">@lang('label.lb09')</label>
								<select class="form-control" name="location" id="location">
									<option value="">-- {{__('ccms.ps')}} --</option>
									{!!cmb_listing($location,[request()->get('location') ?? ''],'','')!!} 					       
								</select>

						    </div>

						    <div class="form-group col-md-2">

								<label class="frm-label" for="title">@lang('label.lb16')</label>
								<select class="form-control" name="device" id="device">
									<option value="">-- {{__('ccms.ps')}} --</option>
									{!!cmb_listing($device_combo,[request()->get('device') ?? ''],'','')!!} 					       
								</select>

						    </div>

							<div class="form-group col-md-2">
						      	<!-- *** -->
						      	<label class="frm-label" for="fromdate">@lang('label.fdate') (@lang('label.d-m-y'))</label>
				
								<div class="input-group">
									<input class="form-control date-picker" name="fromdate" id="fromdate" type="text" data-date-format="dd-mm-yyyy" value="{{request()->get('fromdate')}}" autocomplete="off">
										<span class="input-group-addon">
											<i class="fa fa-calendar bigger-110"></i>
										</span>
								</div>
						      	<!-- **** -->
						    </div>

						    <div class="form-group col-md-2">
						      	<!-- *** -->
						      	<label class="frm-label" for="todate">@lang('label.tdate') (@lang('label.d-m-y'))</label>
				
								<div class="input-group">
									<input class="form-control date-picker" name="todate" id="todate" type="text" data-date-format="dd-mm-yyyy" value="{{request()->get('todate')}}" autocomplete="off">
										<span class="input-group-addon">
											<i class="fa fa-calendar bigger-110"></i>
										</span>
								</div>
						      	<!-- **** -->
						    </div>

							<div class="form-group col-md-2">

							<label class="frm-label" for="title">@lang('label.lb17')</label>
							<select class="form-control" name="airtype" id="airtype">
								{!!cmb_listing($airtype_combo,[request()->get('airtype') ?? ''],'','')!!} 					       
														
							</select>

							</div>

							<div class="form-group col-md-2">

								<label class="frm-label" for="title">Data Type</label>
								<select class="form-control" name="datatype" id="datatype">
									{!!cmb_listing(config('ccms.datatype'),[request()->get('datatype') ?? ''],'','')!!} 					       
															
								</select>

							</div>

						   

						   

							
							
						 </div>

						 <div class="form-row">
							<div class="form-group col-md-2 col-md-offset-4">
						    	<label>&nbsp;</label>
						    	<button class="form-control btn btn-default" type="submit" value="filter">
                                    <i class="fa fa-search"></i>
                                </button>
						    </div>

						    <div class="form-group col-md-2">
						      <label>&nbsp;</label>

                               <button id="btnreset" class="form-control btn btn-default" type="button" onclick="location.href='{{url()->current()}}'">
                                    @lang('label.reset')
                               </button>
						    </div>
						 </div>

				    <!--/--></div>

				</form>
			</div>
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<h3 class="header smaller lighter purple">
									@if(!empty($device_info))
										{{$device_info['location']}}
										<small>
											<i class="ace-icon fa fa-angle-double-right"></i>
											{{$device_info['device']}}
											({{$device_info['device_index']}})
										</small>
									@endif
										
									
								</h3>

								<div class="row">
									<div class="col-xs-12">

										<div class="tabbable">
											<ul class="nav nav-tabs" id="myTab">
												

												

												<li class="active">
													<a data-toggle="tab" href="#media" aria-expanded="false">
														@lang('label.chart')
													</a>
												</li>

												

												<li class="">
													<a data-toggle="tab" href="#madewith" aria-expanded="false">
														@lang('label.list')
													</a>
												</li>

												
                        
                       
                        
                        
											</ul>

											<div class="tab-content">

												
												<!-- ./ start Tabcontent-->
												<div id="media" class="tab-pane fade active in">
													
												
													<div class="row">
														<div class="col-xs-12">
															<div id="cash-flow" style="height: 450px !important"></div>
														</div>
													</div>
													
												</div>
												<!-- ./end Tabcontent-->

												<!-- ./ start Tabcontent-->
												<div id="madewith" class="tab-pane fade">
													
													<table id="dynamic-table" class="table table-striped table-bordered table-hover">
														<thead>
															<tr>
																<!-- <th class="center" style="width: 35px">
																	<label class="pos-rel">
																		<input type="checkbox" class="ace" />
																		<span class="lbl"></span>
																	</label>
																</th> -->
																
		
																<th>
																	Date-Time
																</th>
																
																<th>
																	Air Type
																</th>

																<th>
																	Standard
																</th>

																<th>
																	Real Data
																</th>
																
																
																
																
		
																
															</tr>
														</thead>
		
														<tbody>
															@foreach ($results as $row)
															
															<tr>
																
																<td>
																	{{date($dateformat, strtotime($row->record_datetime))}}
																</td>
		
																<td>
																	{{ $using_airtype['title'] }}
																</td>
																<td>
																	{{ $using_airtype['standard_qty']}}
																</td>
																<td>
																	{{ $row->qty}}
																</td>
																
															</tr>
															 @endforeach
													</tbody>
												</table>
						
												</div>

											</div>
												<!-- ./end Tabcontent-->
                        
                          
                        
										</div><!--/. Tab -->	
										<hr>
										<!-- Pagination and Record info -->
											@include('backend.vreportdatetime.pagination')

										<!-- /. end -->

										
									</div>
								</div>


								
							</div>

						
							

						
		</div>



	<!--/. draw content -->

@stop


								

