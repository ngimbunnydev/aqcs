@if (session('input'))
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
			<button type="button" class="btn btn-white btn-default btn-bold btn-sm btnact_w" onclick="airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'{{$obj_info['name']}}', ajaxact:'change'},'frmedit-{{$obj_info['name']}}','Test',true)">
			<i class="ace-icon fa fa-floppy-o bigger-120 blue"></i><br>
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
<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} or '' }}">				
								
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
		<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span><br><br>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<!--start-->
						<label class="frm-label bold">Menu Type: <b><span class="red">{{ $linktype or '' }}</span></b></label>
				<!--stop-->
			</div>

			
		</div><!-- /.row1 -->
		
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<!--start-->
						<label class="frm-label">Title<span class="red">*</span></label>
						<div class="input-group" style="width:100%;"> 

	            			<select id="tab_title" class="form-control input-sm" style="width:25%;">
	                        @foreach (config('ccms.multilang') as $lang)
		                        <option value="@lang($lang[0])">@lang($lang[1])</option>
		                    @endforeach
	                        
	                    	</select>

	                    	@php ($active = '') @endphp
	                    	@foreach (config('ccms.multilang') as $lang)
	                    	<input type="text" class="form-control input-sm {{$active}}" style="width:75%;" name="title-{{$lang[0]}}" id="title-{{$lang[0]}}" placeholder="{{$lang[1]}}" value="{{ ${'title-'.$lang[0]} or '' }}">
	                    	@php ($active = 'hide') @endphp
	                    	@endforeach

	        			</div>
				<!--stop-->
			</div>

			
		</div><!-- /.row1 -->

		<div class="row">
			<div class="col-xs-12 col-sm-9 col-lg-9 form-group">
				<!--start-->
					
				<label class="frm-label" for="linkto">ID/URL (custom menu's fomat: page/id/act)</label>
				<input type="text" class="form-control input-sm" name="linkto" id="linkto" value="{{ $linkto or '' }}">
				<!--stop-->
			</div>

			<div class="col-xs-12 col-sm-3 col-lg-3 form-group">
				<!--start-->
					<label class="frm-label" for="linkto">Target</label>
					<select class="form-control input-sm" name="target" id="target">							   
					{!!cmb_listing(config('ccms.linktarget'),[$target ?? ''],'','')!!} 
					</select>
				<!--stop-->
			</div>
		</div><!-- /.row2 -->


		<div class="row">
			<div class="col-xs-12 col-sm-8 col-lg-8 form-group">
				<!--start-->
					<label class="frm-label" for="tags">Tags</label>
					<input type="text" class="form-control input-sm" name="tags" id="tags" value="{{ $tags or '' }}">
				<!--stop-->
			</div>

			<div class="col-xs-12 col-sm-4 col-lg-4 form-group">
				<!--start-->
				<label class="frm-label" for="isindex">SEO indexing</label><br/>
					{!!check_select("isindex",array("No"=>"no","Yes"=>"yes"),$isindex ?? '',"")!!}
				<!--stop-->
			</div>
		</div><!-- /.row3 -->


	</div> <!-- /.colmain -->

</div>

</form>



@stack('plugin')
	
@stop


								

