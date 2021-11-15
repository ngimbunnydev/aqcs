<div class="row">
												<div class="col-xs-12 col-sm-6 col-lg-6" style="margin-bottom:10px">
													<div class="dataTables_info" id="dynamic-table_info" role="status" aria-live="polite">
														
														@lang('label.display') #
															<select class="input-sm" name="perpage" onchange='submitPerpage("{{$sort}}","{{$order}}",{{json_encode($querystr )}},this.value)'>
																 {!!cmb_listing(config('ccms.perpage'),[$recordinfo['perpage']],'','')!!}   
															</select>

															{{$recordinfo['from']}} 
															- 
															{{$recordinfo['to']}} 
															@lang('label.of') 
															{{$recordinfo['total']}}  @lang('label.record')
												

														
													</div>
												</div>
												<div class="col-xs-12 col-sm-6 col-lg-6">
													<div class="dataTables_paginate paging_simple_numbers">

														{{ $paginationlinks }}
													</div>
												</div>
	</div> <!--/.row-->