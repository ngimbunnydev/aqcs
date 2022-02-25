@extends('backend.layout')
@section('header_import')

@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/spinbox.min.js')}}"></script>





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

			$('.level_id').click(function() {
				
				var id = $(this).data('id');
				airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:"userlevel", ajaxact:'edit', level_id: id, ajaxnext : 'afteredit'},'','title',true);

			});

			$('.userstatus').click(function() {
				$status = $(this).val();
				if($status=='yes')
				{
					$(this).val('no');
				}
				else
				{
					$(this).val('yes');
				}
				
				id = $(this).attr('id');
				var $datainfo={'field': 'userstatus','id':id, 'newdata':$(this).val(), type:'', datavalidate:''};
		    	$datainfo = JSON.stringify($datainfo);

				airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:"{{$obj_info['name']}}", ajaxact:'edit_field', datainfo: $datainfo},'','title',false);

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
				@include('backend.widget.btnav', ['btnnew' => 'yes', 'btntrash' => 'yes', 'btnactive' => 'yes'])			
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->

	<!--DRAW Content -->
	@php
		$querytitle=url_builder($obj_info['routing'],[$obj_info['name'],'index'],array_merge(['sort=title'], $querystr));
	@endphp
		<div class="row">
				<form action="" method="get" id="filter">
				    <!--/-->
				    	<div class="form-row">
						    <div class="form-group col-md-6">

						      <label for="title">@lang('label.search')</label>
						      <input type="text" class="form-control" id="title" name="title" value="{{request()->get('title')}}">
						    


						    </div>

						    <div class="form-group col-md-4">
						      <label for="ct_id">@lang('label.lb110')</label>
						      <select id="ct_id" name="ct_id" class="form-control">
						        <option value="">-- {{__('ccms.ps')}} --</option>
						        {!!cmb_listing($ctype,[request()->get('ct_id') ?? ''],'','')!!} 
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

                               <button class="form-control btn btn-default" type="button" onclick="location.href='{{url()->current()}}'">
                                    @lang('label.reset')
                               </button>
						    </div>
						 </div>
				    <!--/-->

				</form>
			
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
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
														<th width="55">
															{!!
																orderMenu(
																[	'caption'=>__('label.id'),
																	'sort'=>'pd_id', 
																	'current_sort'=>$sort, 
																	'mdefault'=>'asc', 
																	'method'=>$order, 
																	'act'=>$act
																],
																$querystr,
																$perpage_query, 
																$obj_info)
															!!}															
														</th>
														<th>
															{!!
																orderMenu(
																[	'caption'=>__('label.lb109'),
																	'sort'=>'latinname', 
																	'current_sort'=>$sort, 
																	'mdefault'=>'asc', 
																	'method'=>$order, 
																	'act'=>$act
																],
																$querystr,
																$perpage_query, 
																$obj_info)
															!!}
														</th>
														<th>
															@lang('label.lb114')
														</th>

														<th>
															@lang('label.lb13')
														</th>
														
														
														<th>
															@lang('label.lb110')
														</th>

														
														<th width="100" class="hidden-480">@lang('label.status')</th>

														<th width="100" class="width-480"></th>
													</tr>
												</thead>

												<tbody>
													@foreach ($results as $row)
													@php
														$hili='';
														
														if((int)session('id')==(int)$row->id) $hili = "style='background-color: #ffffdd'";
													@endphp
													<tr {!!$hili!!}>
														<!-- <td class="center">
															<label class="pos-rel">
																<input type="checkbox" class="ace" />
																<span class="lbl"></span>
															</label>
														</td> -->

														<td>
															{{ $row->id }}
														</td>

														<td class="green">
															<span class="badge badge-info new">
															<a href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'edit',$row->id],
										[]
									)}}">
																{{ $row->name }}
															</a>
														</span>
														</td>

														<td>
															
																{{ $row->nativename }}
																	/

																	{{ $row->latinname }}
															
															
														</td>

														<td class="green">
															{{ $branchlisting[$row->branch_id]??'' }}
														</td>

													
														
														<td class="blue">
															
															<a class="level_id lavelname-{{$row->level_id}}" href="#" data-id="{{$row->level_id??''}}">
																{{ $ctype[$row->level_id]??'' }}
															</a>
														</td>
														

														<td class="hidden-480">
															<label>
																@php
																	$checked = '';
																	if($row->userstatus=='yes')
																	{
																		$checked = 'checked';
																	}
																@endphp
																<input name="switch-field-1" class="ace ace-switch ace-switch-4 btn-rotate userstatus" type="checkbox" id="{{$row->id}}" value="{{$row->userstatus??''}}" {{$checked}}>
																<span class="lbl"></span>
															</label>
														</td>

														<td>
															@include('backend.widget.actmenu',['rowid'=>$row->id ,
															'btnedit' => 'yes', 
															
															'btndelete' => 'yes',
															'btnrestore' => 'yes',
															'btndestroy' => 'yes'
															])
														</td>
													</tr>
													 @endforeach
											</tbody>
										</table>
										
										<!-- Pagination and Record info -->
											@include('backend.widget.pagination')

										<!-- /. end -->
									</div>
								</div>
							</div>
		</div>

	<!--/. draw content -->

@stop


								

