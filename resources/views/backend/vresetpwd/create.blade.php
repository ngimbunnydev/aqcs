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
	

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>

	<script>
		$(document).on("change", "#tab_title", function (ev) {
		    ///
		    	var $value=$(this).val();
	  			enableDisableByLang($(this),{!!json_encode($langcode,true)!!},'title-',$value)
		    ///
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

		});
      
   function afterreset(jsondata){
        
        closeairwindow();
  }
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
		<div class="wizard-actions">
			<button type="button" class="btn btn-white btn-default btn-bold btn-sm btnact_w" onclick="airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'{{$obj_info['name']}}', ajaxact:'update'},'frmedit-{{$obj_info['name']}}','Test',false); $(this).attr('disabled', true)">
			<i class="ace-icon fa fa-save bigger-120 blue"></i><br>
			@lang('ccms.save')
		</button>
      
		</div>						
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->


<form name="frmedit-{{$obj_info['name']}}" id="frmedit-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],'change']) }}" enctype="multipart/form-data">
{{ csrf_field() }}	
<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} ?? '' }}">				
								
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
		<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span><br><br>
		
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<!--start-->
						<label class="frm-label" for="password">Current Password<span class="red">*</span></label>
						<input type="Password" class="form-control input-sm" name="password" id="password" value="">
				<!--stop-->
			</div>

			
		</div><!-- /.row1 -->

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<!--start-->
						<label class="frm-label" for="newpassword">New Password<span class="red">*</span></label>
						<input type="Password" class="form-control input-sm" name="newpassword" id="newpassword" value="">
				<!--stop-->
			</div>



			
		</div><!-- /.row2 -->

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<!--start-->
						<label class="frm-label" for="cnewpassword">Confirm Password<span class="red">*</span></label>
						<input type="Password" class="form-control input-sm" name="cnewpassword" id="cnewpassword" value="">
				<!--stop-->
			</div>



			
		</div><!-- /.row3 -->





	</div> <!-- /.colmain -->

</div>

</form>



@stack('plugin')
	
@stop


								

