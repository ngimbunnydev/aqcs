@if (session('att_data'))
	@php 
		$inputdata=session('att_data'); 
		Session::forget('att_data');
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

@foreach ($results as $row)
	@php 
		$required='';
	@endphp
	@if(stripos($row['validator'], 'required')!==false)
		@php 
		$required='<span class="red">*</span>';
		@endphp
	@endif
	<div class="row" >
		<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
			<!-- <div class="controls form-inline"> -->
				<label class="blue">{{$row['title']}}{!!$required!!}</label> <small class="grey"><i>{{$row['placeholder']}}</i></small>

	@switch($row['attribute'])
    @case(1)
		        <input type="text" class="form-control input-sm" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}" placeholder="{{$row['placeholder']}}" value="{{ ${'attr-'.$row['id']} ?? '' }}">
        @break

    @case(2)
        <div class="input-group" style="width:100%;"> 

	            			<select id="tab_title{{$row['id']}}" class="form-control input-sm" style="width:20%;">
	                        @foreach (config('ccms.multilang') as $lang)
		                        <option value="@lang($lang[0])">@lang($lang[1])</option>
		                    @endforeach
	                        
	                    	</select>

	                    	@php ($active = '') @endphp
	                    	@foreach (config('ccms.multilang') as $lang)
	                    	<input type="text" class="form-control input-sm {{$active}}" style="width:80%;" name="attr-{{$row['id']}}-{{$lang[0]}}" id="attr-{{$row['id']}}-{{$lang[0]}}" placeholder="{{$lang[1]}}-{{$row['placeholder']}}" value="{{ ${'attr-'.$row['id'].'-'.$lang[0]} ?? '' }}">
	                    	@php ($active = 'hide') @endphp
	                    	@endforeach

	        			</div>

	    <script>
			$(document).on("change", "#tab_title{{$row['id']}}", function (ev) {
				    ///
				    	var $value=$(this).val();
			  			enableDisableByLang($(this),{!!json_encode($langcode,true)!!},'attr-{{$row['id']}}-',$value)
				    ///
				});
		 </script>
        @break

    @case(3)
    	<br>
        <select name="attr-{{$row['id']}}" id="">
			<option value="-1">-- {{__('ccms.ps')}} --</option>
			@php

					$datalist=$datalists->getdatalist($row['dl_id'], 'en')->pluck('title', 'dl_id');
					
					if($datalist) echo cmb_listing($datalist,[${'attr-'.$row['id']} ?? ''],'','')
					
				@endphp										            	
		</select>
		
        @break

    @case(4)

    	<script>
			$( document ).ready(function() {
				$('#attr-{{$row['id']}}').multiselect();
				
			});
  		</script>
  		<br>
        <select name="attr-{{$row['id']}}[]" id="attr-{{$row['id']}}" multiple="multiple">
				@php

					$datalist=$datalists->getdatalist($row['dl_id'], config('ccms.multilang')[0][0])->pluck('title', 'dl_id');
					$select = isset(${'attr-'.$row['id']}) ? ${'attr-'.$row['id']} : [];
					if(!is_array($select)) $select = explode(',',$select);
					if($datalist) echo cmb_listing($datalist, $select,'','')
					
				@endphp									            	
		</select>
		
        @break

    @case(5)

    	<textarea class="form-control" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}">{{${'attr-'.$row['id']} ?? ''}}</textarea>
		
        @break


    @case(6)

    	<div class="tabbable">
			<ul class="nav nav-tabs" id="myTab">
				@php ($active = 'active') @endphp
				@foreach (config('ccms.multilang') as $lang)
					<li class="{{$active}}">
						<a data-toggle="tab" href="#tab-{{$row['id']}}-{{$lang[0]}}" aria-expanded="false">
							@lang('ccms.'.$lang[0])
						</a>
					</li>
					@php ($active = '') @endphp
				@endforeach
			</ul>

			<div class="tab-content">
				<!-- Multi Languages -->
				@php ($active = 'active in') @endphp
				@foreach (config('ccms.multilang') as $lang)
					<div id="tab-{{$row['id']}}-{{$lang[0]}}" class="tab-pane fade {{$active}}">
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-lg-12">
																
								<textarea class="form-control" name="attr-{{$row['id']}}-{{$lang[0]}}" id="attr-{{$row['id']}}-{{$lang[0]}}">{{ ${'attr-'.$row['id'].'-'.$lang[0]} ?? '' }}</textarea>
																
							</div>
						</div>
					</div>
					@php ($active = '') @endphp
				@endforeach		

			<!-- *************** --></div>
		</div><!--/. Tab -->
		
        @break

    @case(7)
    	<div class="input-group">
			<input type="text" class="form-control input-sm date-picker" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}" value="{{ ${'attr-'.$row['id']} ?? '' }}" data-date-format="dd-mm-yyyy">
			<span class="input-group-addon">
				<i class="fa fa-calendar bigger-110"></i>
			</span>
		</div>
		<script>
			$( document ).ready(function() {


					$('.date-picker').datepicker({
						autoclose: true,
						todayHighlight: true
					})
					//show datepicker when clicking on the icon
					.next().on(ace.click_event, function(){
						$(this).prev().focus();
					});

			});
		</script>
    	@break

    @case(8)
    	<div class="input-group">
			<input type="text" class="form-control input-sm date-timepicker" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}" value="{{ ${'attr-'.$row['id']} ?? '' }}">
			<span class="input-group-addon">
				<i class="fa fa-calendar bigger-110"></i>
			</span>
		</div>
		<script>
			$( document ).ready(function() {


					if(!ace.vars['old_ie']) $('.date-timepicker').datetimepicker({
				 //format: 'MM/DD/YYYY h:mm:ss A',//use this option to display seconds
				 icons: {
					time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-arrows ',
					clear: 'fa fa-trash',
					close: 'fa fa-times'
				 }
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});

			});
		</script>
    	@break

    @case(9)

    	<div class="input-group">
                                <input class="form-control input-sm" type="text" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}" value="{{ ${'attr-'.$row['id']} ?? '' }}" readonly="readonly" ondblclick="this.value=''"/>
                                <span class="input-group-btn">
        
                                    <button id="btn-browe-image" class="btn btn-default btn-sm" type="button" style="height: 30px" data-giventtextbox="attr-{{$row['id']}}">
                                                Browse...
                                    </button>
                                </span>
                            </div>
		
        @break

    @case(10)

    	<div class="input-group" style="width:100%;"> 

	            			<select id="tab_title{{$row['id']}}" class="form-control input-sm" style="width:20%;">
	                        @foreach (config('ccms.multilang') as $lang)
		                        <option value="@lang($lang[0])">@lang($lang[1])</option>
		                    @endforeach
	                        
	                    	</select>

	                    	@php ($active = '') @endphp
	                    	@foreach (config('ccms.multilang') as $lang)
	                    	
	                    	<div class="input-group {{$active}}" style="width:80%;" id="ctnattr-{{$row['id']}}-{{$lang[0]}}"> 
	                    		<input type="text" class="form-control input-sm" name="attr-{{$row['id']}}-{{$lang[0]}}" id="attr-{{$row['id']}}-{{$lang[0]}}" placeholder="{{$lang[1]}}" value="{{ ${'attr-'.$row['id'].'-'.$lang[0]} ?? '' }}" readonly="readonly" ondblclick="this.value=''">
	                    		<span class="input-group-btn">
        
                                    <button id="btn-browe-image" class="btn btn-default btn-sm" type="button" style="height: 30px;" data-giventtextbox="attr-{{$row['id']}}-{{$lang[0]}}">
                                                Browse...
                                    </button>
                                </span>

	                    	</div>
	                    		

	                    	
	                    	
	                    	@php ($active = 'hide') @endphp
	                    	@endforeach

	        			</div>

	    <script>
			$(document).on("change", "#tab_title{{$row['id']}}", function (ev) {
				    ///
				    	var $value=$(this).val();
			  			enableDisableByLang($(this),{!!json_encode($langcode,true)!!},'ctnattr-{{$row['id']}}-',$value)
				    ///
				});
		 </script>
		
        @break


        @case(11)

    	<div class="input-group">
                                <input class="form-control input-sm" type="text" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}" value="{{ ${'attr-'.$row['id']} ?? '' }}" readonly="readonly" ondblclick="this.value=''"/>
                                <span class="input-group-btn">
        
                                    <button id="btn-browe-file" class="btn btn-default btn-sm" type="button" style="height: 30px" data-giventtextbox="attr-{{$row['id']}}">
                                                Browse...
                                    </button>
                                </span>
                            </div>
		
        @break

        @case(12)

    	<div class="input-group" style="width:100%;"> 

	            			<select id="tab_title{{$row['id']}}" class="form-control input-sm" style="width:20%;">
	                        @foreach (config('ccms.multilang') as $lang)
		                        <option value="@lang($lang[0])">@lang($lang[1])</option>
		                    @endforeach
	                        
	                    	</select>

	                    	@php ($active = '') @endphp
	                    	@foreach (config('ccms.multilang') as $lang)
	                    	
	                    	<div class="input-group {{$active}}" style="width:80%;" id="ctnattr-{{$row['id']}}-{{$lang[0]}}"> 
	                    		<input type="text" class="form-control input-sm" name="attr-{{$row['id']}}-{{$lang[0]}}" id="attr-{{$row['id']}}-{{$lang[0]}}" placeholder="{{$lang[1]}}" value="{{ ${'attr-'.$row['id'].'-'.$lang[0]} ?? '' }}" readonly="readonly" ondblclick="this.value=''">
	                    		<span class="input-group-btn">
        
                                    <button id="btn-browe-file" class="btn btn-default btn-sm" type="button" style="height: 30px;" data-giventtextbox="attr-{{$row['id']}}-{{$lang[0]}}">
                                                Browse...
                                    </button>
                                </span>

	                    	</div>
	                    		

	                    	
	                    	
	                    	@php ($active = 'hide') @endphp
	                    	@endforeach

	        			</div>

	    <script>
			$(document).on("change", "#tab_title{{$row['id']}}", function (ev) {
				    ///
				    	var $value=$(this).val();
			  			enableDisableByLang($(this),{!!json_encode($langcode,true)!!},'ctnattr-{{$row['id']}}-',$value)
				    ///
				});
		 </script>
		
        @break

@endswitch

			<!-- </div> -->
		</div>
	</div> <!--/. Row -->
														
														
																						
														
@endforeach


