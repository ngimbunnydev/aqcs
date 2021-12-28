@php
//$userinfo = Auth::guard('admin')->user();
@endphp
<div id="navbar" class="navbar navbar-default          ace-save-state">
			<div class="navbar-container ace-save-state" id="navbar-container">
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>

					<span class="icon-bar"></span>
				</button>

				<div class="navbar-header pull-left">
          <img src="{{ URL::asset('/resources/filelibrary/aqcs_logo.png') }}" height="50px" style="padding: 5px"/>
					<a href="{{url_builder($obj_info['routing'],['home',''])}}" class="navbar-brand">
					  Air Quality Monitoring'
            <sub>System</sub>
						<small>
							<i class="fa fa-leaf"></i>
						</small>

					</a>
          
          <span class="navbar-brand yellow">
            <span class="ajaxloading" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></span>
          </span>
         @if(beta())
				  
            <div class="col-xs-9 col-sm-4 col-lg-4 hide">
					    <div class="input-group">
                  <span class="input-group-addon">
                    <i class="ace-icon fa fa-check"></i>
                  </span>

                  <input type="text" class="form-control search-query" placeholder="Type your query">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-purple btn-sm">
                      <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                      Search
                    </button>
                  </span>
                </div>
				    </div>
            
          
           @endif
				</div>
        @if(beta())
          <div id="nav-search">
							<div class="input-group input-group-sm">
                  <input type="text" class="form-control search-query" placeholder="Type your query">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-purple btn-sm">
                      <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                    </button>
                  </span>
                </div>
						</div>
        @endif

				

				@include('backend.inc.topmenu')


			</div><!-- /.navbar-container -->

    
		</div>