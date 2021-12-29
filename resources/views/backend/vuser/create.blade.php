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
	{{--need to send datata to attribute--}}
	@php
		session(['att_data'=>$inputdata]);
	@endphp

	@foreach ($inputdata as $key => $val)
	   @php ${$key}=$val; @endphp
	@endforeach
@endif




{{--check object id for getting the document/medai file--}}
@if( !empty(${$fprimarykey}))
    @php $media='0,'.${$fprimarykey}; @endphp
@else
	@php $media=0; @endphp
@endif


@extends('backend.layout')
@section('header_import')
	
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datetimepicker.min.css') }}" />

	<!--/******************
	*	Add file manager plugin *
	*******************/-->
	
	<link rel="stylesheet" href="{{asset('app/Plugins/Splitjs/splitjs.css')}}" />
	<!--JS Tree-->
	<link rel="stylesheet" href="{{asset('app/Plugins/Jstree/dist/themes/default/style.min.css')}}" />

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>

  <script src="{{asset('/resources/assets/arcetheme/js/moment.min.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datetimepicker.min.js')}}"></script>


<!--/******************
	*	Add file manager plugin *
	*******************/-->
	<script src="{{asset('app/Plugins/Filemanager/jsfun.js')}}"></script>
	<script src="{{asset('app/Plugins/Jstree/dist/jstree.min.js')}}"></script>
	<script src="{{asset('app/Plugins/Splitjs/split.min.js')}}"></script>
	<!-- Alert Panel (looks like js confrm dialog)-->
  	<script src="{{asset('/resources/assets/arcetheme/js/bootbox.js')}}"></script>
	<script src="{{asset('app/Plugins/twbs-pagination/jquery.twbsPagination.js')}}"></script>
	<script>
		Split(['#file_category', '#file_listing'], {
			sizes: [24, 76],
			minSize: 200
		});
	</script>
	<script>
		var filemanagerSetting={};
		var givent_txtbox;
		var editorContext; /*global variable for store Summernote object when v try to insert image*/

		
		/**-Browse File button @ each object-**/


		$(document).on("click", "#btn-browe-pic", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='image';
				filemanagerSetting.givent_txtbox='object';
				givent_txtbox=$('#pic');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
		});


	</script>
	<!--/*end file manager*/-->


	
  <script>
  	$(document).ready(function() {
  		

  		$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});


  	});

	$( "#newctype" ).click(function() {
		airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'userlevel', ajaxact:'create', ajaxnext : 'ajaxreturn'},'','Test',true);
	});

	$( "#newbranch" ).click(function() {
		airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'branch', ajaxact:'create', ajaxnext : 'ajaxreturn'},'','Test',true);
	});

	$( "#newwarehouse" ).click(function() {
		airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'warehouse', ajaxact:'create', ajaxnext : 'ajaxreturn'},'','Test',true);
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
	<div class="col-xs-12 col-sm-12 col-lg-12">

		<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span>
	</div>
</div>


