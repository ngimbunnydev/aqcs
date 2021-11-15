@extends('backend.layout')

@section('header_import')
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
	<script src="{{asset('/resources/assets/arcetheme/js/moment.min.js')}}"></script>
  <script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('/resources/assets/arcetheme/js/bootstrap-datetimepicker.min.js')}}"></script>
  <script type="text/javascript">
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
@stop	


@section('app')
	<div class="page-header" data-spy="affix" data-offset-top="60">
		<div class="row">
			<div class="col-sm-6">
			<h1>
				{!! $obj_info['icon'] !!}
				<a href="{{url_builder($obj_info['routing'],[$obj_info['name'],'index'])}}">
					{!!$obj_info['title']!!}
				</a>
				<small>
					<i class="ace-icon fa fa-angle-double-right"></i>
					{{$caption}}
				</small>
			</h1>
			</div>
			<div class="col-sm-6">
				@include('backend.widget.btnav', ['btnnew' => 'no', 'btntrash' => 'no', 'btnactive' => 'no'])			
			</div>									
		</div>									
											
	</div>						
	<!-- /...........................................................page-header -->
 
	<!--DRAW Content -->
	@php
		$querytitle=url_builder($obj_info['routing'],[$obj_info['name'],'index'],array_merge(['sort=title'], $querystr));
	@endphp
	<div class="row">
      <form action="" method="get" id="filter">
        <div class="form-row">

          <div class="form-group col-md-4">
            <label class="frm-label" for="title">@lang('label.search')</label>
            <input type="text" class="form-control" id="title" name="title" value="{{request()->get('title')}}">
          </div>
          <div class="form-group col-md-3">
						      	<!-- *** -->
						      	<label class="frm-label" for="fromdate">@lang('label.fdate') (@lang('label.d-m-y'))</label>
				
								<div class="input-group">
									<input class="form-control date-picker" name="fromdate" id="fromdate" type="text" data-date-format="dd-mm-yyyy" value="{{request()->get('fromdate')}}">
										<span class="input-group-addon">
											<i class="fa fa-calendar bigger-110"></i>
										</span>
								</div>
						      	<!-- **** -->
						    </div>

						    <div class="form-group col-md-3">
						      	<!-- *** -->
						      	<label class="frm-label" for="todate">@lang('label.tdate') (@lang('label.d-m-y'))</label>
				
								<div class="input-group">
									<input class="form-control date-picker" name="todate" id="todate" type="text" data-date-format="dd-mm-yyyy" value="{{request()->get('todate')}}">
										<span class="input-group-addon">
											<i class="fa fa-calendar bigger-110"></i>
										</span>
								</div>
						      	<!-- **** -->
						    </div>
          <div class="form-group col-md-1">
            <label>&nbsp;</label>
            <button class="form-control btn btn-default" type="submit" value="filter">
                <i class="fa fa-search"></i>
            </button>
          </div>

          <div class="form-group col-md-1">
             <label>&nbsp;</label>
             <button class="form-control btn btn-default" type="button" onclick="location.href='{{url()->current()}}'">
                  @lang('label.reset')
             </button>
          </div>
       </div>
       <!--/-->
    </form>
			
    <div class="col-xs-12">
      <!-- PAGE CONTENT BEGINS -->
      <div class="row">
        <div class="col-xs-12">
          <table id="dynamic-table" class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  
                  <th width="35">@lang('label.no')</th>
                  <th width="180">
                    {!!
                      orderMenu(
                      [	'caption'=>__('label.date'),
                        'sort'=>'track_date', 
                        'current_sort'=>$sort, 
                        'mdefault'=>'asc', 
                        'method'=>$order, 
                        'act'=>$act
                      ],
                      $querystr,
                      $perpage_query, 
                      $obj_info)
                    !!}
                  </th>
                  <th width="150">
                    {!!
                      orderMenu(
                      [	'caption'=>__('label.lb21'),
                        'sort'=>'name', 
                        'current_sort'=>$sort, 
                        'mdefault'=>'asc', 
                        'method'=>$order, 
                        'act'=>$act
                      ],
                      $querystr,
                      $perpage_query, 
                      $obj_info)
                    !!}
                  </th>
                  <th width="150">@lang('label.ip')</th>
                  <th width="120">@lang('label.on')</th>
                  <th width="100">@lang('label.action')</th>
                  <th width="90">@lang('label.id')</th>
                  
                </tr>
              </thead>
              <tbody>
              @php $runRow = 1; @endphp
              @foreach ($results as $row)
                @php
                  $hili='';
                  
                  if((int)session('id')==(int)$row->id) $hili = "style='background-color: #ffffdd'";
                @endphp
                <tr {!!$hili!!}>    
                  <td>
                    {{ $runRow }}
                  </td>
                  <td class="blue bold">
                    {{ date('d/m/Y h:i:s A', strtotime($row->track_date)) }}
                  </td>
                  <td class="green bold">
                    {{ $row->username }}
                  </td>
                  <td class="orange bold">
                    {{ $row->ip }}
                  </td>
                  <td>
                    {{ ucfirst($row->track_obj) }}
                  </td>
                  <td>
                    {{ $row->action }}
                  </td>
                  <td>
                    {{ empty($row->obj_id)? '': $row->obj_id }}
                  </td>
                  
                </tr>
                  @php $runRow++; @endphp
                 @endforeach
            </tbody>
          </table>

          <!-- Pagination and Record info -->
            @include('backend.widget.pagination')

          <!-- /. end -->
        </div>
      </div>
    </div>
</div>
<!--/. draw content -->

@stop


								

