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

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta charset="utf-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Roboto:wght@100;300;400;500&family=Moul&display=swap" rel="stylesheet">
  <style>

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


<div style="width:100%; margin-top:10px">
	<div style="width:100%; height:130px; position: relative">
		<div style="position:absolute; left:100px">
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
				<br>
				អំពីស្ថានភាពគុណភាពខ្យល់ ថ្ងៃទី ខែ ឆ្នាំ២០២១ 
				<br>
				ដែលមានទីតាំងស្ថិតនៅក្នុង
			</span>
			<span style="font-size: 15px">
				<br>
				កម្រិតភាគល្អិតនិចល PM2.5 នៅក្នុងខ្យល់ជាមធ្យម 2.79 μg/m3 ធៀបទៅនឹងកម្រិតស្តង់ដា PM2.5 គឺ ៥០ μg/m3 
ក្នុងរយៈពេល២៤ម៉ោង	
			</span>
		</div>

	

</div>

<div style="float:left; width:100%; border:1px solid #ddd">
    
    <div id="cash-flow" style="height: 400px !important"></div>
</div>
<div style="float:left;padding:10px; margin-top:10px; border:1px solid #000; font-size:15px;">
	{{$using_airtype['noted']}}
</div>

<div style="float: left; width:100%; text-align: center">
	អគារមរតកតេជោ ដីឡូលេខ៥០៣ ផ្លូវកៅស៊ូអមមាត់ទន្លេបាសាក់ សង្កាត់ទន្លេបាសាក់ ខណ្ឌចំការមន រាជធានីភ្នំពេញ ទូរសព្ទ:០២៣ ២៣៥ ០០៤/ ០២៣ ២៣៥ ០០៦
</div>
@stop

@push('customcss')
@endpush