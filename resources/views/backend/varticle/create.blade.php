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
	@php $media='0'; @endphp
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


		@php
			foreach (config('ccms.multilang') as $key)
			{
				$langGrid [] = "#myGrid-" . $key[0];
			}

			$langGrid = implode(',', $langGrid);
		@endphp

		$( document ).ready(function() {
			// Initialize grid editor
			$('{{$langGrid }}').gridEditor({
				new_row_layouts: [],
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
			var html = $('{{$langGrid }}').gridEditor('getHtml');
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

				airWindows('elements_ctn', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'modules', ajaxact:'generateform', parent : {{$ab_id or -1}} },'','Test',false);
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

	$( "#newcategory" ).click(function() {
		airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'acategory', ajaxact:'create', ajaxnext : 'ajaxreturn'},'','Test',true);
	});

	$( "#ab_id" ).change(function() {
	  	airWindows('elements_ctn', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'modules', ajaxact:'generateform', parent : $(this).val()},'','Test',false);
	});

	$("input[name^='c_id[]']").click(function() {
	  if ($(this).is(':checked')) {
	    //alert($(this).val());
	    var attr = {!!json_encode($category_attr)!!};
	    console.dir(attr);
	    $ab_id = attr[$(this).val()];
	    $("#ab_id").val($ab_id).change();
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
		var editorContext; /*global variable for store Summernote object when v try to insert image*/

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
				//filemanagerSetting.numperpage=5;
				filemanagerSetting.objtable='cms_articlefile';
				filemanagerSetting.idvalue = 0;

				
				categoryid=$('#filecategory').val();
				
				openMediaPanel(categoryid);
		});


		$(document).on("click", "#btn-browe-image", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='image';
				filemanagerSetting.givent_txtbox=$(this).data('giventtextbox');
				givent_txtbox=$(this).parent().parent().children('#image');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
		});


		$(document).on("click", "#btn-browe-file", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='docs';
				filemanagerSetting.givent_txtbox=$(this).data('giventtextbox');
				givent_txtbox=$(this).parent().parent().children('#image');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
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

			@foreach(config('ccms.multilang') as $lang)
				var html = $('#myGrid-{{$lang[0]}}').gridEditor('getHtml');
				$('<input />').attr('type', 'hidden')
		          .attr('name', "des-{{$lang[0]}}")
		          .attr('value', html)
		          .appendTo('#frmadd-{{$obj_info['name']}}');
			@endforeach
			
		  	
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
<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} ?? '' }}">						
								
