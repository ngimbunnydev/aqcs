@php
$legend = [];
$x_axis_data = [];

$series = [];
$airtype_data = [];
foreach ($results as $row){
	$x_axis_data[] = date('Y-m-d H:i', strtotime($row->record_datetime));
	$id = explode(',', $row->airtype_id);
	$val = explode(',', $row->qty);
	$data = array_combine($id, $val);
	foreach ($airtype as $item){
		$airtype_data[$item['title']][] = $data[$item['airtype_id']];
	}
	
	
}
$x_axis_data = array_reverse($x_axis_data);
//$airtype_data = array_reverse($airtype_data);
foreach ($airtype_data as $air => $data){
	$legend[] = $air;
	$ele = [
		'name' => $air,
		'type' => 'line',
		'stack' => 'Total',
		'data' => array_reverse($data)
	];
	array_push($series,$ele);
}

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
	title: {
		text: ''
	},
	tooltip: {
		trigger: 'axis'
	},
	legend: {
		data: [{!!"'".implode("','", $legend)."'"!!}]
		//data: ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine']
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
					show: false,
					title: "Save As Image"
					}
		}
	},
	
	xAxis: {
		type: 'category',
		boundaryGap: false,
		//data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
		data: [{!!"'".implode("','", $x_axis_data)."'"!!}],
		axisLabel: {
         interval: 0,
         rotate: 10 //If the label names are too long you can manage this by rotating the label.
      }
	},
	yAxis: {
		type: 'value'
	},
	series: {!!json_encode($series)!!}
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