<div class="row">
	<div class="col-xs-12 col-sm-6 col-lg-6">

		<!--Panel -->	
		<div class="widget-box">						
										
				<div class="widget-header">
				<h4 class="smaller">
					@lang('label.lb39')
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">
												
					<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<label class="frm-label" for="name">@lang('label.lb109') <span class="red">*</span></label>
				<input class="form-control input-sm" type="text" name="name" id="name"   value="{{ $name ?? '' }}"/>
															  
			</div>
		</div>


		<div class="row">

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="nativename">@lang('label.lb112')<span class="red">*</span></label>
				<input class="form-control input-sm" type="text" name="nativename" id="nativename"   value="{{ $nativename ?? '' }}"/>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="latinname">@lang('label.lb111') <span class="red">*</span></label>
				<input class="form-control input-sm" type="text" name="latinname" id="latinname"   value="{{ $latinname ?? '' }}"/>
															  
			</div>

			
		</div>

		

		<div class="row">
			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="level_id">@lang('label.lb110')<span class="red">*</span> <span class="badge badge-info new">
									<a href="#" id="newctype">New</a>
								</span></label>
				<select class="form-control input-sm" name="level_id" id="level_id">
																	   
								{!!cmb_listing($ctype,[$level_id ?? ''],'','')!!} 
							</select>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="branch_id">@lang('label.lb31')<span class="red">*</span> <span class="badge badge-info new">
									<a href="#" id="newbranch">New</a>
								</span></label>
				<select class="form-control input-sm" name="branch_id" id="branch_id">
																	   
								{!!cmb_listing($branch,[$branch_id ?? ''],'','')!!} 
							</select>
															  
			</div>

			
		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="password">
					
					@lang('label.lb104')
					@if($submitto!='update')
					<span class="red">*</span>
					@endif
				</label>
				<input class="form-control input-sm" type="password" name="password" id="password"   value="" autocomplete="new-password"/>
															  
			</div>


			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="password_confirmation">@lang('label.lb113')
					@if($submitto!='update')
					<span class="red">*</span>
					@endif
				</label>
				<input class="form-control input-sm" type="password" name="password_confirmation" id="password_confirmation"   value="" autocomplete="new-password"/>
															  
			</div>

			


		</div>	


		<div class="row">

			<!-- <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="status">General Customer</label>

				<br />

				{!!check_select("generalcustomer",array("No"=>"no","Yes"=>"yes"),$generalcustomer ?? '',"")!!}
				
															  
			</div> -->
			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="status">@lang('label.status')</label>

				<br />

				{!!check_select("userstatus",array("Enable"=>"yes","Disable"=>"no"),$userstatus ?? '',"")!!}
				
															  
			</div><!--./col-->


			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
															<label class="frm-label" for="created_at">@lang('label.lb105')<span class="red">*</span></label>
															<br />
															<div class="input-group">
																	@php
																		$created_at = !empty($created_at)?date("d-m-Y", strtotime($created_at)):date("Y-m-d");
																	@endphp
																	<input class="form-control input-sm date-picker" name="created_at" id="created_at" type="text" data-date-format="dd-mm-yyyy" value="{{$created_at ?? date('d-m-Y')}}">
																	<span class="input-group-addon">
																		<i class="fa fa-calendar bigger-110"></i>
																	</span>
																</div>
																

															</div>


			
		</div>	


		<!--end-->								
																				
					
				</div>
			</div>
		</div> <!-- /.Panel -->	




	</div> <!-- /.col -->

	<!-- Right Blog ------------------------------------------------------------------------------------- -->
	<div class="col-xs-12 col-sm-6 col-lg-6">

		<!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					@lang('label.lb103')
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					<div class="row">
			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="personincharge">@lang('label.photo') <i>(@lang('label.dblremove'))</i></label>
				
				<div class="input-group">
                                <input class="form-control input-sm" type="text" name="pic" id="pic" value="{{ $pic ?? '' }}" readonly="readonly" ondblclick="this.value=''"/>
                                <span class="input-group-btn">
        
                                    <button id="btn-browe-pic" class="btn btn-default btn-sm" type="button" style="height: 30px">
                                                Browse...
                                    </button>
                                </span>
                            </div>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="cposition">@lang('label.lb107')</label>
				<input class="form-control input-sm" type="text" name="cposition" id="cposition"   value="{{ $cposition ?? '' }}"/>
															  
			</div>
		</div>



															<div class="row">
			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="cphone">@lang('label.phone')</label>
				<input class="form-control input-sm" type="text" name="cphone" id="cphone"   value="{{ $cphone ?? '' }}"/>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="email">@lang('label.email')<span class="red">*</span></label>
				<input class="form-control input-sm" type="text" name="email" id="email"   value="{{ $email ?? '' }}"/>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="cfacebook">@lang('label.fb')</label>
				<input class="form-control input-sm" type="text" name="cfacebook" id="cfacebook"   value="{{ $cfacebook ?? '' }}"/>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="cline">@lang('label.line')</label>
				<input class="form-control input-sm" type="text" name="cline" id="cline"   value="{{ $cline ?? '' }}"/>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="ctelegram">@lang('label.telegram')</label>
				<input class="form-control input-sm" type="text" name="ctelegram" id="ctelegram"   value="{{ $ctelegram ?? '' }}"/>
															  
			</div>

			<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
				<label class="frm-label" for="cwechat">@lang('label.wechat')</label>
				<input class="form-control input-sm" type="text" name="cwechat" id="cwechat"   value="{{ $cwechat ?? '' }}"/>
															  
			</div>


		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<label class="frm-label" for="caddress">@lang('label.add')</label>
				<input class="form-control input-sm" type="text" name="caddress" id="caddress"   value="{{ $caddress ?? '' }}"/>
															  
			</div>
		</div>

		


		<div class="row">
			<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
				<label class="frm-label" for="cnote">@lang('label.note')</label>
				<input class="form-control input-sm" type="text" name="cnote" id="cnote"   value="{{ $cnote ?? '' }}"/>
															  
			</div>
		</div>


				</div>
			</div>								
																	
		</div><!-- /.Panel -->	




	</div><!-- /.col -->

</div>					
								

</form>

@stack('plugin')
	@if (!session('ajax_access'))
		@include('Filemanager.manager')
	@endif
@stop




								

