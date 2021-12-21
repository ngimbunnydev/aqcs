@php
$using_airtype = $airtype[$airtype_id];
$airtype_combo = [];
foreach ($airtype as $item){

	$airtype_combo[$item['airtype_id']]= $item['title'];
}

$data = [];
$count_branch = count($branch);
if(empty($results)){
	
	for($i=0; $i<$count_branch; $i++){
		$data[$i] = '';
	}
}
else{
	foreach ($branch as $key => $item){
		//dd($results[$key]['qty']);
		if(isset($results[$key])){
			$data[] = $results[$key]['qty'];
		}
		else {
			$data[] = '';
		}
		
	}
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
		title:{
			text:"កំហាប់ភាគល្អិតនិចល {{$using_airtype['title']}} ក្នុងបរិយាកាសនៃប្រទេសកម្ពុជា",
			textStyle: {
				color: 'orange'
			},
			x: 'center',
			position: 'bottom'
		},
  xAxis: {
    type: 'category',
    data: [{!!"'".implode("','", array_values($branch))."'"!!}]
  },
  yAxis: {
    type: 'value',
	max:"{{(int)$using_airtype['standard_qty']+10}}"
  },
  series: [
    {
	label: {
        show: true,
        position: 'top'
      },
	  itemStyle: {
        borderColor: '#ddd',
        color: 'orange'
      },
      data: [{!!"'".implode("','", $data)."'"!!}],
      type: 'bar',
      showBackground: true,
      backgroundStyle: {
        color: 'rgba(180, 180, 180, 0.2)'
      },
	  barWidth: '20%',
      markLine: {
                  symbol:"none",
                 data: [
					 {
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
					 }
					 ],
                 lineStyle: {
                    color: 'red',
                    type: 'solid',
                    width: 3
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