<div class="row">
	<div class="col-xs-12 col-sm-9 col-lg-10">
		<span class="frm-label f-right"><span class="red">*</span> @lang('ccms.isrequire')</span>
		<div class="tabbable">
											<ul class="nav nav-tabs" id="myTab">
												<li class="dropdown active">
													<a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
														Content &nbsp;
														<i class="ace-icon fa fa-caret-down bigger-110 width-auto"></i>
													</a>

													<ul class="dropdown-menu dropdown-info">
														
														@php ($active = 'active') @endphp
														@foreach (config('ccms.multilang') as $lang)
															<li class="{{$active}}">
																<a data-toggle="tab" href="#dropdown{{$lang[0]}}" aria-expanded="false">
																	@lang('ccms.'.$lang[0])
																</a>
															</li>
															@php ($active = '') @endphp
														@endforeach
														
													</ul>
												</li>

										

												<li class="">
													<a data-toggle="tab" href="#media" aria-expanded="false">
														Media
													</a>
												</li>

												<li class="">
													<a data-toggle="tab" href="#attribute" aria-expanded="false">
														Attribute
													</a>
												</li>

												<!-- <li class="">
													<a data-toggle="tab" href="#sharing" aria-expanded="false">
														Sharing
													</a>
												</li> -->
												
											</ul>

											<div class="tab-content">

												<!-- Multi Languages -->

												@php ($active = 'active in') @endphp
												@foreach (config('ccms.multilang') as $lang)
													<div id="dropdown{{$lang[0]}}" class="tab-pane fade {{$active}}">
														<div class="row">

															<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
																@if(!empty($active))
																<label style="float: right;"> 
																	<span class="lbl frm-label">Synce language after saved&nbsp;</span>
																	<input name="synlang" id="synlang" class="ace ace-switch ace-switch-2" type="checkbox" value="yes" />
																	<span class="lbl">&nbsp;</span>
																</label>
																@endif

																<label class="frm-label" for="">
																	{{$lang[1]}}
																	<i class="ace-icon fa fa-angle-down"></i> 
																	Title
																	@if(!empty($active))
																	<span class="red">*</span>
																	@endif
																</label>

																<input type="text" name="title-{{$lang[0]}}" id="title-{{$lang[0]}}" placeholder="Text Field" class="form-control input-sm" value="{{ ${'title-'.$lang[0]} ?? '' }}">



															</div>

															<div class="col-xs-12 col-sm-12 col-lg-12">
																
																 
																<!--<textarea class="summernote input-block-level" name="content-{{$lang[0]}}" id="content-{{$lang[0]}}" rows="18">


																	
																</textarea>-->
																<!-- /#myGrid -->
																@php
																	$des_default='<div class="row">
																			<div class="col-sm-12 col-xs-12 col-lg-12" style="">
																				
																				
																				<div class="ge-content ge-content-type-summernote" data-ge-content-type="summernote">
																					
																				</div>
																			</div>
																		</div>';

																		$des=!empty(${'des-'.$lang[0]})?${'des-'.$lang[0]}:$des_default;
																@endphp
																<div id="myGrid-{{$lang[0]}}">
						
																		
																		{!!html_entity_decode($des)!!}
																
																
																</div> <!-- /#myGrid -->
																
															</div>
														</div><br/>

														<div class="row">

														  <div class="col-xs-12 col-sm-12 col-lg-6 form-group">
															<label class="frm-label" for="metatitle-{{$lang[0]}}">Meta Title (maximum of 60 chars)</label>
															<input class="form-control input-sm" type="text" name="metatitle-{{$lang[0]}}" id="metatitle-{{$lang[0]}}"   value="{{ ${'metatitle-'.$lang[0]} ?? '' }}"/>
														  </div>
														  
														 <div class="col-xs-12 col-sm-12 col-lg-6 form-group">
															<label class="frm-label" for="metakeyword-{{$lang[0]}}">Meta Keyword (seperate by comma)</label>
															<input class="form-control input-sm" type="text" name="metakeyword-{{$lang[0]}}" id="metakeyword-{{$lang[0]}}" value="{{ ${'metakeyword-'.$lang[0]} ?? '' }}"/>
														  </div>
														  
														  <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
															<label class="frm-label" for="metades-{{$lang[0]}}">Meta Description (maximum of 160 chars)</label>
															<input class="form-control input-sm" type="text" name="metades-{{$lang[0]}}" id="metades-{{$lang[0]}}" value="{{ ${'metades-'.$lang[0]} ?? '' }}"/>
														  </div>

														</div>


													</div>
													@php ($active = '') @endphp
												@endforeach		

												<!-- *************** -->		
														


												<div id="media" class="tab-pane fade">
													<!-- MEDIA Block-->
													@include('Filemanager.home')
												</div>


												<div id="attribute" class="tab-pane fade">
													<!-- Extra Fields Block-->
														<div class="row">
														  <div class="col-xs-12 col-sm-12 col-lg-12" style="padding-bottom:2px">

	 														<div class="controls form-inline">
													            <label class="red" for="ab_id">Attribute</label>
													            <select name="ab_id" id="ab_id">
													            	<option value="-1">-- {{__('ccms.ps')}} --</option>
													            	{!!cmb_listing($attributes,[$ab_id ?? ''],'','')!!} 
													            </select>
													            
													        </div>


														  </div>

														<!-- fields container --><div class="col-xs-12 col-sm-12 col-lg-12" style="margin-bottom: 5px; border-top:1px solid #ddd">
															<br>
															<span id="elements_ctn" class="span_file_container">
																
															</span>
														<!-- End container --></div>

														</div> <!--/. Row -->
												</div>

												

												
											</div>
										</div><!--/. Tab -->	
	
	

	</div> <!-- /.col -->

	<!-- Right Blog ------------------------------------------------------------------------------------- -->
	<div class="col-xs-12 col-sm-3 col-lg-2">
		<div id="accordion" class="accordion-style1 panel-group">


										<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordionx" href="#collapseTwo">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Category

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapseTwo">
													<div class="panel-body pding-0">
														<p class="pding-5">
															<span class="f" style="float: right; right: 0">
															<a href="#" id="newcategory">New</a>
															</span>
														</p>
														<span id="getcategory">
															{!!CategoryCheckboxTree($cat_tree,"","c_id[]",$c_id ?? []) !!}
														</span>
														
													</div>
												</div>
										</div>	




										<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionx" href="#collapseOne">
															
															<i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>

															&nbsp;Setting
															
														</a>
													</h4>
												</div>

												<div class="panel-collapse collapse in" id="collapseOne">
													<div class="panel-body">
														<div class="row">
															<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
																<label class="frm-label" for="status">Status</label>

																	<br />

																	{!!check_select("status",array("Publish"=>"publish","Draft"=>"draft"),$status ?? '',"")!!}
															

															</div>

															<div class="col-sm-12 form-group">
																<label class="frm-label" for="frontpage">Front Page</label>

																	<br />
																	{!!check_select("frontpage",array("No"=>"no","Yes"=>"yes"),$frontpage ?? '',"")!!}

															</div>

															<div class="col-sm-12 form-group">
																<label class="frm-label" for="searchable">Searchable</label>

																	<br />
																	{!!check_select("searchable",array("Yes"=>"yes","No"=>"no"),$searchable ?? '',"")!!}

															</div>

															<div class="col-sm-12 form-group">
																<label class="frm-label" for="seoindex">SEO indexing</label>

																	<br />
																	{!!check_select("seoindex",array("No"=>"no","Yes"=>"yes"),$seoindex ?? '',"")!!}

															</div>

															<div class="col-sm-12 form-group">
																<label class="frm-label" for="inquiry">Order</label>

																	<br />
																	<div class="input-group">
																		<input type="text" name="ordering" id="ordering" class="spinbox-input form-control input-sm text-center" value="{{$ordering ?? ''}}">
																		
																	</div> 

															</div>

															<div class="col-sm-12 form-group">
															<label class="frm-label" for="chk_recycle">Post Date</label>
															<br />
															<div class="input-group">
																	<input class="form-control input-sm date-picker" name="add_date" id="add_date" type="text" data-date-format="dd-mm-yyyy" value="{{$add_date ?? ''}}">
																	<span class="input-group-addon">
																		<i class="fa fa-calendar bigger-110"></i>
																	</span>
																</div>
																

															</div>

															<div class="col-sm-12 form-group">
																<label class="frm-label" for="chk_recycle">Expire Date</label>
																<br />
																<div class="input-group">
																		<input class="form-control input-sm date-picker" name="exp_date" id="exp_date" type="text" data-date-format="dd-mm-yyyy" value="{{$exp_date ?? ''}}">
																		<span class="input-group-addon">
																			<i class="fa fa-calendar bigger-110"></i>
																		</span>
																	</div>
																	

															</div>

															


															<div class="col-xs-12 col-sm-12 col-lg-12 form-group">

																<label class="frm-label" for="">Permission</label>

																	<br />
																	<select class="form-control input-sm" name="pm_id" id="pm_id">
																	   
																		{!!cmb_listing(config('ccms.permission'),[$pm_id ?? ''],'','')!!} 
																	  </select>
																	

															</div>

															@php
																$hide=(isset($pm_id) && (int)$pm_id==2)?'':'hide';
															@endphp
															<div id="txt_pwd" class="col-xs-12 col-sm-12 col-lg-12 form-group {{$hide}}">

																<label class="frm-label" for="pm_pwd">Password</label>
																<br />
																 <input class="form-control input-sm" type="text" name="pm_pwd" id="pm_pwd"   value="{{$pm_pwd ?? ''}}"/>
																	

															</div>


															<div class="col-xs-12 col-sm-12 col-lg-12 form-group">

																<label class="frm-label" for="">Template</label>

																	<br />
																	<select class="form-control input-sm" name="p_id" id="p_id">
																	   
																		{!!cmb_listing($pages,[$p_id ?? ''],'','')!!} 
																	  </select>
																	

															</div>



														</div><!-- /.row -->
													</div>
												</div>
											</div>

																		

											
		</div>
	</div><!-- /.col -->

</div>
</form>
@include('Widgetmanager.manager')
@stack('plugin')


@stop




								

