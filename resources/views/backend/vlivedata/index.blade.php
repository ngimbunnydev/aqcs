@php
//dd($device_info);

if((null!==request()->get('location') && !empty(request()->get('location')))){
	$location_id  = request()->get('location');
	$filtered2 = array_filter( $device, function( $v ) use($location_id){ 
		return $v['location_id'] == $location_id; 
	} );
	$device_combo = [];
	foreach ($filtered2 as $item) {
		$device_combo[$item['device_id']] = $item['title'];
	}

}

@endphp
@extends('backend.layout')
@section('header_import')

@stop

@section('footer_import')

<script>

	@isset($device)
				var devices = {!!json_encode($device)!!};
	@endisset

	$("select[name='location']").change(function() {
			
			var combo = $("select[name='device']")
			
			combo.find('option:not(:first)').remove();
			var getDevices;
			if(this.value==-1 || this.value==''){
				getDevices = devices;
			}
			else{
				getDevices = devices.filter(item => (item.location_id == this.value));
			}
			

			getDevices.forEach(element => {
				combo.append($("<option></option>")
				.attr("value",element.device_id)
				.text(element.title)); 
			});
			
	});

	$("#btnreset").click(function() {
		location.href="{{url()->current()}}";
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
				
				<form action="" method="get" id="filter">

				    <!--/--><div class="row">
				    	<div class="form-row">

							<div class="form-group col-md-5">

								<label class="frm-label" for="title">@lang('label.lb09')</label>
								<select class="form-control" name="location" id="location">
									<option value="">-- {{__('ccms.ps')}} --</option>
									{!!cmb_listing($location,[request()->get('location') ?? ''],'','')!!} 					       
								</select>

						    </div>

						    <div class="form-group col-md-5">

								<label class="frm-label" for="title">@lang('label.lb16')</label>
								<select class="form-control" name="device" id="device">
									<option value="">-- {{__('ccms.ps')}} --</option>
									{!!cmb_listing($device_combo,[request()->get('device') ?? ''],'','')!!} 					       
								</select>

						    </div>

						   

						    <div class="form-group col-md-1">
						    	<label>&nbsp;</label>
						    	<button class="form-control btn btn-default" type="submit" value="filter">
                                    <i class="fa fa-search"></i>
                                </button>
						    </div>

						    <div class="form-group col-md-1">
						      <label>&nbsp;</label>

                               <button class="form-control btn btn-default" id="btnreset" type="button">
                                    @lang('label.reset')
                               </button>
						    </div>
						 </div>
				    <!--/--></div>

				</form>
			</div>
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<h3 class="header smaller lighter purple">
									@if(!empty($device_info))
										{{$device_info['location']}}
										<small>
											<i class="ace-icon fa fa-angle-double-right"></i>
											{{$device_info['device']}}
											({{$device_info['device_index']}})
										</small>
									@endif
										
									
								</h3>

								<div class="row">
									<div class="col-xs-12">

										<table id="dynamic-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<!-- <th class="center" style="width: 35px">
														<label class="pos-rel">
															<input type="checkbox" class="ace" />
															<span class="lbl"></span>
														</label>
													</th> -->
													

													<th>
														Data Name
													</th>

													<th>
														Real Value
													</th>

													<th width="150">
														State
													</th>

													<th width="160">
														Update Time
													</th>

													
													{{-- <th class="hidden-480">@lang('label.quality')</th> --}}
													
													

													
												</tr>
											</thead>

											<tbody>
												@foreach ($results as $row)
												@php
													$hili='';
													
													if((int)session('id')==(int)$row->id) $hili = "style='background-color: #ffffdd'";
												@endphp
												<tr {!!$hili!!}>
													

													<td>
														{{ $row->title }}
													</td>


													<td>
														{{$row->air_qty}}
													</td>

													<td>
													
														@if($row->air_qty<=$row->standard_qty || $row->standard_qty==0)
															Normal
														@else
															Unnormal
														@endif
													</td>

													<td>
														{{$row->record_datetime}}
													</td>


													
											

													
												</tr>
												 @endforeach
										</tbody>
									</table>

										
									</div>
								</div>
							</div>
		</div>

	<!--/. draw content -->

@stop


								

