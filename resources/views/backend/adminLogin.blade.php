@extends('backend.index')
@section('header_import')
	
@stop

@section('footer_import')

	<script>
		$( document ).ready(function() {
		  	//$( "#username" ).focus();
		  	var num = $('#username').val();        
			$('#username').focus().val('').val(num);

		});
		//protexlogin()


		$( "#newcolor" ).click(function() {
			airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'color', ajaxact:'create', ajaxnext : 'ajaxreturn'},'','Test',true);
		});


	</script>

@stop	

@section('content')
<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							
							
							<div class="space-30"></div>
							<div style="display: flex; justify-content: center; align-items: center; padding: 10px 0px">
								<img src="{{ URL::asset('/resources/filelibrary/aqcs_logo.png') }}" width="100%"/>
							</div>

							<div class="position-relative" >
								<div id="login-box" class="login-box visible widget-box">
									<div class="widget-body lgin-bg">
										<div class="widget-main lgin-pding">

											 <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.auth') }}">
												{{ csrf_field() }}
												<fieldset>

													<div class="form-group">
														@if (session('status'))
														    <div class="alert alert-success">
														        {{ session('status') }}
														    </div>
														@endif

														@if(isset($lgerr))
															<div class="alert alert-danger pding-10">
															<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
															{{$lgerr}}
															</div>
														@else
															<h5 class="blue lighter">
															@lang('ccms.logintitle')
															</h5>
														@endif
													</div>
													<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
														<label class="block clearfix">
															<span class="block input-icon input-icon-right">
																<input type="text" name="username" id="username" class="form-control" placeholder="@lang('ccms.username')" value="{{$username ?? ''}}" />
																<i class="ace-icon fa fa-user"></i>
															</span>
														</label>
														{{-- comment
															@if ($errors->has('username'))
							                                    <span class="help-block">
							                                        <strong>{{ $errors->first('username') }}</strong>
							                                    </span>
						                                	@endif 
						                                --}}
													</div>
													<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
														<label class="block clearfix">
															<span class="block input-icon input-icon-right">
																<input type="password" name="password" class="form-control" placeholder="@lang('ccms.password')" />
																<i class="ace-icon fa fa-lock"></i>
															</span>
														</label>
													</div>

													<div class="space"></div>
													<div class="form-group">
														<div class="clearfix">
															<label class="inline">
																<input type="checkbox" class="ace-old" name="remember" id="remember" />
																<span class="lbl"> @lang('ccms.rememberme')</span>
															</label>

															<button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
																<i class="ace-icon fa fa-key"></i>
																<span class="bigger-110">@lang('ccms.login')</span>
															</button>

														</div>
													</div>

													<div class="space-1"></div>
												</fieldset>
											</form>

											
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix">
											<div>
												<a href="{{ url_builder('admin.controller',['forget']) }}" data-target="#forgot-box" class="forgot-password-link">
													<i class="ace-icon fa fa-arrow-left"></i>
													@lang('ccms.forgotpwd')
												</a>
											</div>

											<div>
												
													<i class="ace-icon fa fa-copyright white"></i>
													<span class="white">&nbsp;&nbsp;&nbsp;</span>
													
												
											</div>


										</div>


									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->

							
							</div><!-- /.position-relative -->

							<div class="navbar-fixed-top align-right">
								<br />
								
											
											
													
											<div class="btn-group">
												<button data-toggle="dropdown" class="btn btn-sm btn-primary btn-white dropdown-toggle" aria-expanded="false">

													@if(null!==Session::get('lang'))
														{{ __('ccms.'.Session::get('lang')) }}
													@else
														Language
													@endif

													<i class="ace-icon fa fa-angle-down icon-on-right"></i>
												</button>


												<ul class="dropdown-menu dropdown-primary dropdown-menu-right">
													@include('backend.widget.lang')	

												</ul>
											</div><!-- /.btn-group -->
										
								&nbsp;
							</div>


							
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->


      

        
@stop


								

