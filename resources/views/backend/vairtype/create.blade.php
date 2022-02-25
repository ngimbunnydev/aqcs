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

@foreach (config('ccms.multilang') as $lang)
	@php
		$langcode[]=$lang[0];
	@endphp
@endforeach




@extends('backend.layout')
@section('header_import')
	

@stop

@section('footer_import')

	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
  <script src="{{asset('/resources/assets/arcetheme/js/spinbox.min.js')}}"></script>

	<script>

	 	$( "#btnAdd" ).click(function() {
		 	var newitemid='newid';
		  	add_item(newitemid);
	  	$('#ordering'+newitemid).ace_spinner({value:0,min:0,step:1, btn_up_class:'btn-info' , btn_down_class:'btn-info'})
					.closest('.ace-spinner')
					.on('changed.fu.spinbox', function(x,y){
						//console.log($('#ordering').val())
					});

			$('#ordering'+newitemid).removeAttr('id');	
		});

	$(document).on("change", "#tab_title", function (ev) {
		    ///
		    	var $value=$(this).val();
	  			enableDisableByLang($(this),{!!json_encode($langcode,true)!!},'title-',$value)
		    ///
		});

		$(document).on("click", ".removeButton", function (ev) {
		    ///
		    	var $row  = $(this).parents('.table_row');
                $row.remove();
		    ///
		});


  </script>


  <script>
  	
  	function add_item(new_id){
        var $template = $('#recordTemplate'),
		$clone    = $template
                .clone()
                .removeClass('hide')
                .removeAttr('id')
                .insertAfter('.tablediv .table_row:last');                        
                                
    $clone
        .find("#ordering").attr( 'id', 'ordering'+new_id).end();                        
	}


  </script>


	
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
      
      $('.ordering0').each(function(i, obj) {
				$(this).ace_spinner({value:0,min:0,step:1, btn_up_class:'btn-info' , btn_down_class:'btn-info'});

			}); 
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
		@include('backend.widget.btnaa',['btnsave' => 'yes', 'btnnew' => 'yes', 'btnapply' => 'yes', 'btncancel' => 'yes'])							
	</div>									
</div>	
							
										
</div>						
<!-- /...........................................................page-header -->	

<!-- /...........................................................Message status -->

@php
	$records=1;
@endphp
@if(!empty(${$fprimarykey}))
	@php
		$records=count(${$fprimarykey});
	@endphp
@endif

<form name="frmadd-{{$obj_info['name']}}" id="frmadd-{{$obj_info['name']}}" method="POST" action="{{ url_builder($obj_info['routing'],[$obj_info['name'],$submitto]) }}" enctype="multipart/form-data">
{{ csrf_field() }}	

	

	@if (session('ajax_access'))
		@if(!empty(\Request::get('ajaxnext')))
		@php
			$ajaxnext_val=\Request::get('ajaxnext');
		@endphp	
		@elseif(!empty($ajaxnext))
			@php
				$ajaxnext_val=$ajaxnext;
			@endphp	
					
		@endif
		<input type="hidden" name="ajaxnext" value="{{$ajaxnext_val}}">	
	@endif			
								
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-12">
		<span class="frm-label"><span class="red">*</span> @lang('ccms.isrequire')</span>
		<div class="tablediv" id="results">
            <div class='theader'>
			  
			  <div class='table_header'>@lang('label.title') <span class="red">*</span></div>
			  <div class='table_header'>@lang('label.code') <span class="red">*</span></div>
			  <div class='table_header'>@lang('label.lb22')</div>
			  <div class='table_header'>Unit</div>
			  <div class='table_header'>Color</div>
			  <div class='table_header'>Note</div>
			  <div class='table_header'>@lang('ccms.ordering')</div>
			  <div class='table_header'>&nbsp;</div>
			</div> 

			@for ($i = 0; $i < $records; $i++)
            <div class='table_row'>
      
              <div class='table_small'>
                              
                    <div class='table_cell'>@lang('label.title') <span class="red">*</span></div>
					<div class='table_cell'>
						<input type="hidden" name="{{$fprimarykey}}[]" id="{{$fprimarykey}}" value="{{${$fprimarykey}[$i] ?? ''}}">	
						<div class="input-group" style="width:100%;"> 

	            			<select id="tab_title" class="form-control input-sm" style="width:25%;">
	                        @foreach (config('ccms.multilang') as $lang)
		                        <option value="@lang($lang[0])">@lang($lang[1])</option>
		                    @endforeach
	                        
	                    	</select>

	                    	@php ($active = '') @endphp
	                    	@foreach (config('ccms.multilang') as $lang)
	                    	<input type="text" class="form-control input-sm {{$active}}" style="width:75%;" name="title-{{$lang[0]}}[]" id="title-{{$lang[0]}}" placeholder="{{$lang[1]}}" value="{{ ${'title-'.$lang[0]}[$i] ?? '' }}">
	                    	@php ($active = 'hide') @endphp
	                    	@endforeach

	        			</div>
					</div>          
                                   
                </div><!-- /.cell --> 

               <div class='table_small'>
                              
                   <div class='table_cell'>@lang('label.code') <span class="red">*</span></div>
                  <div class='table_cell'>
                    <input type="text" class="form-control input-sm" name="code[]" id="code" placeholder="" value="{{$code[$i] ?? '' }}">
                  </div>          
                                   
               </div><!-- /.cell -->

			   

			   <div class='table_small'>
                              
				<div class='table_cell'>@lang('label.lb22')</div>
			   <div class='table_cell'>
				 <input type="text" class="form-control input-sm" name="standard_qty[]" id="standard_qty" placeholder="" value="{{$standard_qty[$i] ?? '' }}">
			   </div>          
								
			</div><!-- /.cell -->

			<div class='table_small'>
                              
				<div class='table_cell'>Unit</div>
				<div class='table_cell'>
					<input type="text" class="form-control input-sm" name="unit[]" id="unit" placeholder="" value="{!!$unit[$i] ?? '' !!}">
				</div>          
								
			</div><!-- /.cell -->

			<div class='table_small'>
                              
				<div class='table_cell'>Color</div>
				<div class='table_cell'>
					<input type="text" class="form-control input-sm" name="color[]" id="color" placeholder="" value="{{$color[$i] ?? '' }}">
				</div>          
								
			</div><!-- /.cell -->

			<div class='table_small'>
                              
				<div class='table_cell'>Note</div>
				<div class='table_cell'>
					<input type="text" class="form-control input-sm" name="noted[]" id="noted" placeholder="" value="{{$noted[$i] ?? '' }}">
				</div>          
								
			</div><!-- /.cell -->


				<div class='table_small'>          
					<div class='table_cell'>@lang('ccms.ordering')</div>
					<div class='table_cell'>
					  <div class="input-group">
						<input type="text" name="ordering[]" id="ordering0" class="spinbox-input form-control input-sm text-center ordering0"​ value="{{ $ordering[$i] ?? '' }}">
					  </div> 
					</div>                     
				  </div><!-- /.cell --> 
      
                <div class='table_small'>          
                  <div class='table_cell'>&nbsp;</div>
                  <div class='table_cell'>
                    @if($i==0)
                      <button type="button" value="" class="btn btn-default btn-sm" style="height: 30px">&nbsp;</button>
                    @else
                      <button type="button" value="" class="btn btn-default btn-sm removeButton" style="height: 30px"><i class="fa fa-minus"></i></button>
                    @endif
                  </div>          
                                   
                </div><!-- /.cell --> 
      
               

			        </div><!-- /.row --> 

            @endfor
        </div><!-- /.table -->
	</div> <!-- /.col1 -->

	@if($submitto!='update')
	<div class="col-xs-12 col-sm-12 col-lg-12" style="text-align: right; padding-top: 10px">
		<button type="button" id="btnAdd" value="" class="btn btn-default btn-sm" style="height: 30px"><i class="fa fa-plus"></i></button>
	</div> <!-- /.col2 -->
	@endif

