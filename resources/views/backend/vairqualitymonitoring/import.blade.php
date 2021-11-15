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

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>

  <script>
  	$(document).ready(function() {
  		$('#file-import').ace_file_input({
					no_file: '{{ __('ccms.nofile') }}',
					btn_choose:'{{ __('ccms.choose') }}',
					btn_change: '{{ __('ccms.change') }}',
					droppable:false,
					onchange: null,
					thumbnail:false, //| true | large
					//whitelist:'xls|xlsx|csv',
					//blacklist:'exe|php'
					//onchange:''
					//allowExt: ["xls", "xlsx", "csv"],
					//allowMime: ["application/vnd.ms-excel"],
				}).on('change', function(){
					 airWindows('customerimportresult', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'{{ $obj_info['name'] }}', ajaxact:'loadimportdata'},'frmimport-{{ $obj_info['name'] }}','#airwindowsloader',false);
				});
        $('.ace-file-input a.remove').on('click', function(){
          $("#customerimportresult").html('');
        });
        $('#btnimport').on('click', function(){
          //window.location.href = window.location.href;
        });
        
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
      
    function reloadImportData(json){
      if(json.status){
        window.location.href = json.url;
      }
    }
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
		@include('backend.widget.btnaa',['btnimport' => 'yes', 'btnnew' => 'yes', 'btnapply' => 'yes', 'btncancel' => 'yes'])							
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->


<form name="frmimport-{{$obj_info['name']}}" id="frmimport-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" enctype="multipart/form-data">
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
	<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
		<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span>
	</div>
</div>


<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
    <div class="form-group">
        <label class="frm-label" for="file-import">@lang('ccms.choosefile') <span class="red">*</span></label>
        <label class="ace-file-input"><input type="file" id="file-import" name="file_import" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel.sheet.macroenabled.12" /></label>
    </div>
	</div> <!-- /.col -->
  
  <div class="col-xs-12">
    <span id="airwindowsloader" class="ace-icon fa fa-spinner fa-pulse icon-on-right bigger-110" style="display: none;"></span>
  </div>
  <div class="col-xs-12 col-sm-12 col-lg-12">
    <span id="customerimportresult">
      
    </span>
  </div>
</div>					
								

</form>
@include('Widgetmanager.manager')
@stack('plugin')
@stop




								

