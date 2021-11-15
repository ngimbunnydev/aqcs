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






@extends('backend.layout')
@section('header_import')
	
	

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>



	
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

	<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} ?? '' }}">	

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-lg-12" style="text-align: right;">
			<span class="frm-label" ><span class="red">*</span> @lang('ccms.isrequire')</span>
		</div>
	</div>



	<div class="row">
		<div class="col-xs-12 col-sm-12 col-lg-12">
			
			
			


				<div class="row">

					<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
						<label class="frm-label" for="title">@lang('label.lb115') <span class="red">*</span></label>
						<input class="form-control input-sm" type="text" name="title" id="title"   value="{{ $title ?? '' }}"/>
																	  
					</div><!--./col-->

					<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
						<label class="frm-label" for="status">@lang('label.status')</label>

						<br />

						{!!check_select("level_status",array("Enable"=>"yes","Disable"=>"no"),$level_status ?? '',"")!!}
						
																	  
					</div><!--./col-->



					
				</div><!--/row-->

				<div class="row">

					<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
						
							<!-- start -->
								<div id="accordion" class="accordion-style1 panel-group">

									@foreach($definelevel as $class)
											@php
												//dd($class);

												$added = [];
											@endphp
											


											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#{{$class['name']}}">
															<i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
															&nbsp;{!!$class['icon']!!}&nbsp;{{$class['title']}}
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse" id="{{$class['name']}}">
													<div class="panel-body">
														@foreach($class['protectme'] as $method)
															@if (!in_array($method[1], $added))
																@php
																	array_push($added,$method[1]);

																	$check = '';
																	$checkval = $class['name'].'-'.$method[1];
																	if(isset($levelsetting) && in_array($checkval,$levelsetting))
																	$check = 'checked';
																@endphp
																<div class="checkbox">
																	<label>
																		<input name="levelsetting[]" value="{{$checkval}}" type="checkbox" class="" {{$check}}>
																		<span class="lbl">&nbsp;{{$method[2]}}</span>
																	</label>
																</div>
															@endif
														@endforeach
													</div>
												</div>
											</div>

											
									@endforeach
											
								</div>
			

							<!---stop-->



																	  
					</div>



					
				</div><!--/row-->



			
			<!--stop-->
		</div>
	</div>
	<!----End top row -->
	




</form>

@stack('plugin')
	
@stop


								

