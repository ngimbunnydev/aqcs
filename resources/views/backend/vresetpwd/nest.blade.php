<ol class='dd-list'>
	        @foreach($elements as $element)
	            @if (!empty($element['children']))
	                <li class='dd-item dd2-item' data-id='{{$element['m_id']}}'>
	                <div class='dd-handle dd2-handle'>
	                	<i class="normal-icon ace-icon fa fa-arrows blue bigger-120"></i>
						<i class="drag-icon ace-icon fa fa-arrows bigger-120"></i>
	                </div>

	                <div class="dd2-content">
	                		<span id="content{{$element['m_id']}}">{{$element['title']}}</span>
	                		<div class="pull-right action-buttons">
	                			<a class='blue editmenu' href='#' data-menu='{{$element['m_id']}}'>
                                        <i class='ace-icon fa fa-pencil bigger-130'></i>
                                    </a>

                                    <a class='red' href='{{url_builder($obj_info['routing'],
											[$obj_info['name'],'remove',$element['m_id']],
											[]
										)}}'>
                                        <i class='ace-icon fa fa-times-circle bigger-130'></i>
                                    </a>
	                		</div>
	                	</div>

	                @php
	                	$elements=$element['children']
	                @endphp
	                @include('backend.vmenus.nest', $elements)
	                </li>
	            @else
	                <li class='dd-item dd2-item' data-id='{{$element['m_id']}}'>
	                	<div class='dd-handle dd2-handle'>
	                		<i class="normal-icon ace-icon fa fa-arrows blue bigger-120"></i>
							<i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
	                	</div>
	                	<div class="dd2-content">
	                		<span id="content{{$element['m_id']}}">{{$element['title']}}</span>
	                		<div class="pull-right action-buttons">
	                			<a class='blue editmenu' href='#' data-menu='{{$element['m_id']}}'>
                                        <i class='ace-icon fa fa-pencil bigger-130'></i>
                                    </a>

                                    <a class='red' href='{{url_builder($obj_info['routing'],
											[$obj_info['name'],'remove',$element['m_id']],
											[]
										)}}'​>
                                        <i class='ace-icon fa fa-times-circle bigger-130'></i>
                                    </a>
	                		</div>
	                	</div>

	                </li>
	            @endif
	        @endforeach
</ol>
