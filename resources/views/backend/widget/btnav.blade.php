<div class="wizard-actions">

		@if($btnnew=='yes')
		<button class="btn btn-white btn-success btn-bold btn-sm btnact_w" onclick="actNew('{{url_builder($obj_info['routing'],[$obj_info['name'],'create'])}}')">
			<i class="ace-icon fa fa-plus bigger-120 green"></i><br>
			@lang('ccms.new')
		</button>
		@endif

		<!--<button class="btn btn-white btn-primary btn-bold btn-sm btnact_w">
			<i class="ace-icon fa fa-pencil-square-o bigger-120 blue"></i><br>
			@lang('ccms.edit')
		</button>-->

		<!--<button class="btn btn-white btn-warning btn-bold btn-sm btnact_w">
			<i class="ace-icon fa fa-clone bigger-120 yellow"></i><br>
			@lang('ccms.duplicate')
		</button>-->

		<!--<button class="btn btn-white btn-danger btn-bold btn-sm btnact_w">
			<i class="ace-icon fa fa-minus-circle   bigger-120 red"></i><br>
			@lang('ccms.delete')
		</button>-->
		@if(empty($trash))
			@if($btntrash=='yes')
			<button class="btn btn-white btn-default btn-bold btn-sm btnact_w pding-lr-0" onclick="actNew('{{url_builder($obj_info['routing'],[$obj_info['name'],'trash'])}}')">
				<i class="ace-icon fa fa-trash bigger-120"></i><br>
				@lang('ccms.bin')
			</button>
			@endif
		@else
			@if($btnactive=='yes')
			<button class="btn btn-white btn-info btn-bold btn-sm btnact_w pding-lr-0" onclick="actNew('{{url_builder($obj_info['routing'],[$obj_info['name'],'index'])}}')">
				<i class="ace-icon fa fa-home bigger-120"></i><br>
				@lang('ccms.active')
			</button>
			@endif
		@endif
  
  @if(isset($btnimport) && $btnimport=='yes')
    <button class="btn btn-white btn-success btn-bold btn-sm btnact_w" onclick="airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'{{ $obj_info['name'] }}', ajaxact:'airimport', ajaxnext : 'ajaxreturn'},'','Test',true);">
          <i class="ace-icon fa fa-file-import bigger-120 green"></i><br>
          @lang('ccms.import')
    </button>

  @endif

		
	</div>