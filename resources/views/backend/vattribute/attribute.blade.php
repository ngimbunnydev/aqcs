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
	<div class="row" >
		<div class="col-xs-12 col-sm-12 col-lg-12 form-group">
			<!-- <div class="controls form-inline"> -->
				<label class="red">{{$row['title']}}</label>
	@switch($row['attribute'])
    @case(1)
		        <input type="text" class="form-control input-sm" name="attr-{{$row['id']}}" id="attr-{{$row['id']}}" value="{{ ${'attr-'.$row['id']} or '' }}">
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
	                    	<input type="text" class="form-control input-sm {{$active}}" style="width:80%;" name="attr-{{$row['id']}}-{{$lang[0]}}" id="attr-{{$row['id']}}-{{$lang[0]}}" placeholder="{{$lang[1]}}" value="{{ ${'attr-'.$row['id'].'-'.$lang[0]} or '' }}">
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

					$datalist=$datalists->getdatalist($row['dl_id'], 'en')->pluck('title', 'dl_id');
					$select = isset(${'attr-'.$row['id']}) ? ${'attr-'.$row['id']} : [];
					if($datalist) echo cmb_listing($datalist, $select,'','')
					
				@endphp									            	
		</select>
		
        @break

@endswitch

			<!-- </div> -->
		</div>
	</div> <!--/. Row -->
														
														
																						
														
@endforeach


