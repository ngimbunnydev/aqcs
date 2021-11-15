@if(session('input'))
	@php 
		$inputdata=session('input');
	@endphp
@endif

@if(!empty($input))
	@php 
		$inputdata=$input;
	@endphp
@endif

@if( ! empty($inputdata))
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
	

	<!-- grid manager-->
	<link rel="stylesheet" href="{{asset('resources/assets/editor/dist/summernote.css')}}">
	<link rel="stylesheet" href="{{asset('resources/assets/grideditor/dist/grideditor.css')}}">

	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
	<!--/******************
	*	Add file manager plugin *
	*******************/-->
	<link rel="stylesheet" href="{{asset('app/Plugins/Splitjs/splitjs.css')}}" />
	<!--JS Tree-->
	<link rel="stylesheet" href="{{asset('app/Plugins/Jstree/dist/themes/default/style.min.css')}}" />
	

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
	
	


  <!-- grid manager-->
   <script src="{{asset('resources/assets/editor/dist/summernote.js')}}"></script>
   <script src="{{asset('resources/assets/editor/plugin/summernote-table-styles.js')}}"></script>
  <script src="{{asset('resources/assets/grideditor/dist/jquery.grideditor.js')}}"></script>
  <script>
  		var editorContext; /*global variable for store Summernote object when v try to insert image*/
		$( document ).ready(function() {
			// Initialize grid editor
			$('#myGrid').gridEditor({
				 //new_row_layouts: [],
				content_types: ['summernote'],
				summernote: {
					config: {
						callbacks: {
							onInit: function() {
								var element = this;
								console.log('init done', element);
							}
						}
					}
				}


			});
			
			// Get resulting html
			var html = $('#myGrid').gridEditor('getHtml');
			console.log(html);
		});
	</script>

  
  <script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('/resources/assets/arcetheme/js/spinbox.min.js')}}"></script>
  
  <script type="text/javascript">
	$( document ).ready(function() {


				$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});

				///////////

				$('#ordering').ace_spinner({value:0,min:0,step:1, btn_up_class:'btn-info' , btn_down_class:'btn-info'})
				.closest('.ace-spinner')
				.on('changed.fu.spinbox', function(){
					//console.log($('#ordering').val())
				}); 
	});
  </script>


	<!--/******************
	* Permission combo *
	*******************/-->
  <script>
	 $( "#pm_id" ).change(function() {
	  	if(this.value==3)
	  	{
	  		$("#txt_pwd").removeClass("hide");
	  	}
	  	else
	  	{
	  		$("#txt_pwd").addClass("hide");
	  	}
	});

	

  </script>

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
		var filemanagerSetting={};
		

		Split(['#file_category', '#file_listing'], {
			sizes: [24, 76],
			minSize: 200
		});
		/**-Browse File button @ each object-**/
		$("#browsefilemanager").click(function(e){
				e.preventDefault();
				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=1;
				filemanagerSetting.filetype='';
				filemanagerSetting.givent_txtbox='txt_scrshot';
				filemanagerSetting.calledby='public';
				filemanagerSetting.numperpage=12;
				filemanagerSetting.objtable='cms_articlefile';
				filemanagerSetting.idvalue = 0;

				
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		});

		/**- get from exsiting file **/
		$(document).ready(function() {
			filemanagerSetting=jsconfig.filemanagerSetting;
			@if (session('input') || !empty($input))
			objGetFiles('yes','',filemanagerSetting.objtable,'{{$media}}',0);
			@endif
		});
	</script>
	<!--/*end file manager*/-->

	<!--/******************
	*	Add widget manager plugin *
	*******************/-->
	<script src="{{asset('app/Plugins/Widgetmanager/jsfun.js')}}"></script>

	<!--/*end widget manager*/-->


	<script>
		
		$( "#frmadd-{{$obj_info['name']}}" ).submit(function( event ) {
			var html='';
			html = $('#myGrid').gridEditor('getHtml');
				$('<input />').attr('type', 'hidden')
		          .attr('name', "contents")
		          .attr('value', html)
		          .appendTo('#frmadd-{{$obj_info['name']}}');
			
		  	
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
<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} or '' }}">						
								
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
		<span class="frm-label f-right"><span class="red">*</span> @lang('ccms.isrequire')</span>

		<!--start-->
			<div class="row">

				
				<div class="col-xs-12 col-sm-12 col-lg-12">

																<label class="frm-label" for="p_name">
																	Section Name
																	<span class="red">*</span>
																</label>
																<br />
																 <input class="form-control input-sm" type="text" name="blogsname" id="blogsname"   value="{{$blogsname or ''}}"/>
																	

															</div>
															
				<div class="col-xs-12 col-sm-12 col-lg-12">
					@php
						$des_default='<div class="row">
								<div class="col-sm-12 col-xs-12 col-lg-12" 
					style="">
									
									
									<div class="ge-content 
					ge-content-type-summernote" 
					data-ge-content-type="summernote">
										
									</div>
								</div>
							</div>';
							$des=isset($contents) ? $contents: $des_default;
					@endphp											
																
					<div id="myGrid">
						{!!html_entity_decode($des)!!}
					</div> <!-- /#myGrid -->
																
				</div>
			</div><br/>								
														
		<!--stop-->


	
	

	</div> <!-- /.col -->

	

</div>
</form>
@include('Filemanager.manager')
@include('Widgetmanager.manager')
@stack('plugin')
@stop


								

