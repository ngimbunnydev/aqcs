<div class="navbar-buttons navbar-header pull-right" role="navigation">
        
					<ul class="nav ace-nav">

						        @php
                                		
		                    $branchall = $branchlisting;
		                       		
		                @endphp
						
						@if(!isset($mobile))
            
           
           
            
            
						<li class="green dropdown-modal">

						
							<a data-toggle="dropdown" href="#" class="dropdown-toggle" alt="{{__('ccms.branch')}}">
								
									<i class="ace-icon fa fa-building"></i>
									@if($userinfo->branch_id==0)
										<span class="red">{{__('ccms.everybranch')}}</span>
									@else
										
									{{$branchall[$userinfo->branch_id]}}
									@endif


									@if(count($branchall)>0 && $userinfo->level_id==1)
									<i class="ace-icon fa fa-caret-down"></i>
									@endif

								
							</a>
							@if(count($branchall)>0 && $userinfo->level_id==1)
							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								
								@php
									$branchall = array_except($branchall, [$userinfo->branch_id]);
									$branchall = (object)($branchall);
								@endphp
								@foreach($branchall as $ind => $r)
								<li>
									<a href="{{ url_builder('admin.controller',['branch', 'change',$ind]) }}">
										<i class="ace-icon fa fa-building"></i>
										{{$r}}
									</a>
								</li>
                                @endforeach

                                @if($userinfo->branch_id!=0)
                                <li>
									<a class="red" href="{{ url_builder('admin.controller',['branch', 'change',0]) }}">
										<i class="ace-icon fa fa-building"></i>
										{{__('ccms.everybranch')}}
									</a>
								</li>
								@endif
								
							</ul>
							@endif
						</li> <!--end menu-->


						
						
						@endif

						<li class="light-blue dropdown-modal">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								
                                <img class="nav-user-photo" alt="System user" src="{{ URL::asset('/resources/assets/arcetheme/images/avatars/avatar2.png') }}">
								<span class="user-info">
									<small>@lang('ccms.welcome'),</small>
									{{$userinfo->name}}
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								
								@if(isset($mobile))
								<li style="padding-left: 10px; font-size: 13px; line-height: 30px">
									<i class="ace-icon fa fa-building"></i>
									@if($userinfo->branch_id==0)
										<span class="red">{{__('ccms.everybranch')}}</span>
									@else
										
									{{$branchall[$userinfo->branch_id]}}
									@endif
								</li>

								<li style="padding-left: 10px; font-size: 13px; line-height: 20px">
									<i class="ace-icon fa fa-warehouse"></i>
									@if($userinfo->wh_id==0 || count($warehouseall)==0)
										<span class="red"> {{__('ccms.everywarehouse')}}</span>
									@else	
										{{$warehouseall[$userinfo->wh_id]}}
									@endif
								</li>


								@endif

                                <li>
									<a href="#" onclick="airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'resetpwd', ajaxact:'edit', ajaxid:'{{$userinfo->id}}'},'frmname','Test',true)">
										<i class="ace-icon fa fa-key"></i>
										@lang('ccms.resetpwd')
									</a>
								</li>

								<li class="divider"></li>
								@include('backend.widget.lang')	
								<li>
									<a href="{{ url_builder('admin.controller',['logout']) }}">
										<i class="ace-icon fa fa-power-off"></i>
										@lang('ccms.logout')
									</a>
								</li>
							</ul>
						</li> <!--end menu-->
					</ul>
				</div>