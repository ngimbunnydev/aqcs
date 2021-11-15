<ol class='dd-list'>
	        @foreach($elements as $element)
	        	@php
					$hili='';									
					if((int)session('id')==(int)$element['id']) $hili = "style='background-color: #ffffdd'";
				@endphp

	            @if (!empty($element['children']))
	                <li class='dd-item dd2-item' data-id='{{$element['id']}}'>
	                <div class='dd-handle dd2-handle'>
	                	<i class="normal-icon ace-icon fa fa-arrows blue bigger-120"></i>
						<i class="drag-icon ace-icon fa fa-arrows bigger-120"></i>
	                </div>

	                <div class="dd2-content" {!!$hili!!}>
                      
                      @if($element['display']=='no')
                      <span class="red">
                        {{$element['title']}}&nbsp;({{$element['id']}})
                      </span>
                      @else
                      {{$element['title']}}&nbsp;({{$element['id']}})
                      @endif
                      
	                		
	                		<div class="pull-right action-buttons">
	                		@include('backend.widget.actmenu',['rowid'=>$element['id'], 'btnedit' => 'yes', 'btnduplicate' => 'yes', 'btndelete' => 'yes','btnrestore' => 'yes','btndestroy' => 'yes', 'delete_cfm' => 'yes'])
	                		</div>
	                	</div>

	                @php
	                	$elements=$element['children']
	                @endphp
	                @include('backend.vpcategory.nest', $elements)
	                </li>
	            @else
	                <li class='dd-item dd2-item' data-id='{{$element['id']}}'>
	                	<div class='dd-handle dd2-handle'>
	                		<i class="normal-icon ace-icon fa fa-arrows blue bigger-120"></i>
							<i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
	                	</div>
	                	<div class="dd2-content" {!!$hili!!}>
                      
	                		
                      
                      @if($element['display']=='no')
                      <span class="red">
                      {!!$element['title']!!}&nbsp;({{$element['id']}})
                      </span>
                      @else
                      {!!$element['title']!!}&nbsp;({{$element['id']}})
                      @endif
	                		<div class="pull-right action-buttons">
	                		@include('backend.widget.actmenu',['rowid'=>$element['id'], 'btnedit' => 'yes', 'btnduplicate' => 'yes', 'btndelete' => 'yes','btnrestore' => 'yes','btndestroy' => 'yes', 'delete_cfm' => 'yes'])
	                		</div>
	                	</div>

	                </li>
	            @endif
	        @endforeach
</ol>