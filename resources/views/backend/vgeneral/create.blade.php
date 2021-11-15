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
	

	<!--/******************
	*	Add file manager plugin *
	*******************/-->
	
	<link rel="stylesheet" href="{{asset('app/Plugins/Splitjs/splitjs.css')}}" />
	<!--JS Tree-->
	<link rel="stylesheet" href="{{asset('app/Plugins/Jstree/dist/themes/default/style.min.css')}}" />
	

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
	
	




	

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


		$(document).on("click", "#btn-browe-logo", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='image';
				filemanagerSetting.givent_txtbox='object';
				givent_txtbox=$('#logo');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
		});


		$(document).on("click", "#btn-browe-icon", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='image';
				filemanagerSetting.givent_txtbox='object';
				givent_txtbox=$('#icon');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
		});


	</script>
	<!--/*end file manager*/-->




	
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
		@include('backend.widget.btnaa',['btnsave' => 'yes', 'btnnew' => 'no', 'btnapply' => 'no', 'btncancel' => 'no'])							
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->


<form name="frmadd-{{$obj_info['name']}}" id="frmadd-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" enctype="multipart/form-data">
{{ csrf_field() }}	
<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} ?? '' }}">						
								
<div class="row">
	<div class="col-xs-12 col-sm-6 col-lg-6">

		<!--Panel -->	
		<div class="widget-box">						
										
				<div class="widget-header">
				<h4 class="smaller">
					Site
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<div class="row">
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="nativename">Company Native Name</label>
							<input class="form-control input-sm" type="text" name="nativename" id="nativename"   value="{{$nativename ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="latinname">Company Latin Name</label>
							<input class="form-control input-sm" type="text" name="latinname" id="latinname"   value="{{ $latinname ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="logo">Logo</label>
							<div class="input-group">
                                <input class="form-control input-sm" type="text" name="logo" id="logo" value="{{ $logo ?? '' }}" readonly="readonly" ondblclick="this.value=''"/>
                                <span class="input-group-btn">
        
                                    <button id="btn-browe-logo" class="btn btn-default btn-sm" type="button" style="height: 30px">
                                                Browse...
                                    </button>
                                </span>
                            </div>
						</div><!-- /.column -->


						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="icon">Icon</label>
							<div class="input-group">
                                <input class="form-control input-sm" type="text" name="icon" id="icon" value="{{ $icon ?? '' }}" readonly="readonly" ondblclick="this.value=''"/>
                                <span class="input-group-btn">
        
                                    <button id="btn-browe-icon" class="btn btn-default btn-sm" type="button" style="height: 30px">
                                                Browse...
                                    </button>
                                </span>
                            </div>
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="vat">VAT No.</label>
							<input class="form-control input-sm" type="text" name="vat" id="vat"   value="{{ $vat ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="">Home Page</label>

																	<br />
							<select class="form-control input-sm" name="homepage" id="homepage">
																	   
								{!!cmb_listing($pages,[$homepage ?? ''],'','')!!} 
							</select>	
						</div><!-- /.column -->
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="">Theme</label>

																	<br />
							<select class="form-control input-sm" name="theme" id="theme">
																	   
								{!!cmb_listing(['theme1'=>'Theme 1', 'theme2'=> 'Theme 2', 'theme3'=> 'Theme 3'],[$theme ?? ''],'','')!!} 
							</select>	
						</div><!-- /.column -->
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="">Authentication</label>

																	<br />
							<select class="form-control input-sm" name="auth" id="auth">
																	   
								{!!cmb_listing(['no'=>'No', 'yes'=> 'Yes'],[$auth ?? ''],'','')!!} 
							</select>	
						</div><!-- /.column -->
						  
						  
					</div><!-- /.row -->								
														
																				
					
				</div>
			</div>
		</div> <!-- /.Panel -->	


		<!--Panel -->	
		<div class="widget-box">						
										
				<div class="widget-header">
				<h4 class="smaller">
					Contact
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					<div class="row">
            
             

						 <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="rcvmail">Receiver Email (separate by comma)</label>
							<input class="form-control input-sm" type="text" name="rcvmail" id="rcvmail"   value="{{$rcvmail ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="address">Address</label>
							<input class="form-control input-sm" type="text" name="address" id="address"   value="{{$address ?? '' }}"/>		
						</div>	

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="phone">Phone</label>
							<input class="form-control input-sm" type="text" name="phone" id="phone"   value="{{$phone ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="phone1">Phone</label>
							<input class="form-control input-sm" type="text" name="phone1" id="phone1"   value="{{ $phone1 ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="website">Website</label>
							<input class="form-control input-sm" type="text" name="website" id="website"   value="{{ $website ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="email">Email</label>
							<input class="form-control input-sm" type="text" name="email" id="email"   value="{{ $email ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="map">Map</label>
							<input class="form-control input-sm" type="text" name="map" id="map"   value="{{ $map ?? '' }}"/>		
						</div>

					</div><!-- /.row -->	


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
					Social Network
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="facebook">Facebook</label>
							<input class="form-control input-sm" type="text" name="facebook" id="facebook"   value="{{ $facebook ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="youtube">Youtube</label>
							<input class="form-control input-sm" type="text" name="youtube" id="youtube"   value="{{ $youtube ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="rcvmail">Twitter</label>
							<input class="form-control input-sm" type="text" name="twitter" id="twitter"   value="{{ $twitter ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="linkedin">Linkedin</label>
							<input class="form-control input-sm" type="text" name="linkedin" id="linkedin"   value="{{ $linkedin ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->


					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="line">Line</label>
							<input class="form-control input-sm" type="text" name="line" id="line"   value="{{ $line ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="telegram">Telegram</label>
							<input class="form-control input-sm" type="text" name="telegram" id="telegram"   value="{{ $telegram ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->


				</div>
			</div>								
																	
		</div><!-- /.Panel -->	

		<!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					SMTP
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<div class="row">

						 <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="host">Host</label>
							<input class="form-control input-sm" type="text" name="host" id="host"   value="{{ $host ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="port">Port</label>
							<input class="form-control input-sm" type="text" name="port" id="port"   value="{{ $port ?? '' }}"/>		
						</div>	

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="user">User</label>
							<input class="form-control input-sm" type="text" name="user" id="user"   value="{{ $user ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="password">Password</label>
							<input class="form-control input-sm" type="text" name="password" id="password"   value="{{ $password ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="enctype">Encryption</label>
							<input class="form-control input-sm" type="text" name="enctype" id="enctype"   value="{{ $enctype ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="fromemail">From Email</label>
							<input class="form-control input-sm" type="text" name="fromemail" id="fromemail"   value="{{ $fromemail ?? '' }}"/>		
						</div>

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="fromname">From Name</label>
							<input class="form-control input-sm" type="text" name="fromname" id="fromname"   value="{{ $fromname ?? '' }}"/>		
						</div>

					</div><!-- /.row -->	
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


								

