@if(session('input'))
	@php 
		$inputdata=session('input');
	@endphp

@elseif(! empty($input))
	@php 
		$inputdata=$input;
	@endphp
@endif

@if( ! empty($inputdata))
	@foreach ($inputdata as $key => $val)
	   @php ${$key}=$val; @endphp
	@endforeach
@endif




@extends('backend.layout')
@section('header_import')
	

	<!--/******************
	*	Add file manager plugin *
	*******************/-->
	
	<link rel="stylesheet" href="{{asset('app/Plugins/Splitjs/splitjs.css')}}" />
	<!--JS Tree-->
	<link rel="stylesheet" href="{{asset('app/Plugins/Jstree/dist/themes/default/style.min.css')}}" />

	<!--autocomplet-->
	<link rel="stylesheet" href="{{asset('resources/assets/jqueryautocomplete/style.css')}}" />

	<!--spinner-->
	<link rel="stylesheet" href="{{asset('resources/assets/spinner/css/input-spinner.css')}}">
	

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
	
	 <!--autocomplet-->
	  <script src="{{asset('/resources/assets/jqueryautocomplete/jquery-autocomplete/dist/jquery.autocomplete.min.js')}}"></script>
	  <script src="{{asset('/resources/views/backend/vproduct/product.js')}}"></script>

	<script src="{{asset('/resources/assets/spinner/js/jquey.bootstrap-input-spinner.js')}}"></script>


	
	<script src="{{asset('/resources/assets/arcetheme/js/jquery.hotkeys.index.min.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/bootstrap-wysiwyg.min.js')}}"></script>

	

	<!--/******************
	*	Add file manager plugin *
	*******************/-->
	<script src="{{asset('app/Plugins/Filemanager/jsfun.js')}}"></script>
	<script src="{{asset('app/Plugins/Jstree/dist/jstree.min.js')}}"></script>
	<script src="{{asset('app/Plugins/Splitjs/split.min.js')}}"></script>
	<!-- Alert Panel (looks like js confrm dialog)-->
  	<script src="{{asset('/resources/assets/arcetheme/js/bootbox.js')}}"></script>
	<script src="{{asset('app/Plugins/twbs-pagination/jquery.twbsPagination.js')}}"></script>
  
	<script>
		Split(['#file_category', '#file_listing'], {
			sizes: [24, 76],
			minSize: 200
		});
	</script>
	<script>
		var filemanagerSetting={};
		var givent_txtbox;
		var editorContext; /*global variable for store Summernote object when v try to insert image*/

		
		/**-Browse File button @ each object-**/


		$(document).on("click", "#btn-browe-logo", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='image';
				filemanagerSetting.givent_txtbox='object';
				givent_txtbox=$('#logo');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
		});


		$(document).on("click", "#btn-browe-icon", function (e) {
		    ///
		    	e.preventDefault();

				filemanagerSetting=jsconfig.filemanagerSetting;
				filemanagerSetting.displaymode=2;
				filemanagerSetting.filetype='image';
				filemanagerSetting.givent_txtbox='object';
				givent_txtbox=$('#icon');
				categoryid=$('#filecategory').val();
				openMediaPanel(categoryid);
		    ///
		});


	</script>
	<!--/*end file manager*/-->




	
	<script type="text/javascript">
		$(document).ready(function() {
			@if (session('errors'))
				$.gritter.add({
								title: 'Error:',
								text: '<strong><i class="ace-icon fa fa-exclamation-triangle"></i></strong> {{ session('errors') }}',
								sticky: true,
								class_name: 'gritter-error gritter-center'
							});
			@endif

			@if (session('success'))
				$.gritter.add({
								title: 'Success:',
								text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong> {{ session('success') }}',
								sticky: false,
								class_name: 'gritter-success gritter-center'
							});

			@endif


			$( "#removecustomer" ).click(function() {
					$('#customer').val("{{__('ccms.retailer')}}");
          $('#cm_id').val('0');
          $('#ct_id').val('0');
				});


            $( "#removesupplier" ).click(function() {
					$('#supplier, #supplier_id').val('');
			});


		});
	</script>

	<script type="text/javascript">
		
		/* filter customer*/
			$('#customerfilter').devbridgeAutocomplete({
			    	serviceUrl: env.ajaxadmin_url,
			    	type: 'POST',
			   		dataType: 'json',
			   		secureuri: false,
			   		async: false,
					cache: false,
			        processData: false,
			        contentType: false,
			        minChars: 3,
			        noCache: true,
			   		transformResult: function(response) {
				        return {
				            suggestions: $.map(response, function(dataItem) {
				                return { value: dataItem.value, data: dataItem.data, ct_id: dataItem.ct_id };
				            })
				        };
				    },
			    	params : {_token:env.token, ajaxpath:'ajax_obj', objpath:'', ajaxobj:'customer','withbrahch':'no', ajaxact:'autocomplet'},
			    	triggerSelectOnValidInput: false,
				    onSelect: function (suggestion) {
				        //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
				        $('#cm_id').val(suggestion.data);
				        $('#customer').val(suggestion.value);
                $('#ct_id').val(suggestion.ct_id);
				        $(this).val('');
				    },

				    onSearchStart: function (query) {
				    	$(this).inputSpinner({marginright:'10px'});
				    },
				    onSearchComplete: function (query, suggestions) {
				    	$(this).removeSpinner();	
				    }
			});


		/* filter customer*/
			$('#supplierfilter').devbridgeAutocomplete({
			    	serviceUrl: env.ajaxadmin_url,
			    	type: 'POST',
			   		dataType: 'json',
			   		secureuri: false,
			   		async: false,
					cache: false,
			        processData: false,
			        contentType: false,
			        minChars: 3,
			        noCache: true,
			   		transformResult: function(response) {
				        return {
				            suggestions: $.map(response, function(dataItem) {
				                return { value: dataItem.value, data: dataItem.data };
				            })
				        };
				    },
			    	params : {_token:env.token, ajaxpath:'ajax_obj', objpath:'', ajaxobj:'supplier', 'withbrahch':'no', ajaxact:'autocomplet'},
			    	triggerSelectOnValidInput: false,
				    onSelect: function (suggestion) {
				        //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
				        $('#supplier_id').val(suggestion.data);
				        $('#supplier').val(suggestion.value);
				        $(this).val('');
				    },

				    onSearchStart: function (query) {
				    	$(this).inputSpinner({marginright:'10px'});
				    },
				    onSearchComplete: function (query, suggestions) {
				    	$(this).removeSpinner();	
				    }
			});

	</script>

	<script type="text/javascript">
		
		$('#editor1').ace_wysiwyg(

			{
				toolbar:
				[
					null,
					null,
					'fontSize',
					null,
					{name:'bold', className:'btn-info'},
					{name:'italic', className:'btn-info'},
					{name:'strikethrough', className:'btn-info'},
					{name:'underline', className:'btn-info'},
					null,
					{name:'insertunorderedlist', className:'btn-success'},
					{name:'insertorderedlist', className:'btn-success'},
					{name:'outdent', className:'btn-purple'},
					{name:'indent', className:'btn-purple'},
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					'foreColor',
					null,
					{name:'undo', className:'btn-grey'},
					{name:'redo', className:'btn-grey'}
				]
			}

		).prev().addClass('wysiwyg-style2');

		/*

			[
			'font',
			null,
			'fontSize',
			null,
			{name:'bold', className:'btn-info'},
			{name:'italic', className:'btn-info'},
			{name:'strikethrough', className:'btn-info'},
			{name:'underline', className:'btn-info'},
			null,
			{name:'insertunorderedlist', className:'btn-success'},
			{name:'insertorderedlist', className:'btn-success'},
			{name:'outdent', className:'btn-purple'},
			{name:'indent', className:'btn-purple'},
			null,
			{name:'justifyleft', className:'btn-primary'},
			{name:'justifycenter', className:'btn-primary'},
			{name:'justifyright', className:'btn-primary'},
			{name:'justifyfull', className:'btn-inverse'},
			null,
			{name:'createLink', className:'btn-pink'},
			{name:'unlink', className:'btn-pink'},
			null,
			{name:'insertImage', className:'btn-success'},
			null,
			'foreColor',
			null,
			{name:'undo', className:'btn-grey'},
			{name:'redo', className:'btn-grey'}
		]

		*/

		$( "#frmadd-{{$obj_info['name']}}" ).submit(function( event ) {
			
			var html = $('#editor1').html();
			
				$('<input />').attr('type', 'hidden')
		          .attr('name', "paymentinfo")
		          .attr('value', html)
		          .appendTo('#frmadd-{{$obj_info['name']}}');
			
		  	
		});



	</script>
	
	
