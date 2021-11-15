@if(session('input'))
	@php 
		$inputdata=session('input');
	@endphp

@elseif(! empty($input))
	@php 
		$inputdata=$input;
	@endphp
@endif

@if( ! empty($inputdata))
	@foreach ($inputdata as $key => $val)
	   @php ${$key}=$val; @endphp
	@endforeach
@endif

@foreach (config('ccms.multilang') as $lang)
	@php
		$langcode[]=$lang[0];
	@endphp
@endforeach




@extends('backend.layout')
@section('header_import')
<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datetimepicker.min.css') }}" />


@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>

	<script src="{{asset('/resources/assets/arcetheme/js/moment.min.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datepicker.min.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datetimepicker.min.js')}}"></script>
  
	<script type="text/javascript">
		$( document ).ready(function() {
	
					$('.date-time-picker').datetimepicker({
						format: 'DD-MM-YYYY HH:mm:ss'
					})
					//show datepicker when clicking on the icon
					.next().on(ace.click_event, function(){
						$(this).prev().focus();
					});
	
					///////////
			
	
		
					
		});
	  </script>


	
	<script type="text/javascript">
		$(document).ready(function() {
			@if (session('errors'))
				$.gritter.add({
								title: 'Error:',
								text: '<strong><i class="ace-icon fa fa-exclamation-triangle"></i></strong> {{ session('errors') }}',
								sticky: true,
								class_name: 'gritter-error gritter-center'
							});
			@endif

			@if (session('success'))
				$.gritter.add({
								title: 'Success:',
								text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong> {{ session('success') }}',
								sticky: false,
								class_name: 'gritter-success gritter-center'
							});

			@endif
      
      $('.ordering0').each(function(i, obj) {
				$(this).ace_spinner({value:0,min:0,step:1, btn_up_class:'btn-info' , btn_down_class:'btn-info'});

			}); 
		});
	</script>
	
	
@stop	


@section('app')

<!-- /...........................................................page-header -->	

<div class="page-header" data-spy="affix" data-offset-top="60">
<div class="row">
	<div class="col-xs-12 col-sm-6 col-lg-6">
	<h1>
				{!! $obj_info['icon'] !!}
				<a href="{{url_builder($obj_info['routing'],[$obj_info['name'],'index'])}}">
					{!! $obj_info['title'] !!}
				</a>
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$caption}}
		</small>
	</h1>
	</div>
	<div class="col-xs-12 col-sm-6 col-lg-6">
		@include('backend.widget.btnaa',['btnsave' => 'yes', 'btnnew' => 'yes', 'btnapply' => 'yes', 'btncancel' => 'yes'])							
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->

<form name="frmadd-{{$obj_info['name']}}" id="frmadd-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" enctype="multipart/form-data">
{{ csrf_field() }}	

	

	@if (session('ajax_access'))
		@if(!empty(\Request::get('ajaxnext')))
		@php
			$ajaxnext_val=\Request::get('ajaxnext');
		@endphp	
		@elseif(!empty($ajaxnext))
			@php
				$ajaxnext_val=$ajaxnext;
			@endphp	
					
		@endif
		<input type="hidden" name="ajaxnext" value="{{$ajaxnext_val}}">	
	@endif			

<div class="row">
		<div class="col-xs-12 col-sm-12 col-lg-12">
			<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span>
		</div>
</div>
	
<div class="row">
		<div class="col-xs-12 col-sm-4 col-lg-4">
			<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{${$fprimarykey} ?? ''}}">	
			<br/>
			<label class="frm-label" for="location_id">@lang('label.lb09')</label>
			<br/>														
			<select class="form-control input-sm" name="location_id" data-placeholder="@lang('label.lb09')...">
				{!!cmb_listing($location,[$location_id ?? 0],
																									  '','')
																									  !!} 
			</select>
								
		</div>

		<div class="col-xs-12 col-sm-4 col-lg-4">
			<br/>
			<label class="frm-label" for="device_id">@lang('label.lb16') <span class="red">*</span></label>
			<br/>														
			<select class="form-control input-sm" name="device_id" data-placeholder="@lang('label.lb16')...">
				{!!cmb_listing($device,[$device_id ?? 0],
																									  '','')
																									  !!} 
			</select>
								
		</div>

		<div class="col-xs-12 col-sm-4 col-lg-4 from-group">
			<br/>
			<label class="frm-label" for="record_datetime">@lang('label.datetime')<span class="red">*</span></label>
			
			<div class="input-group">
				<input class="form-control input-sm date-time-picker" name="record_datetime" id="record_datetime" type="text" value="{{$record_datetime ?? date('d-m-Y H:i:s')}}">
																<span class="input-group-addon">
																	<i class="fa fa-calendar bigger-110"></i>
																</span>
			</div>
														  
		</div><!--./end col-->

</div>

<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
		
		<div class="tablediv" id="results">
            <div class='theader'>
			  
			  <div class='table_header'>@lang('label.lb17') <span class="red">*</span></div>
			  <div class='table_header'>@lang('label.quality')</div>
			  
			</div> 

			@foreach ($airtype as $item)
				
            <div class='table_row'>
      
              

               <div class='table_small'>
                              
                   <div class='table_cell'>@lang('label.lb17')</div>
                  <div class='table_cell'>
                    
					<input type="hidden" name="aqmd_id_{{$item['airtype_id']}}" id="aqmd_id_{{$item['airtype_id']}}" value="{{${'aqmd_id_'.$item['airtype_id']}??''}}">	
					<input type="text" class="form-control input-sm" name="subairqty[]" value="{{$item['title']??''}}" readonly>
                  </div>          
                                   
               </div><!-- /.cell -->

			   <div class='table_small'>
                              
					<div class='table_cell'>@lang('label.quality')</div>
				<div class='table_cell'>
					<input type="text" class="form-control input-sm" name="airtype_{{$item['airtype_id']}}" id="model" placeholder="" value="{{${'airtype_'.$item['airtype_id']}??''}}">
				</div>          
									
				</div><!-- /.cell -->

			</div><!-- /.row --> 

            @endforeach
        </div><!-- /.table -->
	</div> <!-- /.col1 -->



</div>
</form>


@stack('plugin')
@stop


								

