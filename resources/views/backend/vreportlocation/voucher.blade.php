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

@extends('backend.pdf_layout')
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
    tooltip: {
		trigger: 'axis'
	},
	
	legend: {
		data: ["{{$using_airtype['title']}}"]
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

	cashFlowEchart.setOption(option);
	
	window.onresize = function() {
		cashFlowEchart.resize();
	};
	
</script>
@stop


@section('app')

<div class="item-heading-box">
    <h3 class="item-heading-title" style="margin-bottom: 30px;">Charts</h3>
</div>

<div style="float:left; width:100%;">
    
    <div id="cash-flow" style="height: 450px !important"></div>
</div>
<div class="item-body-box">
</div>
@stop

@push('customcss')
@endpush