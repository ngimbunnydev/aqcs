
@php
//dd($main_data);
@endphp
	<!--DRAW Content -->
    <div class="row">
				
			
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								<div class="row">
									<div class="col-xs-12">
										<table id="dynamic-table" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th width="50">@lang('label.no')</th>
														<th >@lang('label.lb09')</th>
														<th width="100">
															@lang('label.lb16')
														</th>
                            
                            							<th width="130">
															@lang('label.datetime')
														</th>
														
														@foreach ($main_data as $key => $value)
														<th>
															{{$value}}
														</th>
														@endforeach
                            
														
														
													</tr>
												</thead>

												<tbody>
                          @if($results->count() >0)
                          @php $runRow = 1; @endphp
													@foreach ($results as $row)
													@php
														$hili='';
														
														//if((int)session('id')==(int)$row->id) $hili = "style='background-color: #ffffdd'";
													@endphp
													<tr {!!$hili!!}>
														<td class="center">
															{{ $runRow }}
														</td>

														<td>
															{{$devices[$row['B']]['location']}}
														</td>

														<td>
															{{ $row['B'] ?? '' }}
														</td>
										
                            							<td>
															{{gmdate("d-m-Y H:i", ExcelDateToUnix($row['C']))}}
																
														</td>

														@foreach ($main_data as $key => $value)
														<td>
															{{$row[$key]??''}}
																
														</td>
														@endforeach
                            
                           
                            
        

													</tr>
                            @php $runRow++; @endphp
													 @endforeach
                          @else
                            <tr>
                               <td colspan="7" class="red"><strong>@lang('ccms.noresult')</strong></td>
                            </tr>
                          @endif
											</tbody>
										</table>
										
										
									</div>
								</div>
							</div>
		</div>

	<!--/. draw content -->
		



								

