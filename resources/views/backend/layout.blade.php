
@extends('backend.index')
@section('content')
		<!-- start Header -->
			@if (!session('ajax_access'))
        		@include('backend.inc.header')
        	@endif
        <!-- End Header -->

        @if (!session('ajax_access'))
		<div class="main-container ace-save-state" id="main-container">
		@else
		<div class="ace-save-state" id="main-container">
		@endif

			<script type="text/javascript">
				try{ace.settings.loadState('main-container')}catch(e){}
			</script>
            <!-- start Nevigetor -->
            @if (!session('ajax_access'))
                @if(!isset($displaymainmenu) || !$displaymainmenu)
               
                  @include('backend.inc.navigator')
                @endif
      
              
            @endif
            <!-- End Nevigator -->
            
			<div class="main-content">
				<div class="main-content-inner">

					
					
					<div class="page-content">
						
						<div class="row">
							<div class="col-xs-12">
                
<!--                 <div id="globalaction" class="progress progress-small progress-striped active hide" style="height: 4px; margin: 0">
                          <div class="progress-bar progress-bar-info" style="width: 100%;"></div>
                </div> -->
								<!-- PAGE CONTENT BEGINS -->
								@yield('app')
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->

					</div><!-- /.page-content -->

				</div>
			</div><!-- /.main-content -->

			

			@if (!session('ajax_access'))
	            <!-- footer start-->
              @if(!isset($displaymainmenu) || !$displaymainmenu)
	                @include('backend.inc.footer')
              @endif
	            <!-- end footer -->

	            <!-- AJax window-->
	                @include('backend.ajax')
	            <!-- end ajax -->
	        @endif
		

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->
@stop