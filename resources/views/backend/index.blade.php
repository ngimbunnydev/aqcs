<!DOCTYPE html>
<html lang="en">
	<head>
		@if (!session('ajax_access'))
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
		<title>i-POS .:. {{$obj_info['title'] ?? 'Welcome'}}</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="{{ URL::asset('/resources/assets/arcetheme/css/bootstrap.min.css') }}" />
		<link rel="stylesheet" href="{{ URL::asset('/resources/assets/arcetheme/font-awesome/5.9.0/css/all.min.css') }}" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="{{ URL::asset('/resources/assets/arcetheme/css/fonts.googleapis.com.css') }}" />

		<!-- ace styles -->
		<link rel="stylesheet" href="{{ URL::asset('/resources/assets/arcetheme/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="{{ URL::asset('/resources/assets/arcetheme/css/ace-skins.min.css') }}" />
		<link rel="stylesheet" href="{{ URL::asset('/resources/assets/arcetheme/css/ace-rtl.min.css') }}" />

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="{{ URL::asset('/resources/assets/arcetheme/js/ace-extra.min.js') }}"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
        
        <!-- Default own CSS -->
        <!--<link rel="stylesheet" href="{{ URL::asset('/resources/views/backend/lib/css/'.Config::get('own.ubrowser').'.css') }}" />-->
		<link rel="stylesheet" href="{{ URL::asset('/resources/views/backend/lib/css/unknown.css') }}" />
        

        <!-- FOR MESSAGE ALERT -->
        <link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/jquery.gritter.min.css') }}" />

       @endif

		<!-- BEGIN PAGE LEVEL PLUGINS -->
		    @stack('cssfiles')
		<!-- END PAGE LEVEL PLUGINS -->

		<!-- Page specific plugin scripts/css -->
			@yield('header_import')
		<!-- END -->

		
	</head>

	<body class="no-skin">
	<span class="hide" id="unittemplate">
  </span>   
				<!-- PAGE CONTENT BEGINS -->
				@yield('content')
				<!-- PAGE CONTENT ENDS -->

		<!-- basic scripts -->

		<script>
			var env = {!!json_encode(config('ccms.js_env'))!!};
			//env.ajaxpublic_url="{{url('/').config('ccms.js_env.ajaxpublic_url')}}";
			//env.ajaxadmin_url ="{{url('/'.config('ccms.backend').config('ccms.js_env.ajaxadmin_url'))}}";

			env.token="{{ csrf_token() }}";
			@isset($js_config)
			var jsconfig = {!!json_encode($js_config)!!};
			//alert(jsconfig.BASE);
			@endisset

	  		//var productunits = {--!!json_encode($productunits)!!--};
			//   var __FOUND = productunits.find(function(units, index) {
				 
			// 		return units.pd_id == 1
						
			// 	});
				
		</script>

		@if (!session('ajax_access'))
		<!--[if !IE]> -->
		<script src="{{ URL::asset('/resources/assets/arcetheme/js/jquery-2.1.4.min.js') }}"></script>
	
		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
    

		<script type="text/javascript">
			if('ontouchstart' in document.documentElement)
			{
			 //document.write("");
			 
			 

			 var s = document.createElement("script");
				s.src = "{{ URL::asset('/resources/assets/arcetheme/js/jquery.mobile.custom.min.js') }}";
				s.onload = function(e){ /* now that its loaded, do something */ }; 
				document.head.appendChild(s);



			}
		</script>
		<!-- <script src="{{ URL::asset('/resources/assets/arcetheme/js/jquery.mobile.custom.min.js') }}"></script> -->
        <!--<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>-->
		<script src="{{ URL::asset('/resources/assets/arcetheme/js/bootstrap.min.js') }}"></script>

		<!-- page specific plugin scripts -->


		<!-- ace scripts -->
		<script src="{{ URL::asset('/resources/assets/arcetheme/js/ace-elements.min.js') }}"></script>
		<script src="{{ URL::asset('/resources/assets/arcetheme/js/ace.min.js') }}"></script>

		<!-- inline scripts related to this page -->
		<script src="{{ URL::asset('/resources/assets/js/jsfun.js') }}"></script>
		<!-- FOR MESSAGE ALERT -->
        <script src="{{asset('/resources/assets/arcetheme/js/jquery.gritter.min.js')}}"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js"></script> -->
    <!-- webcam -->
    <script src="{{asset('/resources/assets/webcamjs/webcam.min.js')}}"></script>
    
        
        

        @endif
    
    <script>
 
      $( document )
      .ajaxStart(function() {
        //$("#globalaction").removeClass('hide');
      })
      .ajaxComplete(function() {
        //$('#globalaction').addClass('hide');
      });
      
      $(document).ready(function(){
        @isset($userinfo)
          @if($userinfo->level_id!=1)
            $('.showhidemenu').addClass('hide');
            @php
              $class_toshow = [];
              foreach($userinfo->levelsetting as $obj){
                $obj = explode('-', $obj);
                
                array_push($class_toshow, '.showhide_'.$obj[0]);
              } 
            @endphp
            $('{{implode(",", $class_toshow)}}').removeClass('hide');
          @endif
          
        @endisset

		$('html').click(function() {
			//$('.popover').remove();
    		$('.unitlebel, .unitlabel').popover("hide").removeClass('showUnit');
			
		});
        
        
      });
    
        </script>



		<!-- Page specific plugin scripts -->
			@stack('scripts')
			@yield('footer_import')
		<!-- END -->
		
	</body>
	<footer>
		
	</footer>
</html>
