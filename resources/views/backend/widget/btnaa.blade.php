<div class="wizard-actions" id="formactionbutton">
	@if (!session('ajax_access'))
  
    @if(isset($btnhold) && $btnhold=='yes')
		<button type="button" class="btn btn-white btn-warning btn-bold btn-sm btnact_w" onclick="">
			<i class="ace-icon fa fa-save bigger-120 yellow"></i><br>
			@lang('label.lb03')
		</button>
		@endif

		@if(isset($btnsaveoption) && count($btnsaveoption)>0)
			<div class="btn-group">
												<button data-toggle="dropdown" class="btn btn-white btn-default btn-bold btn-sm btnact_w" aria-expanded="false" style="height: 45px">
													<i class="ace-icon fa fa-save bigger-120 blue"></i>
													<i class="ace-icon fa fa-angle-down icon-on-right"></i>
													<br>
			@lang('ccms.save')
												</button>

												<ul class="dropdown-menu">
													@foreach($btnsaveoption as $key => $val)
														<li>
															<a href="#" onclick="actSave('frmadd-{{$obj_info['name']}}','save.{{$key}}');">{{$val}}</a>
														</li>
													@endforeach
												</ul>
											</div>
		
		@endif


		
		

		@if(isset($btnsave) && $btnsave=='yes')
		<button type="button" class="btn btn-white btn-default btn-bold btn-sm btnact_w" onclick="actSave('frmadd-{{$obj_info['name']}}','save');">
			<i class="ace-icon fa fa-save bigger-120 blue"></i><br>
			@lang('ccms.save')
		</button>
		@endif

		@if(isset($btnnew) && $btnnew=='yes')
		<button type="button" class="btn btn-white btn-primary btn-bold btn-sm btnact_w" onclick="actSave('frmadd-{{$obj_info['name']}}','new');">
			<i class="ace-icon fa fa-save bigger-120 blue"></i><br>
			@lang('ccms.save')<sup class="red">*</sup>
		</button>
		@endif
  
  @if(isset($btntreat) && $btntreat=='yes')
		<button type="button" class="btn btn-white btn-primary btn-bold btn-sm btnact_w" onclick="actSave('frmadd-{{$obj_info['name']}}','treat');">
			<i class="ace-icon fa fa-save bigger-120 blue"></i><br>
			@lang('ccms.save')<i class="ace-icon fa fa-stethoscope red"></i>
		</button>
		@endif

		@if(isset($btnapply) && $btnapply=='yes')
		<button type="button" class="btn btn-white btn-success btn-bold btn-sm btnact_w" onclick="actSave('frmadd-{{$obj_info['name']}}','apply');">
			<i class="ace-icon fa fa-check bigger-120 green"></i><br>
			@lang('ccms.apply')
		</button>
		@endif

		@if(isset($btnpreview) && $btnpreview=='yes')
		<button type="button" class="btn btn-white btn-success btn-bold btn-sm" id="btnpreview">
			<i class="ace-icon fa fa-eye bigger-120 green"></i><br>
			@lang('ccms.preview')
		</button>
		@endif
		

		@if(isset($btncancel) && $btncancel=='yes')
		<button type="button" name="btncancel" id="btncancel" class="btn btn-white btn-danger btn-bold btn-sm btnact_w" onclick="actCancel('{{ URL::previous() }}','{{ url_builder($obj_info['routing'],[$obj_info['name']]) }}')">
			<i class="ace-icon fa fa-times bigger-120 red"></i><br>
			@lang('ccms.cancel')
		</button>
		@endif

	@else
		@if(isset($btnsave) && $btnsave=='yes')
		<button type="button" class="btn btn-white btn-default btn-bold btn-sm btnact_w" onclick="airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'{{$obj_info['name']}}', ajaxact:'{{$submitto}}'},'frmadd-{{$obj_info['name']}}','Test',false); $(this).attr('disabled', true)">
			<i class="ace-icon fa fa-save bigger-120 blue"></i><br>
			@lang('ccms.save')
		</button>
		@endif

	@endif
  
  @if(isset($btnimport) && $btnimport=='yes')
		<button type="button" class="btn btn-white btn-default btn-bold btn-sm" id="btnimport" onclick="airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'{{$obj_info['name']}}', ajaxact:'{{$submitto}}', ajaxnext: 'ajaxloadingimportdata'},'frmimport-{{$obj_info['name']}}','Test',false);">
			<i class="ace-icon fa fa-save bigger-120 blue"></i><br>
			@lang('ccms.save')
		</button>
		@endif

	</div>
