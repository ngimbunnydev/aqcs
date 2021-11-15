@extends('backend.layout')
@section('header_import')

@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>

	<script src="{{asset('/resources/assets/arcetheme/js/jquery.nestable.min.js')}}"></script>

	<!--/*Use confirmation for Delete or Destroy*/-->
	<script src="{{asset('/resources/assets/arcetheme/js/bootbox.js')}}"></script>




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


		var updateNestableDatalist = function(e)
        {
            var list   = e.length ? e : $(e.target),
                output = list.data('sourceId');
            if (window.JSON) {
                //alert(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                var listdata = window.JSON.stringify(list.nestable('serialize'));
                airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'modules', ajaxact:'editlist', listdata: listdata, parent_id:'{{${$fprimarykey ?? 'md_id'} ?? '-1'}}'},'','title',false);

            } else {
                output.val('JSON browser support required for this demo.');
            }
        };


		$(function($){
			
				//$('.dd').nestable({handleClass:'undifine'});
				//$('.dd').nestable({dragStop  : function(e) {alert(sourceId) }});

				$('.dd').nestable({maxDepth: 1}).on('change',updateNestableDatalist);
		});


		/*Use confirmation for Delete or Destroy*/
		$(".bootbox-confirm").on(ace.click_event, function() {
				var act = $(this).data('act');
					bootbox.confirm("{{__('ccms.ays')}}", function(result) {
						if(result) {
							$(location).attr('href', act);
						}
					});
		});


	</script>
@stop	


@section('app')

	<div class="page-header" data-spy="affix" data-offset-top="60">
		<div class="row">
			<div class="col-sm-6">
			<h1>
				{!! $obj_info['icon'] !!}
				<a href="{{url_builder($obj_info['routing'],[$obj_info['name'],'index'])}}">
					{!!$obj_info['title']!!}
				</a>
				<small>
					<i class="ace-icon fa fa-angle-double-right"></i>
					{{$caption}}
				</small>
			</h1>
			</div>
			<div class="col-sm-6">
				@include('backend.widget.btnav', ['btnnew' => 'yes', 'btntrash' => 'no', 'btnactive' => 'yes'])			
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->

	<!--DRAW Content -->
	@php
		$querytitle=url_builder($obj_info['routing'],[$obj_info['name'],'index'],array_merge(['sort=title'], $querystr));
	@endphp
		<div class="row">
	
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
							
									
									@php
										$datalist=[];
										$elements=$cat_tree;
									@endphp


									@if (count($elements) > 0)
								    	@foreach($elements as $element)

								    		@php
														$hili='';
														$id = explode(',',session('id'));
														
														if(in_array($element['id'],$id)) $hili = "style='background-color: #ffffdd'";
											@endphp
								    		<!--start panel-->
												<div class="panel panel-default">
																		<div class="panel-heading" {!!$hili!!}>
																			<div class="row">
																				<div class="col-xs-6 col-sm-6 col-lg-6">
																					<h4 class="panel-title">
																						<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$element['id']}}">
																							<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
																							&nbsp;{{$element['title']}} <small>({{$element['moduletype']}})</small>

																						</a>


																					</h4>

																				</div>

																				<div class="col-xs-6 col-sm-6 col-lg-6">
																					<div class="pull-right action-buttons">

																					@include('backend.widget.actmenu',['rowid'=>$element['id'], 'btnedit' => 'yes', 'btnduplicate' => 'no', 'btndelete' => 'yes','btnrestore' => 'yes','btndestroy' => 'yes', 'delete_cfm' => 'yes', 'destroy_cfm' => 'yes'])
																					</div>

																				</div>
																				

																			</div>
																			

																		</div>

																		<div class="panel-collapse collapse" id="collapse{{$element['id']}}">
																			<div class="panel-body pding-5">
																				<div class="dd dd-draghandle" style="max-width: 100%">
																					<ol class='dd-list'>

																				@if (!empty($element['children']))

																					@php ($i = $datalist=$element['children'])
																					@include('backend.vmodule.nest', $datalist)
																				@endif

																			</ol>

																			</div>
																			</div>
																			
																			
																		</div>
												</div>
											<!--end panel -->

								    	@endforeach
									@endif






							</div><!-- /. col -->
		</div>

		

	<!--/. draw content -->

@stop


								

