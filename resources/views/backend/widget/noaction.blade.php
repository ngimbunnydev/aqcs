@extends('backend.layout')
@section('header_import')

@stop

@section('footer_import')
	
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
					{{__('ccms.noaction')}}
				</small>
			</h1>
			</div>
			<div class="col-sm-6">
						
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->

	<!--DRAW Content -->
		
		<div class="alert alert-danger">
											<!-- <button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button> -->

											<!-- <strong>
												<i class="ace-icon fa fa-times"></i>
												
											</strong> -->

											
											{{$caption}}
											<br>
										</div>

	<!--/. draw content -->

@stop


								

