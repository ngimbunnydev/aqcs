<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#product">
															<i class="ace-icon fa fa-angle-up bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-up"></i>
															&nbsp;Product

														</a>


													</h4>
												</div>

												<div class="panel-collapse collapse" id="product">
													<div class="panel-body pding-5">
														@php
															use App\Models\Backend\Product;
															$product = new Product;
															$productlist = $product->select(\DB::raw("pd_id AS id, 
					                                                    JSON_UNQUOTE(title->'$.".$dflang[0]."') AS title"
					                                                )
					                                        )
					                                        ->where('trash', '<>', 'yes')
					                                        ->orderby('pd_id', 'asc')
					                                        ->offset(0)
                											->limit(10)->get()->toArray();
                									
														@endphp

														

														<!-- strat tab-->
															<div class="row">
																		<div class="col-xs-12">
																			
																			

																			    <div class="input-group">
																					<input type="text" class="form-control search-query" placeholder="Type your query">
																					<span class="input-group-btn">
																						<button type="button" class="btn btn-white btn-primary btnsearch" data-obj="product" data-modelid='pd_id'>
																							<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
																							Search
																						</button>
																					</span>
																				</div>

																			
																		</div>
															</div>
															<hr style="margin: 5px 0px">

															<span id="product-menu">
																@php
																	$productrecent=checkbox_select('product[]',$productlist,[],'');
																@endphp
																{!!$productrecent!!}
															</span>

															<span class="hide" id="product-menu-temp">
																{!!$productrecent!!}
															</span>
																	

														<!-- stop tab -->


														<hr style="margin: 3px 0px">

														<button class="btn btn-minier" id='resetdata' data-temp='product-menu-temp' data-show='product-menu'>Reset</button>
												
														
														<button style="float: right;" type="button" class="btn btn-white btn-primary addtomenu" data-obj="product" data-modelid='pd_id' data-ele='product'>Add</button>
													</div>
												</div>
						</div>