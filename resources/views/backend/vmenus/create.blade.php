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
	<script src="{{asset('/resources/assets/arcetheme/js/jquery.nestable.min.js')}}"></script>

	<script>

		function addtomenu(jsondata) {
		    $(jsondata.container).append(jsondata.data);
		}

		var updateNestableMenu = function(e)
        {
            var list   = e.length ? e : $(e.target),
                output = list.data('sourceId');
            if (window.JSON) {
                //alert(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                var listdata = window.JSON.stringify(list.nestable('serialize'));
                airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'menus', ajaxact:'editlist', listdata: listdata, parent_id:'{{${$fprimarykey} ?? '-1'}}'},'','title',false);

            } else {
                output.val('JSON browser support required for this demo.');
            }
        };


		
		$(function($){
			
				//$('.dd').nestable({handleClass:'undifine'});
				//$('.dd').nestable({dragStop  : function(e) {alert(sourceId) }});

				$('.dd').nestable().on('change',updateNestableMenu);
		});

		$(document).on("click", ".btnsearch", function (ev) {
		    ///
		    	var givent_txtbox=$(this).parent().parent().children('.search-query');
		    	var givent_val = givent_txtbox.val();

		    	//alert(givent_val);

		    	if(!!givent_val)
		    	{
		    		var $obj = $(this).data('obj');
		    		var $modelid = $(this).data('modelid');
		    		var $objinfo={'obj':$obj, 'modelid':$modelid, 'searchtext': givent_val};
		    		$objinfo = JSON.stringify($objinfo);
		    		airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'menus', ajaxact:'menufilter', objinfo:$objinfo},'','Test',false);
		    	}
		    	
		    ///
		});

		$(document).on("click", "#resetdata", function (ev) {
		    ///

		    	var temp=$(this).data('temp');
		    	var show = $(this).data('show');
		    	$('#'+show).html($('#'+temp).html());

		    	
		    ///
		});


		$(document).on("click", ".addtomenu", function (ev) {
		    ///
		    	var $obj = $(this).data('obj');
		    	var $modelid = $(this).data('modelid');
		    	var $objinfo={'obj':$obj, 'modelid':$modelid, 'parent_id':'{{${$fprimarykey} ?? '-1'}}'};
		    	$objinfo = JSON.stringify($objinfo);
		    	var $ele = $(this).data('ele');
		    	var selectedItems = new Array();
				$('input[name="'+$ele+'[]"]:checked').each(function() {
					selectedItems.push($(this).val());
					$(this).prop('checked',false);
				});

				if(selectedItems.length > 0 || $obj=='custom'){
				    var json_selected = JSON.stringify(selectedItems);
				    airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'menus', ajaxact:'addtomenu', objinfo:$objinfo , menusitems : json_selected},'','Test',false);
				}
				

				
		    	
		    ///
		});

		$(document).on("click", ".editmenu", function (ev) {
		    ///
		    	var $menuid = $(this).data('menu');

		    	airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'menus', ajaxact:'editmenu', ajaxid:$menuid},'','Test',true);
				
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
		@include('backend.widget.btnaa',['btnsave' => 'yes', 'btnnew' => 'yes', 'btnapply' => 'yes', 'btncancel' => 'yes'])							
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->


			
								
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
		<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span><br><br>
		

		<div class="row">
			<div class="col-xs-6 col-sm-4 col-lg-4">
				<div id="accordion" class="accordion-style1 panel-group">

					<!--start panel-->
						<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Pages

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapse3">
													<div class="panel-body pding-5">
														@php
															use App\Models\Backend\Pages;
															$pages = new Pages;
															$pageslist = $pages->select(\DB::raw("p_id AS id, p_name AS title"
					                                                )
					                                        )
					                                        ->where('trash', '<>', 'yes')
					                                        ->orderby('p_id', 'asc')
					                                        ->offset(0)
                											->limit(10)->get()->toArray();
                									
														@endphp

														

														<!-- strat tab-->
															<div class="row">
																		<div class="col-xs-12">
																			
																			

																			    <div class="input-group">
																					<input type="text" class="form-control search-query" placeholder="Type your query">
																					<span class="input-group-btn">
																						<button type="button" class="btn btn-white btn-primary btnsearch" data-obj="pages" data-modelid='p_id'>
																							<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																							Search
																						</button>
																					</span>
																				</div>

																			
																		</div>
															</div>
															<hr style="margin: 5px 0px">

															<span id="pages-menu">
																@php
																	$pagesrecent=checkbox_select('pages[]',$pageslist,[],'');
																@endphp
																{!!$pagesrecent!!}
															</span>

															<span class="hide" id="pages-menu-temp">
																{!!$pagesrecent!!}
															</span>
																	

														<!-- stop tab -->


														<hr style="margin: 3px 0px">

														<button class="btn btn-minier" id='resetdata' data-temp='pages-menu-temp' data-show='pages-menu'>Reset</button>
												
														
														<button style="float: right;" type="button" class="btn btn-white btn-primary addtomenu" data-obj="pages" data-modelid='p_id' data-ele='pages'>Add</button>
													</div>
												</div>
						</div>
					<!--end panel -->

					<!--start panel-->
						<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Article

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapseOne">
													<div class="panel-body pding-5">
														@php
															use App\Models\Backend\Article;
															$article = new Article;
															$articlelist = $article->select(\DB::raw("a_id AS id, 
					                                                    JSON_UNQUOTE(title->'$.".$dflang[0]."') AS title"
					                                                )
					                                        )
					                                        ->where('trash', '<>', 'yes')
					                                        ->where('md_id',0)
					                                        ->orderby('a_id', 'asc')
					                                        ->offset(0)
                											->limit(10)->get()->toArray();
                									
														@endphp

														

														<!-- strat tab-->
															<div class="row">
																		<div class="col-xs-12">
																			
																			

																			    <div class="input-group">
																					<input type="text" class="form-control search-query" placeholder="Type your query">
																					<span class="input-group-btn">
																						<button type="button" class="btn btn-white btn-primary btnsearch" data-obj="article" data-modelid='a_id'>
																							<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																							Search
																						</button>
																					</span>
																				</div>

																			
																		</div>
															</div>
															<hr style="margin: 5px 0px">

															<span id="article-menu">
																@php
																	$articlerecent=checkbox_select('article[]',$articlelist,[],'');
																@endphp
																{!!$articlerecent!!}
															</span>

															<span class="hide" id="article-menu-temp">
																{!!$articlerecent!!}
															</span>
																	

														<!-- stop tab -->


														<hr style="margin: 3px 0px">

														<button class="btn btn-minier" id='resetdata' data-temp='article-menu-temp' data-show='article-menu'>Reset</button>
												
														
														<button style="float: right;" type="button" class="btn btn-white btn-primary addtomenu" data-obj="article" data-modelid='a_id' data-ele='article'>Add</button>
													</div>
												</div>
						</div>
					<!--end panel -->


					<!--start panel-->
						@include('backend.vmenus.product')
					<!--end panel -->


					<!--start panel-->
						<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Article Category

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapseTwo">
													<div class="panel-body pding-5">
														
															{!!CategoryCheckboxTree($cat_tree,"","acategory[]",$c_id ?? []) !!}
														
														<hr style="margin: 3px 0px">
														<button style="float: right;" type="button" class="btn btn-white btn-primary addtomenu" data-obj="acategory" data-modelid='c_id' data-ele='acategory'>Add</button>
													</div>
												</div>
						</div>
					<!--end panel -->


					<!--start panel-->
						<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#productcategory">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Product Category

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="productcategory">
													<div class="panel-body pding-5">
														
															{!!CategoryCheckboxTree($pcat_tree,"","pcategory[]",$c_id ?? []) !!}
														
														<hr style="margin: 3px 0px">
														<button style="float: right;" type="button" class="btn btn-white btn-primary addtomenu" data-obj="pcategory" data-modelid='c_id' data-ele='pcategory'>Add</button>
													</div>
												</div>
						</div>
					<!--end panel -->

					<!--start panel-->
						<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTree">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Custom Menu

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="collapseTree">
													<div class="panel-body pding-5">
														<button style="float: right;" type="button" class="btn btn-white btn-primary addtomenu" data-obj="custom" data-modelid='' data-ele=''>Add</button>
													</div>
												</div>
						</div>
					<!--end panel -->


					

				</div><!-- /.panel group -->
			</div> <!-- /.col -->

			<!-- *****************Right Blog*************** -->
			<div class="col-xs-6 col-sm-8 col-lg-8" style="margin-top: -4px">
				<div class="widget-box">
											<div class="widget-header">
												<div class="row pding-tb-5">
													<div class="col-xs-12 col-sm-6 col-lg-6">
														<form name="frmadd-{{$obj_info['name']}}" id="frmadd-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" enctype="multipart/form-data">
	{{ csrf_field() }}										<div class="form-inline">
																<label class="frm-label" for="seoindex">Menu Name<span class="red">*</span></label>
																<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{${$fprimarykey} ?? ''}}">	
																<input type="text" class="form-control input-sm" name="title" id="title" value="{{ $title ?? '' }}" style="width: 70%">
															</div>
														</form>
													</div>

													<div class="col-xs-12 col-sm-6 col-lg-6">

													</div>
												</div>
											</div>

											<div class="widget-body">
												<div class="widget-main">
													<div class="dd dd-draghandle" style="max-width: 100%">
															@if(empty($itemtree))
															    
																<ol class='dd-list'>
																</ol>
															@else
																@php
																	$elements=$itemtree;
																@endphp


																@if (count($elements) > 0)
															    	 @include('backend.vmenus.nest', $elements)
																@endif
															@endif
														
													</div>
												</div>
											</div>
										</div>
			</div><!-- /.col -->


		</div><!-- /.row2 -->

	</div> <!-- /.colmain -->

</div>





@stack('plugin')
	
@stop


								

