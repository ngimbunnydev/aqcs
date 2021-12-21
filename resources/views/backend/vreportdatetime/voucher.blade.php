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
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Roboto:wght@100;300;400;500&family=Moul&display=swap" rel="stylesheet">
    #cash-flow, #sales-echart, .dashboard-echart{width: auto;}
    
    @media only screen and (max-width: 736px){
        #cash-flow {
            margin-top: 30px;
        }
    }

	.khtitle{
        font-family: 'Moul', cursive;
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

<div style="width:100%; margin-top:10px">
	<div style="width:100%; height:130px; position: relative">
		<div style="position:absolute; left:50px">
			<img src="{{ URL::asset('/resources/filelibrary/logo-ministry-of-environment.jpeg') }}" width="100px"/>
			<br>
			<span class="khtitle" style="font-size: 18px; color: green">ក្រសួងបរិស្ថាន</span>
		</div>

		<div class="khtitle" style="text-align: center; font-size:18px; color: green">
			ព្រះរាជាណាចក្រកម្ពុជា
			<br>
			ជាតិ សាសនា ព្រះមហាក្សត្រ
			
		</div>
	</div>
	
	<div style="width:100%; text-align: center;">
			<span class="khtitle" style="color: rgb(57, 104, 175); font-size:16px;">
				សេចក្តីជូនដំណឹង
			</span>

		</div>

	

</div>

<div style="float:left; width:100%; border:1px solid #ddd">
    
    <div id="cash-flow" style="height: 400px !important"></div>
</div>


<div style="float: left; width:100%; margin-top:10px; text-align: center">
	អគារមរតកតេជោ ដីឡូលេខ៥០៣ ផ្លូវកៅស៊ូអមមាត់ទន្លេបាសាក់ សង្កាត់ទន្លេបាសាក់ ខណ្ឌចំការមន រាជធានីភ្នំពេញ ទូរសព្ទ:០២៣ ២៣៥ ០០៤/ ០២៣ ២៣៥ ០០៦
</div>

@stop

@push('customcss')
@endpush