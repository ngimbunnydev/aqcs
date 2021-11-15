<!-- A BLOCK of Element  --><div class="row" style="padding:0px 5px 5px 5px;">


	<div class="col-xs-12 col-sm-12 col-lg-12" style="float:left; padding:2px
	 0px 2px 0px; margin:0px 0px 0px 0px; text-align: center;">

	    <input type="button" name="browsefilemanager" id="browsefilemanager" value="@lang('label.browsemedia')..."  class="en label_b 
	btn btn-default btn-sm" />

	</div>							                
											                
											                
											                
	<!-- Files container --><div class="col-xs-12 col-sm-12 col-lg-12" style="margin:1px 0px 0px 0px; padding:2px 0px 2px 0px; border-top:1px solid #ddd">

		<span >
		    <div id="temp_f" style='float:left; display:none'></div>
		    <!-- this dive i can not delete any way -->

		</span>

		<ul id="file_container" class="ace-thumbnails clearfix" >

		</ul>

	<!-- End Files container --></div>										                
										                									              							                
<!--end A BLOCK of Element --></div>

<!--================================================================================================-->
@if(empty($allcolors))
	@php $allcolors=[];@endphp
@endif

@push('plugin')
    @include('Filemanager.manager')
	
@endpush

@push('plugin')
   @include('Filemanager.setting',$allcolors)
@endpush




<!--Please write {{--@stack('plugin')@stop--}} where you need to get this include/content @ other file/block-->