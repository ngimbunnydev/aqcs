<div class="hidden-sm hidden-xs action-buttons">
								
								



																
																@isset($btnedit)
																<a class="blue" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'edit',$rowid],
										[]
									)}}" title="@lang('ccms.edit')">
																	<i class="ace-icon fa fa-pencil-alt bigger-130"></i>
																</a>
																@endisset

																
																@isset($btnduplicate)
																<a class="green" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'duplicate',$rowid],
										[]
									)}}" title="@lang('ccms.duplicate')">
																	<i class="ace-icon fa fa-clone bigger-130"></i>
																</a>
																@endisset


																@if(empty($trash))
																	
																	@isset($btndelete)
																	
																		@if(isset($delete_cfm) && $delete_cfm=='yes')
																	  	<a class="red bootbox-confirm" href="#" title="@lang('ccms.delete')" data-act='{{url_builder($obj_info['routing'],
											[$obj_info['name'],'delete',$rowid],
											[]
										)}}'>
																		<i class="ace-icon fa fa-trash bigger-130"></i>
																	</a>

																		@else
																			<a class="red" href="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'delete',$rowid],
											[]
										)}}" title="@lang('ccms.delete')">
																		<i class="ace-icon fa fa-trash bigger-130"></i>
																	</a>


																	 	@endif
																	@endisset

																@else

										
																	@isset($btnrestore)
																	<a class="orange" href="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'restore',$rowid],
											[]
										)}}" title="@lang('ccms.restore')">
																		<i class="ace-icon fa fa-recycle bigger-130"></i>
																	</a>
																	@endisset

																	
																	@isset($btndestroy)
																	
																		@if(isset($destroy_cfm) && $destroy_cfm=='yes')
																			<a class="red bootbox-confirm" href='#' data-act="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'destroy',$rowid],
											[]
										)}}" title="@lang('ccms.destroy')">
																		<i class="ace-icon fa fa-times-circle bigger-130"></i>
																	</a>

																		@else
																			<a class="red" href="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'destroy',$rowid],
											[]
										)}}" title="@lang('ccms.destroy')">
																		<i class="ace-icon fa fa-times-circle bigger-130"></i>
																	</a>

																		@endif
																	@endisset

																@endif
                          @isset($btnshow)
																<a class="blue" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'show',$rowid],
										[]
									)}}" title="@lang('ccms.show')">
																	<i class="ace-icon fas fa-eye bigger-130"></i>
																</a>
																@endisset
                          @isset($btnpdfview)
																<a class="red" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'pdfgenerate',$rowid],
										['option'=>1]
									)}}" title="@lang('ccms.pdf')" target="popup">
																	<i class="ace-icon fas fa-file-pdf bigger-130"></i>
																	
																</a>
								@endisset

								@isset($btnpdfdownload)
																<a class="green" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'pdfgenerate',$rowid],
										['option'=>2]
									)}}" title="@lang('ccms.pdf')" target="popup">
																	<i class="ace-icon fas fa-download bigger-130"></i>
																	
																</a>
								@endisset                  

															</div>

															<div class="hidden-md hidden-lg">
																<div class="inline pos-rel">
																	<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
																		<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
																	</button>

																	<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
						
																		
																		@isset($btnedit)
																		<li>
																			<a href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'edit',$rowid],
										[]
									)}}" class="tooltip-success" data-rel="tooltip" title="@lang('ccms.edit')">
																				<span class="blue">
																					<i class="ace-icon fa fa-pencil-alt bigger-120"></i>
																				</span>
																			</a>
																		</li>
																		@endisset

																		
																		@isset($btnduplicate)
																		<li>
																			<a href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'duplicate',$rowid],
										[]
									)}}" class="tooltip-success" data-rel="tooltip" title="@lang('ccms.duplicate')">
																				<span class="green">
																					<i class="ace-icon fa fa-clone bigger-120"></i>
																				</span>
																			</a>
																		</li>
																		@endisset

																		@if(empty($trash))
																			
																			@isset($btndelete)
																			<li>
																				<a href="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'delete',$rowid],
											[]
										)}}" class="tooltip-error" data-rel="tooltip" title="@lang('ccms.delete')">
																					<span class="red">
																						<i class="ace-icon fa fa-trash bigger-120"></i>
																					</span>
																				</a>
																			</li>
																			@endisset

																		@else
																			
																			@isset($btnrestore)
																			<li>
																				<a href="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'restore',$rowid],
											[]
										)}}" class="tooltip-error" data-rel="tooltip" title="@lang('ccms.restore')">
																					<span class="orange">
																						<i class="ace-icon fa fa-recycle bigger-120"></i>
																					</span>
																				</a>
																			</li>
																			@endisset


																			
																			@isset($btndestroy)
																			<li>
																				<a href="{{url_builder($obj_info['routing'],
											[$obj_info['name'],'destroy',$rowid],
											[]
										)}}" class="tooltip-error" data-rel="tooltip" title="@lang('ccms.destroy')">
																					<span class="red">
																						<i class="ace-icon fa fa-times-circle bigger-120"></i>
																					</span>
																				</a>
																			</li>
																			@endisset
																			
																		@endif
                                  @isset($btnshow)
																		<li>
																			<a href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'show',$rowid],
										[]
									)}}" class="tooltip-success" data-rel="tooltip" title="@lang('ccms.show')">
																				<span class="blue">
																					<i class="ace-icon fas fa-eye bigger-120"></i>
																				</span>
																			</a>
																		</li>
																		@endisset
                                    @isset($btnpdfview)
																			<li>
																				<a class="red" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'pdfgenerate',$rowid],
										['option'=>1]
									)}}" title="@lang('ccms.pdf')" target="popup">
																	<i class="ace-icon fas fa-file-pdf bigger-130"></i>
																	
																</a>
																			</li>

																		@endisset

																		@isset($btnpdfdownload)
																			<li>
																				<a class="green" href="{{url_builder($obj_info['routing'],
										[$obj_info['name'],'pdfgenerate',$rowid],
										['option'=>2]
									)}}" title="@lang('ccms.pdf')" target="popup">
																	<i class="ace-icon fas fa-download bigger-130"></i>
																	
																</a>
																			</li>

																		@endisset

																	</ul>
																</div>
															</div>