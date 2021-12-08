@extends('backend.layout')
@section('header_import')
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.8.0/echarts.min.js"></script>
<script>
	var cashFlowEchart = echarts.init(document.getElementById('cash-flow'));
	var option;

option = {
  title: {
    text: 'Stacked Line'
  },
  tooltip: {
    trigger: 'axis'
  },
  legend: {
    data: ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine']
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true
  },
  toolbox: {
    feature: {
		saveAsImage: {
                  title: "Save As Image"
                }
    }
  },
  
  xAxis: {
    type: 'category',
    boundaryGap: false,
    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  },
  yAxis: {
    type: 'value'
  },
  series: [
    {
      name: 'Email',
      type: 'line',
      stack: 'Total',
      data: [120, 132, 101, 134, 90, 230, 210]
    },
    {
      name: 'Union Ads',
      type: 'line',
      stack: 'Total',
      data: [220, 182, 191, 234, 290, 330, 310]
    },
    {
      name: 'Video Ads',
      type: 'line',
      stack: 'Total',
      data: [150, 232, 201, 154, 190, 330, 410]
    },
    {
      name: 'Direct',
      type: 'line',
      stack: 'Total',
      data: [320, 332, 301, 334, 390, 330, 320]
    },
    {
      name: 'Search Engine',
      type: 'line',
      stack: 'Total',
      data: [820, 932, 901, 934, 1290, 1330, 1320]
    }
  ]
};

$(document).ready(function() {
	cashFlowEchart.setOption(option);
});
	

	window.onresize = function() {
      cashFlowEchart.resize();
    };
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
			<div class="col-sm-6">
				
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
						    <div class="form-group col-md-10">

								<label class="frm-label" for="title">@lang('label.lb16')</label>
								<select class="form-control" name="device" id="device">
										  
										  {!!cmb_listing($device,[request()->get('device') ?? ''],'','')!!} 					       
									  </select>

						    </div>

						   

						    <div class="form-group col-md-1">
						    	<label>&nbsp;</label>
						    	<button class="form-control btn btn-default" type="submit" value="filter">
                                    <i class="fa fa-search"></i>
                                </button>
						    </div>

						    <div class="form-group col-md-1">
						      <label>&nbsp;</label>

                               <button class="form-control btn btn-default" type="button" onclick="location.href='{{url()->current()}}'">
                                    @lang('label.reset')
                               </button>
						    </div>
						 </div>
				    <!--/--></div>

				</form>
			</div>
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
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
														<div id="cash-flow"></div>
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
																	{!!
																		orderMenu(
																		[	'caption'=>__('label.title'),
																			'sort'=>'title', 
																			'current_sort'=>$sort, 
																			'mdefault'=>'asc', 
																			'method'=>$order, 
																			'act'=>$act
																		],
																		$querystr,
																		$perpage_query, 
																		$obj_info)
																	!!}
																</th>
		
																
																<th class="hidden-480">@lang('label.quality')</th>
																
																
		
																
															</tr>
														</thead>
		
														<tbody>
															@foreach ($results as $row)
															@php
																$hili='';
																
																if((int)session('id')==(int)$row->id) $hili = "style='background-color: #ffffdd'";
															@endphp
															<tr {!!$hili!!}>
																
		
																<td>
																	<a href="{{url_builder($obj_info['routing'],
												[$obj_info['name'],'edit',$row->id],
												[]
											)}}">{{ $row->title }}</a>
																</td>
		
		
																<td class="hidden-480">
																	{{$row->air_qty}}
																</td>
		
		
																
														
		
																
															</tr>
															 @endforeach
													</tbody>
												</table>
														
						
												</div>
												<!-- ./end Tabcontent-->
                        
                          
                        
										</div><!--/. Tab -->	

										
									</div>
								</div>
							</div>
		</div>

	<!--/. draw content -->

@stop


								

