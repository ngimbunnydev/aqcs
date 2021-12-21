@php
  /*if(dbis('pfkdemo')){
  foreach($userinfo->levelsetting as $object){
    $object = explode('-', $object);
    ${'showhide_'.$object[0]} = '';
  }
} */
@endphp


			<div id="sidebar" class="sidebar responsive ace-save-state">
				<script type="text/javascript">
					try{ace.settings.loadState('sidebar')}catch(e){}
				</script>
          
					<ul class="nav nav-list">
					<li class="{{nav_checkactive(['home'],$obj_info['name'])}}">
						<a href="{{ url_builder('admin.controller',['home']) }}">
							
							<i class="menu-icon"><i class="fas fa-tachometer-alt"></i></i>
							<span class="menu-text"> @lang('label.lb08') </span>
						</a>

						<b class="arrow"></b>
					</li>


					<ul class="nav nav-list">
            
					
            
         
          <!--     Location        -->
            <li class="showhidemenu {{nav_checkactive(['location'],$obj_info['name'])}}">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon" style="color: #000099"><i class="fas fa-map-marked-alt"></i></i>
							<span class="menu-text"> @lang('label.lb09') </span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li class="">
								
								<a href="{{ url_builder('admin.controller',['location']) }}">
									<i class="menu-icon pink"><i class="fa fa-eye"></i></i>
									@lang('label.view')
								</a>

								<b class="arrow"></b>
							</li>

							<li class="">
								<a href="{{ url_builder('admin.controller',['location','create']) }}">
									<i class="menu-icon purple"><i class="fa fa-plus"></i></i>
									@lang('label.create')
								</a>

								<b class="arrow"></b>
							</li>


						</ul>
					</li>

			<!--     Devide        -->
            <li class="showhidemenu {{nav_checkactive(['device'],$obj_info['name'])}}">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon" style="color: #993d00"><i class="fas fa-hdd"></i></i>
					<span class="menu-text"> @lang('label.lb16') </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<li class="">
						
						<a href="{{ url_builder('admin.controller',['device']) }}">
							<i class="menu-icon pink"><i class="fa fa-eye"></i></i>
							@lang('label.view')
						</a>

						<b class="arrow"></b>
					</li>

					<li class="">
						<a href="{{ url_builder('admin.controller',['device','create']) }}">
							<i class="menu-icon purple"><i class="fa fa-plus"></i></i>
							@lang('label.create')
						</a>

						<b class="arrow"></b>
					</li>


				</ul>
			</li>

			<!--     Air Type        -->
            <li class="showhidemenu {{nav_checkactive(['airtype'],$obj_info['name'])}}">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon" style="color: #009978"><i class="fas fa-fan"></i></i>
					<span class="menu-text"> @lang('label.lb17') </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<li class="">
						
						<a href="{{ url_builder('admin.controller',['airtype']) }}">
							<i class="menu-icon pink"><i class="fa fa-eye"></i></i>
							@lang('label.view')
						</a>

						<b class="arrow"></b>
					</li>

					<li class="">
						<a href="{{ url_builder('admin.controller',['airtype','create']) }}">
							<i class="menu-icon purple"><i class="fa fa-plus"></i></i>
							@lang('label.create')
						</a>

						<b class="arrow"></b>
					</li>


				</ul>
			</li>

			<!--     Benchmark       -->
            <li class="showhidemenu {{nav_checkactive(['benchmark'],$obj_info['name'])}}">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon" style="color: #000099"><i class="fas fa-book-open"></i></i>
					<span class="menu-text"> @lang('label.lb18') </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<li class="">
						
						<a href="{{ url_builder('admin.controller',['benchmark']) }}">
							<i class="menu-icon pink"><i class="fa fa-eye"></i></i>
							@lang('label.view')
						</a>

						<b class="arrow"></b>
					</li>

					<li class="">
						<a href="{{ url_builder('admin.controller',['benchmark','create']) }}">
							<i class="menu-icon purple"><i class="fa fa-plus"></i></i>
							@lang('label.create')
						</a>

						<b class="arrow"></b>
					</li>


				</ul>
			</li>


			<!--     Air Quality        -->
            <li class="showhidemenu {{nav_checkactive(['airqualitymonitoring'],$obj_info['name'])}}">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon" style="color: #009921"><i class="fas fa-clipboard-list"></i></i>
					<span class="menu-text"> @lang('label.lb19') </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<li class="">
						
						<a href="{{ url_builder('admin.controller',['airqualitymonitoring']) }}">
							<i class="menu-icon pink"><i class="fa fa-eye"></i></i>
							@lang('label.view')
						</a>

						<b class="arrow"></b>
					</li>

					<li class="">
						<a href="{{ url_builder('admin.controller',['airqualitymonitoring','create']) }}">
							<i class="menu-icon purple"><i class="fa fa-plus"></i></i>
							@lang('label.create')
						</a>

						<b class="arrow"></b>
					</li>


				</ul>
			</li>

			{{-- Report --}}
			<li class="{{nav_checkactive(['livedata','reportdatetime', 'reportlocation','reportbranch'],$obj_info['name'],'open')}}">
				<a href="#" class="dropdown-toggle">
					<i class="menu-icon"><i class="fa fa-cog"></i></i>
					<span class="menu-text">
						@lang('label.report')
					</span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					

					


					<li class="showhidemenu {{nav_checkactive(['livedata'],$obj_info['name'])}}">
						<a href="{{ url_builder('admin.controller',['livedata']) }}">
							<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
							@lang('label.livedata')
						</a>

						<b class="arrow"></b>
					</li>

					<li class="showhidemenu {{nav_checkactive(['reportdatetime'],$obj_info['name'])}}">
						<a href="{{ url_builder('admin.controller',['reportdatetime']) }}">
							<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
							Multi Air Type
						</a>

						<b class="arrow"></b>
					</li>


					<li class="showhidemenu {{nav_checkactive(['reportlocation'],$obj_info['name'])}}">
						<a href="{{ url_builder('admin.controller',['reportlocation']) }}">
							<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
							Air Type
						</a>

						<b class="arrow"></b>
					</li>

					<li class="showhidemenu {{nav_checkactive(['reportbranch'],$obj_info['name'])}}">
						<a href="{{ url_builder('admin.controller',['reportbranch']) }}">
							<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
							Province/Country
						</a>

						<b class="arrow"></b>
					</li>

					
	   
				   
				</ul>
			</li>
			{{-- End report --}}
            
                    
                    

					<li class="{{nav_checkactive(['color','evaluation', 'branch', 'systemconfig', 'general'],$obj_info['name'],'open')}}">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon"><i class="fa fa-cog"></i></i>
							<span class="menu-text">
								@lang('label.lb10')
							</span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">

							

							


							<li class="showhidemenu {{nav_checkactive(['branch'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['branch']) }}">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									@lang('label.lb13')
								</a>

								<b class="arrow"></b>
							</li>

							<li class="showhidemenu {{nav_checkactive(['evaluation'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['evaluation']) }}">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									@lang('label.lb23')
								</a>

								<b class="arrow"></b>
							</li>

							<li class="showhidemenu {{nav_checkactive(['color'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['color']) }}">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									@lang('label.lb24')
								</a>

								<b class="arrow"></b>
							</li>


              
               
                           
						</ul>
					</li>



					<li class="{{nav_checkactive(['user','userlevel'],$obj_info['name'], 'open')}}">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon"><i class="fas fa-user"></i></i>
							<span class="menu-text"> @lang('label.lb11')</span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">
							<li class="showhidemenu {{nav_checkactive(['user'],$obj_info['name'])}}">
								
								<a href="{{ url_builder('admin.controller',['user']) }}">
									<i class="menu-icon pink"><i class="fa fa-eye"></i></i>
									@lang('label.view')
								</a>

								<b class="arrow"></b>
							</li>

							
							<li class="showhidemenu {{nav_checkactive(['user'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['user','create']) }}">
									<i class="menu-icon purple"><i class="fa fa-plus"></i></i>
									@lang('label.create')
								</a>

								<b class="arrow"></b>
							</li>

							<li class="showhidemenu {{nav_checkactive(['userlevel'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['userlevel']) }}">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									@lang('label.lb12')
								</a>

								<b class="arrow"></b>
							</li>


						</ul>
					</li>




					<!-- <li class="{{nav_checkactive(['userlevel'],$obj_info['name'],'open')}}">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon"><i class="fa fa-user"></i></i>
							<span class="menu-text">
								User Setting
							</span>

							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>

						<ul class="submenu">

							
							<li class="">
								<a href="#" onclick="airWindows('air_windows', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'resetpwd', ajaxact:'edit', ajaxid:'{{$userinfo->id}}'},'frmname','Test',true)">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									@lang('ccms.resetpwd')
								</a>

								<b class="arrow"></b>
							</li>

							<li class="{{nav_checkactive(['user'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['user']) }}">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									User
								</a>

								<b class="arrow"></b>
							</li>


							<li class="{{nav_checkactive(['userlevel'],$obj_info['name'])}}">
								<a href="{{ url_builder('admin.controller',['userlevel']) }}">
									<i class="menu-icon"><i class="fa fa-caret-right"></i></i>
									User Permission
								</a>

								<b class="arrow"></b>
							</li>

						</ul>
					</li> -->

					

				
				</ul><!-- /.nav-list -->

				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
					<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>
			</div>