@php 
	$header_contact_info = json_decode(html_entity_decode($pageinfo['contact']),true); 
@endphp

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
      @page {
				header: page-header;
				footer: page-footer;
			}
      body {
          background: white;/*rgb(204,204,204); */
        }
        page {
          background: white;
          display: block;
          margin: 0 auto;
          margin-bottom: 0.5cm;
        /*   box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
        }
        page[size="pos58"] {  
          width: 58mm;
          height: 100%; 
        }
        page[size="pos58"][layout="landscape"] {
          width: 100%;
          height: 58mm;  
        }
        
        @media print {
          body, page {
            margin: 0;
            box-shadow: 0;
            width: 58mm;
            height:100%;
            position:absolute;
          }
        }

    </style>
		
		<!-- BEGIN PAGE LEVEL PLUGINS -->
		    @stack('customcss')
		<!-- END PAGE LEVEL PLUGINS -->
	</head>

	<body>
    <page size="pos58" class="first-page relative">
		<!-- PAGE CONTENT BEGINS -->
		@yield('app')
		<!-- PAGE CONTENT ENDS -->
    </page>
	</body>
</html>
