@extends('backend.layout')

@section('header_import')
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datepicker3.min.css') }}" />
	<link rel="stylesheet" href="{{asset('resources/assets/arcetheme/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('footer_import')
	<script src="{{asset('resources/views/backend/lib/js/listener.js')}}"></script>
  <script src="{{asset('resources/views/backend/lib/js/localStorage.js')}}"></script>
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

  <!-- LocalStorage -->
  <script>
    // init global require vars AirWindow
    let $modalAirWindows = $("#modal_windows"),
        $modalAirWPageHeader = $("#listPageHeader"),
        $modalAirWTableHeader = $("#dynamic-table thead"),
        modalAirWContainerId = "#air_windows",
        storeObjName = 'StockTracking',
        columnAction = {title: '{{ __("label.action") }}', width: "50"};
    // init item count when load|reload page
    $(window).load(function() {
      initItemListCount("#countListItem", storeObjName);
    });
    
    /*
     * ========================= 
     * LocalStorage
     * =========================
     */
    $( document ).ready(function() {
    // load air window modal
     $("#btnLoadList").on('click', function(){
       $modalAirWindows.modal('show');
       $modalAirWindows.find('.modal-body').css({'overflow-y':'scroll','height': (window.innerHeight-150)+'px'});
       $modalAirWindows.find(modalAirWContainerId).html($modalAirWPageHeader.html()+getListTable(storeObjName, {noRecord: '{{ __('ccms.norecord') }}'}));
     });
    // reset air window modal content
     $modalAirWindows.on('hidden.bs.modal', function () {
       $(this).find(modalAirWContainerId).html('');
     });
    // add item to localStorage
    $('body').delegate('.btnAddBulkItems', 'click', function(){
      let self = $(this),
          $rows = $("#dynamic-table > tbody").find('.btnAddBulkItem').parents('tr');
        self.find('i').removeClass('fa-plus-circle').addClass('fa-spinner fa-spin'); 
        self.prop("disabled", true );
        setTimeout(function() {
          if($rows){
              $rows.each(function(){
                let setData = setListDataInfo($(this));

                if(isHasItem(setData.id, storeObjName)){
                  let ind = getItemIndexOf(setData.id);
                  if(ind > -1){
                     self.css({'border-color': '#F89406'});
                  }

                }else{
                  addNewItem(storeObjName, setData);
                }
              });
              // init item count number
              initItemListCount("#countListItem", storeObjName);
           }
           self.prop( "disabled", false );
           self.find('i').addClass('fa-plus-circle').removeClass('fa-spinner fa-spin');
          setTimeout(function() {
            self.css({'border-color': '#428BCA'});
          }, 500);
        }, 600);

      }).delegate('.btnAddBulkItem', 'click', function(){
          var self = $(this),
              $parent = self.parents('tr'),
              $templateData = $parent.find('.item-template-data');
          let setData = setListDataInfo($parent);
          self.find('i').removeClass('fa-plus-circle').addClass('fa-spinner fa-spin'); 
          self.prop("disabled", true );
          setTimeout(function() {
            if(isHasItem(setData.id, storeObjName)){
              let ind = getItemIndexOf(setData.id);
                  //object_data = getObject({objectName: storeObjName});
              if(ind >= 0){
                //setObject(object_data, storeObjName);
                self.css({'border-color': '#F89406'});
              }

            }else{
              addNewItem(storeObjName, setData);
              initItemListCount('#countListItem', storeObjName);
            }
              self.prop( "disabled", false );
              self.find('i').addClass('fa-plus-circle').removeClass('fa-spinner fa-spin');

            setTimeout(function() {
              self.css({'border-color': '#428BCA'});
            }, 500);

          }, 600);

     }).delegate('.psws-remove-table-tr', 'click', function(){
        let self = $(this),
            $parent = $(this).parents('tr');
        var item = parseInt($(this).attr('data-index'));
        $parent.css({"background-color":"#ececec", "transaction":".3s"});
        self.find('i').removeClass('fa-trash').addClass('fa-spinner fa-spin'); 
        self.prop("disabled", true );

        setTimeout(function(){
            let isRemoved = getRemoveObjectItem(storeObjName, {
                index: item
            });
          if(isRemoved){
            initItemListCount("#countListItem", storeObjName);
            $modalAirWindows.find("#listTBody").html(getListTableRow(storeObjName, {noRecord: '{{ __('ccms.norecord') }}'}));
            $parent.fadeOut( "slow" );
          }
          self.prop( "disabled", false );
          self.find('i').addClass('fa-trash').removeClass('fa-spinner fa-spin');
        }, 600);
     }).delegate('#btnClearAll', 'click', function(){
        let self = $(this);
        self.find('i').removeClass('fa-trash').addClass('fa-spinner fa-spin'); 
        self.prop("disabled", true );
        setTimeout(function() {
          setObject([], storeObjName);
          initItemListCount();
          $modalAirWindows.find("#listTBody").html(getListTableRow(storeObjName, {noRecord: '{{ __('ccms.norecord') }}'}));
          self.prop( "disabled", false );
          self.find('i').addClass('fa-trash').removeClass('fa-spinner fa-spin');
        }, 600);
     }).delegate('#btnExportToExcel', 'click', function(){
        var form = document.createElement("form");
        var elToken = document.createElement("input"),
            elExportType = document.createElement("input"); 

        form.method = "POST";
        form.action = "{{ url_builder($obj_info['routing'],[$obj_info['name'],'ptoexcel']) }}";   

        elToken.value='{{ csrf_token() }}';
        elToken.name="_token";
        elToken.type="hidden";
        form.appendChild(elToken);

        let listData = getObject({objectName: storeObjName});

        if(listData){
          if(listData.length>0){
            listData.forEach(function(item){
              let elInputId = document.createElement("input");
              // ID
              elInputId.name = "id[]";
              elInputId.type = "hidden";
              elInputId.value = item.id;
              form.appendChild(elInputId);
            });
          }
        }
        document.body.appendChild(form);
        form.submit();
      });
      /* export */
      $(".btnb2excel").on('click', function(e){
        e.preventDefault();
        let exportType = $(this).data('export-type'),
            bulkId = [],
            $bulkItems = $(".item-template-data");
          $bulkItems.each(function(){
            bulkId.push($(this).data('id'));
          });
        //set text
        $("#b2excel-text").text($(this).text());
        var form = document.createElement("form");
        var elBulkId = document.createElement("input"); 
        var elExportType = document.createElement("input"); 
        var elToken = document.createElement("input");
        var elTitle = document.createElement("input"),
            elFromDate = document.createElement("input"),
            elToDate = document.createElement("input");

        form.method = "POST";
        form.action = "{{ url_builder($obj_info['routing'],[$obj_info['name'],'ptoexcel']) }}";   

        elToken.value='{{ csrf_token() }}';
        elToken.name="_token";
        elToken.type="hidden";
        form.appendChild(elToken); 

        elBulkId.type="hidden";
        elBulkId.value=bulkId;
        elBulkId.name="id";
        form.appendChild(elBulkId);  

        elExportType.type="hidden";
        elExportType.value=exportType;
        elExportType.name="exportType";
        form.appendChild(elExportType);
        // title
        elTitle.type="hidden";
        elTitle.value=getUrlParameter('title');
        elTitle.name="title";
        form.appendChild(elTitle);
        //from date
        elFromDate.type="hidden";
        elFromDate.value=getUrlParameter('fromdate');
        elFromDate.name="fromdate";
        form.appendChild(elFromDate);
        //to date
        elToDate.type="hidden";
        elToDate.value=getUrlParameter('todate');
        elToDate.name="todate";
        form.appendChild(elToDate);

        document.body.appendChild(form);

        form.submit();
      });
  });
    
    /*
     * ========================
     * LocalStorage Functions
     * ========================
     */

    // get list table
    function getListTable(objectName, options = {}){
      // init default options
      var default_options = {
        noRecord: 'No record found!'
      };
      //var data = getObject({objectName: objectName});
      // Merge default options with options
      $.extend(default_options, options);

      var table = '';
      table += '<table class="table table-striped table-bordered table-hover">';
      table += 	'<thead id="listTHead">';
      table += getListTableHeader();
      table += 	'</thead>';

      table += 	'<tbody id="listTBody">';
      table +=    getListTableRow(objectName, {noRecord: '{{ __('ccms.norecord') }}'});
      table += 	'</tbody>';
      return table;
    }
    // get list table header
  function getListTableHeader(){
    let $header = $modalAirWTableHeader.find('th').not('.except-column').clone(),
        $headerArr = [],
        $headerObjStr,
        $drawHeader = [];
    $header.each(function(){
      let text = $(this).text(),
          width = $(this).attr('width');
      if(!width){
        width = "";
      }
      text = text.trim().replace(/\s\s+/g, ' ');
      $headerObjStr = {title: text, width: width};
      $headerArr.push($headerObjStr);
    });
    $headerArr.push(columnAction);
    $headerArr.forEach(function(el){
      let w = el.width;
      if(w){
        w = 'width="'+w+'"';
      }
      $drawHeader.push('<th '+w+'>'+el.title+'</th>');
    });
    return '<tr>'+$drawHeader.join("")+'</tr>';
  }
    // get list items table row
    function getListTableRow(objectName, options = {}){
      // init default options
      var default_options = {
        noRecord: 'No record found!'
      };
      var data = getObject({objectName: objectName});
      // Merge default options with options
      $.extend(default_options, options);

      let table = '';
      if(data !="" && data != null){
        let runRow = 1;
        if(data.length){
          let grandQty = 0;
          for(var i = 0; i < data.length; i++){
            grandQty += parseFloat(data[i].quantity);
            let actionHtml = '';			
            actionHtml += '<td>';
            actionHtml += 				'<div class="btn-group">';
            actionHtml += 					'<button class="btn btn-xs btn-danger psws-remove-table-tr" data-index="'+ i +'"><i class="ace-icon fa fa-trash bigger-120"></i></button>';
            actionHtml += 				'</div>';
            actionHtml += 			'</td>';
            table += 		'<tr>'+'<td>'+runRow+'</td>'+data[i].html+actionHtml+'</tr>';
            runRow++;
          }
          table += '<tr>';
          table += '<td colspan="7">&nbsp;</td>';
          table += '<td><span class="badge badge-yellow w100 green"><strong>'+grandQty+'</strong></span></td>';
          table += '<td colspan="2">&nbsp;</td>';
          table += '</tr>';
        }
      }else{
        table += 		'<tr><td colspan="15"><p class="red"><i class="fa fa-exclamation-circle"></i>&nbsp;'+ default_options.noRecord +'</p></td></tr>';
      }
      return table;
    }
    // set list data info
    function setListDataInfo(rawData){
      let item = rawData.clone(),
          tmpData = rawData.find('.item-template-data');
      item.find('.except-column').remove();
      let data = {
        id: tmpData.data('id'),
        productId: tmpData.data('product-id'),
        title: tmpData.data('title'),
        quantity: tmpData.data('quantity'),
        html: item.html()
      };
      return data;
    }
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
 <!-- templates -->
  <template id="listPageHeader">
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
                {{ trans('label.list') }}
              </small>
            </h1>
          </div>
          <div class="col-sm-6">		
            <div class="wizard-actions">

                <button class="btn btn-white btn-success btn-bold btn-sm" id="btnExportToExcel">
                  <i class="ace-icon fa fa-file-excel bigger-120 green"></i><br>
                  @lang('label.export')
                </button>
                <button class="btn btn-white btn-danger btn-bold btn-sm" id="btnClearAll">
                  <i class="ace-icon fa fa-trash bigger-120"></i><br>
                  @lang('label.lb207')
                </button>

            </div>
          </div>									
        </div>									
      </div>
   </template>
	<!--DRAW Content -->
	@php
		$querytitle=url_builder($obj_info['routing'],[$obj_info['name'],'index'],array_merge(['sort=title'], $querystr));
	@endphp
	<div class="row">
      <form action="" method="get" id="filter">
        <div class="form-row">

          <div class="form-group col-md-3">
            <label class="frm-label" for="title">@lang('label.search')</label>
            <input type="text" class="form-control" id="title" name="title" value="{{request()->get('title')}}">
          </div>
          <div class="form-group col-md-2">
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

						    <div class="form-group col-md-2">
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
          <div class="form-group col-md-3 text-right">
            <label style="display:block;">&nbsp;</label>
            @include('backend.widget.btnexport', [])
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
                  <th class="center except-column" width="35" style="padding: 8px 0px;">
                    <button class="btn btn-minier btn-primary btnAddBulkItems" title="{{ trans('label.lb212') }}" data-rel="tooltip" data-placement="bottom"><i class="fa fa-plus-circle"></i>&nbsp;{{ str_plural(trans('label.add')) }}</button>
                  </th>
                  
                  <th width="130">
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
                  <th class="orange" width="70">@lang('label.on')</th>
                  <th class="green" width="80">@lang('label.lb223')</th>
                  <th class="blue hidden-480" width="35">@lang('label.id')</th>
                  <th class="blue hidden-480" width="70">@lang('label.barcode')</th>
                  <th width="150">
                    {!!
                      orderMenu(
                      [	'caption'=>__('label.lb70'),
                        'sort'=>'title', 
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
                  
                  
                  <th width="120">@lang('label.lb64')</th>
                  <th width="90 hidden-480">@lang('label.lb21')</th>
                  
                </tr>
              </thead>
              <tbody>
             @if($results->count())
              @php $runRow = 1; $grand_qty=0; @endphp
              @foreach ($results as $row)
                @php
                  $qty = json_decode($row->qty, true);
                  $qty_amount = array_values($qty)[0] ?? 0;
                  $grand_qty += $qty_amount;
                  $hili='';
                  if((int)session('id')==(int)$row->id) $hili = "style='background-color: #ffffdd'";
                @endphp
                    <tr {!!$hili!!}>
                      <td class="except-column">
                        {{ $runRow }}
                      </td>
                      <td class="center except-column">
                        <button class="btn btn-minier btn-primary btnAddBulkItem" title="{{ trans('label.lb211') }}" data-rel="tooltip" data-placement="top"><i class="fa fa-plus-circle"></i>&nbsp;@lang('label.add')</button>
                      </td>
                      
                      <td class="blue bold">
                        {{ date('d/m/Y h:i:s A', strtotime($row->track_date)) }}
                      </td>
                      <td class="orange bold">
                        {{ ucfirst($row->tracking_on) }}
                      </td>
                      <td class="green">
                        {{ $row->tracking_ref }}
                      </td>
                      <td class="blue hidden-480">
                        {{ $row->pd_id }}
                      </td>
                      <td class="blue hidden-480">
                        {{ $row->barcode }}
                      </td>
                      <td class="blue bold">
                        {{ $row->title }}
                      </td>
                      <td>
                        @include('backend.vproduct.sizecolorinfo',
                            [
                            'qty' => $qty,
                            'allsizes' => $allsizes, 
                            'allcolors' => $allcolors, 
                            'unit'=> ''
                            ]

                        )
                      </td>
                      <td class="hidden-480">
                        {{ $row->username }}
                      </td>
                      <!-- template data item -->
                      <template class="item-template-data" id="templateId{{$row->id}}" 
                              data-id="{{ $row->id }}"
                              data-product-id="{{ $row->pd_id }}"
                              data-title="{{ $row->title }}"
                              data-quantity="{{ $qty_amount }}"></template>
                      <!-- end template data item -->
                    </tr>
                  @php $runRow++; @endphp
                 @endforeach
                 <tr>
                  <td colspan="8">&nbsp;</td>
                  <td>
                    <span class="badge badge-yellow w100"><b style="color:green">{{ number_format($grand_qty, 2, '.', '') }}</b></span>
                  </td>
                  <td>&nbsp;</td>
                </tr>
                @else
                  <tr><td class="red bold" colspan="9"><i class="fas fa-info-circle"></i>&nbsp;{{ __('ccms.norecord') }}</td></tr>
                @endif
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


								

