@php

@endphp

@extends('backend.pdf_layout')
@section('header_import')

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta charset="utf-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  
  <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Roboto:wght@100;300;400;500&family=Moul&display=swap" rel="stylesheet">
  <style>

    .khtitle{
          font-family: 'Moul', cursive;
        }
	table, td, th {
		border: 1px solid black;
	}
	td{
		padding: 5px;
	}
	th{font-weight: bold; font-size: 17px}

	#table1 {
		border-collapse: separate;
	}

	#table2 {
		border-collapse: collapse;
		width: 100%;
	}

  </style>

@stop


@section('footer_import')
<script>

	
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
	
	<div style="width:100%; text-align: center; padding:10px 0px">
			<span class="khtitle" style="color: rgb(57, 104, 175); font-size:16px;">
				សេចក្តីជូនដំណឹងស្តីពី 
				<br>
				លទ្ធផលសន្ទស្សន៍គុណភាពខ្យល់(AQI)នៅក្នុងរាជធានីភ្នំពេញ និងតាមបណ្តាខេត្តត្រឹម
				<br>
				ម៉ោង ៧ព្រឹក ថ្ងៃទី១៣ ខែមករា ឆ្នាំ២០២២
				<br>
				
			</span>
			<span style="color: rgb(57, 104, 175); font-size:18px;">
				Announcement<br>
				The result of Air Quality Index in Phnom Penh and provinces on 13 January 2022 at 7AM
			</span>

		</div>

	

</div>

<div style="float:left; width:100%; padding:5px 0px 20px 0px">
    
	<table id="table2">			
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

@stop

@push('customcss')
@endpush