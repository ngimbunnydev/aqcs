@php 
	
@endphp

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap" rel="stylesheet">
		<style type="text/css">
			@page {
				header: page-header;
				footer: page-footer;
			}
			body,table {
				font-family: arial;
                font-size: 10pt;
            }
			
			.battambang{
				font-family: 'Battambang';
			}
			.clearfix::after {
				content: "";
				clear: both;
				display: table;
			}

			body {
			background: #fff;
			}
			page {
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
			box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
			padding: 20px;
			}
			page[size="A4"] {  
			width: 21cm;
			height: 29.7cm; 
			}
			page[size="A4"][layout="landscape"] {
			width: 29.7cm;
			height: 21cm;  
			}
			page[size="A3"] {
			width: 29.7cm;
			height: 42cm;
			}
			page[size="A3"][layout="landscape"] {
			width: 42cm;
			height: 29.7cm;  
			}
			page[size="A5"] {
			width: 14.8cm;
			height: 21cm;
			}
			page[size="A5"][layout="landscape"] {
			width: 21cm;
			height: 14.8cm;  
			}
			@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
			}

		</style>
		<!-- BEGIN PAGE LEVEL PLUGINS -->
		    @stack('customcss')
		<!-- END PAGE LEVEL PLUGINS -->
		<!-- Page specific plugin scripts/css -->
		@yield('header_import')
		<!-- END -->
	</head>

	<body class="no-skin">
	
		<!-- PAGE CONTENT BEGINS -->
		<page size="A4" layout="landscape">
			@yield('app')
		</page>
		
		<!-- PAGE CONTENT ENDS -->
		<!-- Page specific plugin scripts -->
		@stack('scripts')
		@yield('footer_import')
		<!-- END -->
		
	</body>
</html>
