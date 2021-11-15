@extends('backend.layout')

@section('header_import')
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
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
				@include('backend.widget.btnav', ['btnnew' => 'no', 'btntrash' => 'no', 'btnactive' => 'no'])			
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->
 
	<!--DRAW Content -->
	<div class="row">
    <div class="col-xs-12">
      <!-- PAGE CONTENT BEGINS -->
      <div class="row">
        <div class="col-xs-12">
          <div class="well text-center">
            <form action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" method="POST">
                {{ csrf_field() }}	
                <input type="hidden" name="btype" id="btype" value="db">
                <button type="submit" class="btn btn-primary btn-app radius-4" onclick="$('#btype').val('db');">
                  <i class="ace-icon fas fa-database bigger-230"></i>@lang('label.lb229')
                </button>
                <span style="display:inline-block; width:30px"></span>
                <button type="submit" class="btn btn-success btn-app radius-4" onclick="$('#btype').val('pic');">
                  <i class="ace-icon far fa-images bigger-230"></i>@lang('label.lb230')
                </button>
            </form>
            
          </div>
        </div>
      </div>
    </div>
</div>
<!--/. draw content -->

@stop


								

