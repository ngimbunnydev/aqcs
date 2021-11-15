@foreach($element['children'] as $list)
																			           
																			                <li class='dd-item dd2-item' data-id='{{$list['id']}}'>
																			                <div class='dd-handle dd2-handle'>
																			                	<i class="normal-icon ace-icon fa fa-arrows blue bigger-120"></i>
																								<i class="drag-icon ace-icon fa fa-arrows bigger-120"></i>
																			                </div>

																			                <div class="dd2-content">
																			                		{{$list['title']}} ({{$list['id']}})
																			                		<div class="pull-right action-buttons">
																			                		@include('backend.widget.actmenu',['rowid'=>$list['id'], 'btnedit' => 'yes', 'btnduplicate' => 'yes', 'btndelete' => 'yes','btnrestore' => 'yes','btndestroy' => 'yes',  'delete_cfm' => 'yes', 'destroy_cfm' => 'yes'])
																			                		</div>
																			                </li>
																			        @endforeach