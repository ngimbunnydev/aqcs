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

			@if (session('success'))
				$.gritter.add({
								title: 'Success:',
								text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong> {{ session('success') }}',
								sticky: false,
								class_name: 'gritter-success gritter-center'
							});

			@endif


			

		});


		var updateNestableTree = function(e)
        {
            var list   = e.length ? e : $(e.target),
                output = list.data('sourceId');
            if (window.JSON) {
                //alert(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                var listdata = window.JSON.stringify(list.nestable('serialize'));
                airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'pcategory', ajaxact:'editlist', listdata: listdata},'','title',false);

            } else {
                output.val('JSON browser support required for this demo.');
            }
        };


		$(function($){
			
				//$('.dd').nestable({handleClass:'undifine'});
				//$('.dd').nestable({dragStop  : function(e) {alert(sourceId) }});

				$('.dd').nestable().on('change',updateNestableTree);
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
								


								<div class="dd dd-draghandle" style="max-width: 100%">
									
									@php
										$elements=$cat_tree;
									@endphp


									@if (count($elements) > 0)
								    	 @include('backend.vpcategory.nest', $elements)
									@endif



								</div>



							</div><!-- /. col -->
		</div>

		

	<!--/. draw content -->

@stop


								