@stop	


@section('app')

<!-- /...........................................................page-header -->	

<div class="page-header" data-spy="affix" data-offset-top="60">
<div class="row">
	<div class="col-xs-12 col-sm-6 col-lg-6">
	<h1>
				{!! $obj_info['icon'] !!}
				<a href="{{url_builder($obj_info['routing'],[$obj_info['name'],'index'])}}">
					{!! $obj_info['title'] !!}
				</a>
		<small>
			<i class="ace-icon fa fa-angle-double-right"></i>
			{{$caption}}
		</small>
	</h1>
	</div>
	<div class="col-xs-12 col-sm-6 col-lg-6">
		@include('backend.widget.btnaa',['btnsave' => 'yes', 'btnnew' => 'no', 'btnapply' => 'no', 'btncancel' => 'no'])							
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->


<form name="frmadd-{{$obj_info['name']}}" id="frmadd-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" enctype="multipart/form-data">
{{ csrf_field() }}	
<input type="hidden" name="{{$fprimarykey}}" id="{{$fprimarykey}}" value="{{ ${$fprimarykey} ?? '' }}">						
								
<div class="row">
	<div class="col-xs-12 col-sm-6 col-lg-6">

		<!--Panel -->	
		<div class="widget-box">						
										
				<div class="widget-header">
				<h4 class="smaller">
					POS
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<div class="row">
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Cost/Stock Method</label>

																	<br/>
							<select class="form-control input-sm" name="costmethod" id="costmethod">
																		{!!cmb_listing(['average'=>__('ccms.average'), 'fifo'=>__('ccms.fifo'), 'lifo'=>__('ccms.lifo'), 'fefo'=>__('ccms.fefo')],[$costmethod ?? 'average'],
																		'','')
																		!!} 
															</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="lowstock"># Low Stock Alert</label>

																	<br/>
							<input class="form-control input-sm" type="number" min="1" name="lowstock" id="lowstock"   value="{{ $lowstock ?? '5' }}"/>
              
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Product to Product Transfer</label>

																	<br/>
							<select class="form-control input-sm" name="p2ptransfer" id="p2ptransfer">
																		{!!cmb_listing(['manual'=>__('ccms.manual'), 'auto'=>__('ccms.auto')],[$p2ptransfer ?? 'manual'],
																		'','')
																		!!} 
															</select>	
						</div>  
            <!-- /.column --> 
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Made-With Stock</label>

																	<br/>
							<select class="form-control input-sm" name="madewithstock" id="madewithstock">
																		{!!cmb_listing(['resource'=>__('ccms.resource'), 'own'=>__('ccms.own')],[$madewithstock ?? 'resource'],
																		'','')
																		!!} 
															</select>	
						</div><!-- /.column -->

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
															<label class="frm-label" for="customerfilter">General Customer</label>
															
															
															<div class="input-group" style="width: 100%;">
											                    <div class="form-inline">
											                                          <input type="text" name="customerfilter" id="customerfilter" class="form-control input-sm" placeholder="Filter customer..." onblur="this.value=''" autocomplete="off" style="width: 40%;">

											                                          <input type="text" name="customer" id="customer" class="en label_b form-control input-sm" value="{{$customer??__('ccms.retailer')}}" readonly="readonly" style="width: 50%;">

											                                          <input type="hidden" name="cm_id" id="cm_id" value="{{$cm_id??'0'}}">
                                                                <input type="hidden" name="ct_id" id="ct_id" value="{{$ct_id??'0'}}">

											                                          <button type="button" class="btn btn-default input-sm form-control" id="removecustomer" style="width: 9%;">
											                                          	<i class="ace-icon fa fa-times"></i>
											                                          </button>

											                                          

											                    </div>
											                                        
											                </div>
											                                    
																										  
														</div><!--./end col-->


						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
															<label class="frm-label" for="customerfilter">General Supplier</label>
															
															
															<div class="input-group" style="width: 100%;">
											                    <div class="form-inline">
											                                          <input type="text" name="supplierfilter" id="supplierfilter" class="form-control input-sm" placeholder="Filter supplier..." onblur="this.value=''" autocomplete="off" style="width: 40%;">

											                                          <input type="text" name="supplier" id="supplier" class="en label_b form-control input-sm" value="{{$supplier??''}}" readonly="readonly" style="width: 50%;">

											                                          <input type="hidden" name="supplier_id" id="supplier_id" value="{{$supplier_id??''}}">

											                                          <button type="button" class="btn btn-default input-sm form-control" id="removesupplier" style="width: 9%;">
											                                          	<i class="ace-icon fa fa-times"></i>
											                                          </button>

											                                          

											                    </div>
											                                        
											                </div>
											                                    
																										  
														</div><!--./end col-->
            
            
             
            
            
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="requiredchkin">POS Required Check-In</label>

																	<br/>
							<select class="form-control input-sm" name="requiredchkin" id="requiredchkin">
																	   
								
								{!!cmb_listing(['no'=>'No', 'yes'=>'Yes'],[$requiredchkin ?? 'no'],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
              
           



						
            
             <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="posaccno_id">POS Interface</label>

																	<br/>
							<select class="form-control input-sm accno_id" name="postheme" id="postheme">
								
								{!!cmb_listing(['t1'=>'Original', 't2'=>'Small Size', 't3'=>'No Image '],[$postheme ?? 't1'],
																					'','')
																					!!} 
							</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="posaccno_id">Invoice Interface</label>

																	<br/>
							<select class=" form-control input-sm accno_id" name="invtheme" id="invtheme">
								
								{!!cmb_listing(['t1'=>'Original', 't2'=>'Theme 1'],[$invtheme ?? 't1'],
																					'','')
																					!!} 
							</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="df_dis">Default Discount</label>

																	<br/>
							<select class="form-control input-sm" name="df_dis" id="df_dis">
																	   
								
								{!!cmb_listing(config('ccms.discounttype'),[$df_dis ?? ''],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            
<!--      Only display for Programmer Mode        !empty($codermode)--> 
            
             <div class="col-xs-12 col-sm-4 col-lg-4 form-group {{$codermode}}">
							<label class="frm-label" for="restaurant">POS for Restaurant</label>

																	<br/>
							<select class="form-control input-sm" name="restaurant" id="restaurant">
																	   
								
								{!!cmb_listing(['no'=>'No', 'yes'=>'Yes'],[$restaurant ?? 'no'],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-4 col-lg-4 form-group {{$codermode}}">
							<label class="frm-label" for="customermode">Customer Mode</label>

																	<br/>
							<select class="form-control input-sm" name="customermode" id="customermode">
																	   
								
								{!!cmb_listing(['map'=>'Mapping', 'input'=>'Input', 'both' => 'Input and Mapping', 'referral'=>'Referral'],[$customermode ?? 'no'],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-4 col-lg-4 form-group {{$codermode}}">
							<label class="frm-label" for="madewithtocart">Made With To Cart</label>

																	<br/>
							<select class="form-control input-sm" name="madewithtocart" id="madewithtocart">
																	   
								
								{!!cmb_listing(['main'=>'Main Product', 'sub'=>'Sub Product'],[$madewithtocart ?? 'main'],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            

						<div class="col-xs-12 col-sm-4 col-lg-4 form-group {{$codermode}}">
							<label class="frm-label" for="">Using Size/Color?</label>

																	<br/>
							<select class="form-control input-sm" name="usingsizecolor" id="usingsizecolor">
																	   
								
								{!!cmb_listing(['yes'=>'Yes', 'no'=>'No'],[$usingsizecolor ?? 'yes'],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            
            
            <div class="col-xs-12 col-sm-4 col-lg-4 form-group {{$codermode}}">
							<label class="frm-label" for="">Using Extra Price?</label>

																	<br/>
							<select class="form-control input-sm" name="usingextraprice" id="usingextraprice">
																	   
								
								{!!cmb_listing(['no'=>'No', 'yes'=>'Yes'],[$usingextraprice ?? 'no'],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->



						<div class="col-xs-12 col-sm-4 col-lg-4 form-group {{$codermode}}">
							<label class="frm-label" for="sub_input">Sub Product Input</label>

																	<br/>
							<select class="form-control input-sm" name="sub_input" id="sub_input">
																	   
								
								{!!cmb_listing([1=>'textbox', 2=>'textarea'],[$sub_input ?? ''],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-12 col-lg-12 form-group {{$codermode}}">
              Product#:<input class="form-control input-sm" type="text" name="productnum" id="productnum"   value="{{ $productnum ?? '50' }}"/>
              <br>
              W#:<input class="form-control input-sm" type="text" name="whnum" id="whnum"   value="{{ $whnum ?? '2' }}"/>
              <br>
              B#:<input class="form-control input-sm" type="text" name="branchnum" id="branchnum"   value="{{ $branchnum ?? '2' }}"/>
              <br>
              Pos for:<input class="form-control input-sm" type="text" name="posfor" id="posfor"   value="{{ $posfor ?? 'pos' }}"/>
              
							<br>
            <!--        separate/share        -->
              Hold Mode:<input class="form-control input-sm" type="text" name="holdmode" id="holdmode"   value="{{ $holdmode ?? 'separate' }}"/>
              
						</div><!-- /.column -->
            
            
            

            
            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="companyname">Company Name</label>

																	<br/>
							<input class="form-control input-sm" type="text" min="1" name="companyname" id="companyname"   value="{{ $companyname ?? 'Your Company' }}"/>
						</div><!-- /.column -->

						<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="poswelcometext">POS Welcome Text</label>

																	<br/>
							<input class="form-control input-sm" type="text" min="1" name="poswelcometext" id="poswelcometext"   value="{{ $poswelcometext ?? 'Welcome to i-POS' }}"/>
						</div><!-- /.column -->
            
            
            
            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="paymentinfo">Payment Info</label>

							

							<div class="wysiwyg-editor" id="editor1" style="border: 1px solid #ddd">
								{!! html_entity_decode($paymentinfo ?? '') !!}
							</div>


						</div><!-- /.column -->
						  
						  
					</div><!-- /.row -->								
														
																				
					
				</div>
			</div>
		</div> <!-- /.Panel -->	
    
    <!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					Accounting
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					<div class="row">
            
             <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="posaccno_id">Invoice/POS Account.No</label>

																	<br/>
							<select class="form-control input-sm accno_id" name="posaccno_id" id="posaccno_id" data-placeholder="Choose a Acc.No...">
								
								{!!cmb_listing($accountno,[$posaccno_id ?? 0],
																					'','')
																					!!} 
							</select>	
						</div><!-- /.column -->
            
            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Pos Payment Method</label>

																	<br/>
							<select class="form-control input-sm" name="pospmethod_id" id="pospmethod_id">
																		{!!cmb_listing($paymentmethod,[$pospmethod_id ?? 0],
																		'','')
																		!!} 
															</select>	
						</div><!-- /.column -->
						 	
             <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Sale Discount Account.No</label>

																	<br/>
							<select class="form-control input-sm" name="acc_salediscount" id="acc_salediscount">
																		{!!cmb_listing($account_discount,[$acc_salediscount ?? 0],
																		'','')
																		!!} 
															</select>	
						</div><!-- /.column -->


						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Profit Current Year Account.No</label>

																	<br/>
							<select class="form-control input-sm" name="acc_pcy" id="acc_pcy">
								<option></option>
																		{!!cmb_listing($account_eqt,[$acc_pcy ?? -1],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pospmethod_id">Retained Earning Account.No</label>

																	<br/>
							<select class="form-control input-sm" name="acc_re" id="acc_re">
								<option></option>
																		{!!cmb_listing($account_eqt,[$acc_re ?? -1],
																		'','')
																		!!} 
							</select>	
						</div><!-- /.column -->
            
            
            
            
            
            
					</div><!-- /.row -->
				</div>
			</div>								
																	
		</div><!-- /.Panel -->	
    
  
    
    @if(config('sysconfig.posfor')=='clinic')
    @php
    
    @endphp
      <!--Panel -->	
		  <div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					Clinic
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<div class="row">
						 <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="catlabo">Labo</label>
							<select class="form-control input-sm chosen-select-deselect" name="catlabo[]" id="catlabo" multiple data-placeholder="Choose some categories...">
																	   
								{!! CategorySelectboxTree($cat_tree, '', '', isset($catlabo) && !empty($catlabo)?$catlabo:['']) !!} 
							</select>			
						</div>	
            
            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="catimagery">Imagery</label>
							<select class="form-control input-sm chosen-select-deselect" name="catimagery[]" id="catimagery" multiple data-placeholder="Choose some categories...">
																	   
								{!! CategorySelectboxTree($cat_tree, '', '', isset($catimagery) && !empty($catimagery)?$catimagery:['']) !!} 
							</select>			
						</div>
            
            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
							<label class="frm-label" for="catservice">Service</label>
							<select class="form-control input-sm chosen-select-deselect" name="catservice[]" id="catservice" multiple data-placeholder="Choose some categories...">
																	   
								{!! CategorySelectboxTree($cat_tree, '', '', isset($catservice) && !empty($catservice)?$catservice:['']) !!} 
							</select>			
						</div>

						

					</div><!-- /.row -->


				


				</div>
			</div>								
																	
		</div><!-- /.Panel -->
    @endif
		





	</div> <!-- /.col -->

	<!-- Right Blog ------------------------------------------------------------------------------------- -->
	<div class="col-xs-12 col-sm-6 col-lg-6">

		<!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					Format
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="inv">Invoice ID</label>
							<input class="form-control input-sm" type="text" name="inv" id="inv"   value="{{ $inv ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="qt">Quotation ID</label>
							<input class="form-control input-sm" type="text" name="qt" id="qt"   value="{{ $qt ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="addstock">Add-Stock ID</label>
							<input class="form-control input-sm" type="text" name="addstock" id="addstock"   value="{{ $addstock ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="adjuststock">Adjust-Stock ID</label>
							<input class="form-control input-sm" type="text" name="adjuststock" id="adjuststock"   value="{{ $adjuststock ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->


					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pch">Phurchase ID</label>
							<input class="form-control input-sm" type="text" name="pch" id="pch"   value="{{ $pch ?? '' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="po">Phurchase Order ID</label>
							<input class="form-control input-sm" type="text" name="po" id="po"   value="{{ $po ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="rp">Receive.Payment ID</label>
							<input class="form-control input-sm" type="text" name="rp" id="rp"   value="{{ $rp ?? 'AR' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="je">Journal Entry</label>
							<input class="form-control input-sm" type="text" name="je" id="je"   value="{{ $je ?? 'JE' }}"/>		
						</div>	

					</div><!-- /.row -->
          
          <div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="rt">Invoice Return</label>
							<input class="form-control input-sm" type="text" name="rt" id="rt"   value="{{ $rt ?? 'RTN' }}"/>		
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="rtpch">Purchase Return</label>
							<input class="form-control input-sm" type="text" name="rtpch" id="rtpch"   value="{{ $rtpch ?? 'RTN-PCH' }}"/>		
						</div>	

					</div><!-- /.row -->


					<div class="row">
             
            <div class="col-xs-12 col-sm-4 col-lg-4 form-group">
							<label class="frm-label" for="exp">Expense ID</label>
							<input class="form-control input-sm" type="text" name="exp" id="exp"   value="{{ $exp ?? 'EXP' }}"/>		
						</div>	

						<div class="col-xs-12 col-sm-4 col-lg-4 form-group">
							<label class="frm-label" for="pp">Purchase Payment ID</label>
							<input class="form-control input-sm" type="text" name="pp" id="pp"   value="{{ $pp ?? 'AP' }}"/>		
						</div>	


						 <div class="col-xs-12 col-sm-4 col-lg-4 form-group">
							<label class="frm-label" for="dl">Delivery ID</label>
							<input class="form-control input-sm" type="text" name="dl" id="dl"   value="{{ $dl ?? '' }}"/>		
						</div>		

						

					</div><!-- /.row -->

					<div class="row">

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="iddigit">ID Digit</label>
							<input class="form-control input-sm" type="number" min="1" name="iddigit" id="iddigit"   value="{{ $iddigit ?? '6' }}"/>		
						</div>	 	

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="pdfwatermask">PDF Watermark</label>
							<input class="form-control input-sm" type="text" name="pdfwatermask" id="pdfwatermask"   value="{{ $pdfwatermask ?? '' }}"/>		
						</div>	

					</div><!-- /.row -->


				</div>
			</div>								
																	
		</div><!-- /.Panel -->	
    
    <!------------------------------>
			<!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					PDF Format
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">


					<div class="row">
						@php
							$titlecol = ['A4', 'A5', 'POS Receipt'];
							$format_df = ['A4', 'A5', '80,1440'];
						@endphp
						@for($i=0; $i < 3 ; $i++)
						<div class="col-xs-12 col-sm-4 col-lg-4 form-group">
							<span class="badge badge-info">{{$titlecol[$i]}}</span>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="pdfsize">Size</label>
									<input class="form-control input-sm" type="text" name="format[]" id="format"   value="{{ $format[$i] ?? $format_df[$i] }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="orientation">Orientation</label>
									<input class="form-control input-sm" type="text" name="orientation[]" id="orientation"   value="{{ $orientation[$i] ?? 'P' }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="margin_left">Margin.left</label>
									<input class="form-control input-sm" type="text" name="margin_left[]" id="margin_left"   value="{{ $margin_left[$i] ?? '10' }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="margin_right">Margin.right</label>
									<input class="form-control input-sm" type="text" name="margin_right[]" id="margin_right"   value="{{ $margin_right[$i] ?? '10' }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="margin_top">Margin.top</label>
									<input class="form-control input-sm" type="text" name="margin_top[]" id="margin_top"   value="{{ $margin_top[$i] ?? '10' }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="margin_bottom">Margin.bottom</label>
									<input class="form-control input-sm" type="text" name="margin_bottom[]" id="margin_bottom"   value="{{ $margin_bottom[$i] ?? '10' }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="margin_header">Margin.header</label>
									<input class="form-control input-sm" type="text" name="margin_header[]" id="margin_header"   value="{{ $margin_header[$i] ?? '10' }}"/>		
								</div>	
								<!----->
								<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
									<label class="frm-label" for="margin_footer">Margin.footer</label>
									<input class="form-control input-sm" type="text" name="margin_footer[]" id="margin_footer"   value="{{ $margin_footer[$i] ?? '10' }}"/>		
								</div>	
								<!----->



							</div><!--row-->
						</div>
						<!--col-->
						@endfor
						


					</div><!--row-->




				</div>
			</div>								
																	
		</div><!-- /.Panel -->	

		<!--***************************-->
  
    <!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					Cash-In/Cash-Out
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="ciosize">Printing Size</label>
							<select class="form-control input-sm" name="ciosize" id="ciosize">
																	   
								
								{!!cmb_listing(['a4'=>'A4', '80'=>'80mm', '58'=>'58mm'],[$ciosize ?? 'a4'],
																		'','')
																		!!} 
							</select>			
						</div>		

						<div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="cioproduct">Display Product</label>
							<select class="form-control input-sm" name="cioproduct" id="cioproduct">
																	   
								
								{!!cmb_listing(['yes'=>'Yes', 'no'=>'No'],[$cioproduct ?? 'yes'],
																		'','')
																		!!} 
							</select>			
						</div>	

					</div><!-- /.row -->


				


				</div>
			</div>								
																	
		</div><!-- /.Panel -->	
    
    <!--Panel -->	
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="smaller">
					Reports
				</h4>
			</div>
			<div class="widget-body">
				<div class="widget-main">

					<div class="row">

						 <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
							<label class="frm-label" for="withcosting">Profit &#38; Loss (Included Costing)</label>
							<select class="form-control input-sm" name="withcosting" id="withcosting">
								{!!cmb_listing(['yes'=>'Yes', 'no'=>'No'],[$withcosting ?? 'yes'],
																		'','')
																		!!} 
							</select>			
						</div>		
					</div><!-- /.row -->
				</div>
			</div>								
																	
		</div><!-- /.Panel -->	
		



	</div><!-- /.col -->

</div>
</form>

@stack('plugin')
	@if (!session('ajax_access'))
		@include('Filemanager.manager')
	@endif
@stop


								

