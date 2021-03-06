@extends('backend.layout')
@section('header_import')

@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>

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
				
				<form action="" method="get" id="filter">

				    <!--/--><div class="row">
				    	<div class="form-row">
						    <div class="form-group col-md-10">

						      <label for="title">@lang('label.search')</label>
						      <input type="text" class="form-control" id="title" name="title" value="{{request()->get('title')}}">
						    


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
				    <!--/--></div>

				</form>
			</div>
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
														<th width="50">
															{!!
																orderMenu(
																[	'caption'=>__('label.id'),
																	'sort'=>'cl_id', 
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
																[	'caption'=>__('label.title'),
																	'sort'=>'title', 
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

														<th class="hidden-480">@lang('label.code')</th>

														<th class="hidden-480">@lang('label.pic')</th>
														
														

														<th width="80" class="width-480"></th>
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

														<td>
															<a href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'edit',$row->id],
										[]
									)}}">{{ $row->title }}</a>
														</td>

														<td style="color:{{$row->code??'#000'}}">
															
																{{ $row->code??''}}
														</td>


														<td>
															@if(!empty($row->pic))
																<img height="50" alt="{{$row->pic}}" 
      src="{{ URL::asset('/resources/filelibrary/_150/'.$row->pic) }}" />
															@endif
														</td>


														
												

														<td>
															@include('backend.widget.actmenu',['rowid'=>$row->id ,'btnedit' => 'yes', 'btndelete' => 'yes','btnrestore' => 'yes','btndestroy' => 'yes','delete_cfm' => 'yes'])
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


								