</div>
</form>


<!-- write for cloen-->
<div class='table_row hide' id='recordTemplate'>
	<div class='table_small'>
                              
                    <div class='table_cell'>@lang('label.title') <span class="red">*</span></div>
					<div class='table_cell'>
						<input type="hidden" name="{{$fprimarykey}}[]" id="{{$fprimarykey}}" value="">	
						<div class="input-group my-group" style="width:100%;"> 

	            			<select id="tab_title" class="form-control input-sm" style="width:25%;">
	                        @foreach (config('ccms.multilang') as $lang)
		                        <option value="@lang($lang[0])">@lang($lang[1])</option>
		                    @endforeach
	                        
	                    	</select>

	                    	@php ($active = '') @endphp
	                    	@foreach (config('ccms.multilang') as $lang)
	                    	<input type="text" class="form-control input-sm {{$active}}" style="width:75%;" name="title-{{$lang[0]}}[]" id="title-{{$lang[0]}}" placeholder="{{$lang[1]}}" value="">
	                    	@php ($active = 'hide') @endphp
	                    	@endforeach

	        			</div>
					</div>          
                                   
                </div><!-- /.cell --> 

                
   <div class='table_small'>                     
    <div class='table_cell'>@lang('label.code') <span class="red">*</span></div>
    <div class='table_cell'>
      <input type="text" class="form-control input-sm" name="code[]" id="code" placeholder="" value="">
    </div>                                    
  </div><!-- /.cell -->

  <div class='table_small'>                     
    <div class='table_cell'>@lang('label.lb22')</div>
    <div class='table_cell'>
      <input type="text" class="form-control input-sm" name="standard_qty[]" id="standard_qty" placeholder="" value="">
    </div>                                    
  </div><!-- /.cell -->

  <div class='table_small'>                     
    <div class='table_cell'>Unit</div>
    <div class='table_cell'>
      <input type="text" class="form-control input-sm" name="unit[]" id="unit" placeholder="" value="">
    </div>                                    
  </div><!-- /.cell -->


  <div class='table_small'>                     
    <div class='table_cell'>Color</div>
    <div class='table_cell'>
      <input type="text" class="form-control input-sm" name="color[]" id="color" placeholder="" value="">
    </div>                                    
  </div><!-- /.cell -->

  <div class='table_small'>                     
    <div class='table_cell'>Note</div>
    <div class='table_cell'>
      <input type="text" class="form-control input-sm" name="noted[]" id="noted" placeholder="" value="">
    </div>                                    
  </div><!-- /.cell -->
  

	<div class='table_small'>                       
					<div class='table_cell'>@lang('ccms.ordering')</div>
					  <div class='table_cell'>
						<div class="input-group">
						  <input type="text" name="ordering[]" id="ordering" class="spinbox-input form-control input-sm text-center" value="">
						</div> 
					</div>
				  </div><!-- /.cell -->

                
   <div class='table_small'>
                              
    <div class='table_cell'>&nbsp;</div>
    <div class='table_cell'>
      <button type="button" value="" class="btn btn-default btn-sm removeButton" style="height: 30px"><i class="fa fa-minus"></i></button>
    </div>          
                                   
  </div><!-- /.cell --> 

</div><!-- /.row -->

@stack('plugin')
	@if (!session('ajax_access'))
		@include('Filemanager.manager')
	@endif
@stop


								

