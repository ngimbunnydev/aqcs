@php $firstRow = $results[0]??[]; @endphp
<div class="row">
  <div class="col-sm-12">
    <div class="page-header" data-spy="affix" data-offset-top="60">
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-lg-6">
        <h1 style="color:#000099;">
            <i class="fas fa-folder-open"></i>
              @lang('label.lb220')
          <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            @lang('ccms.active')
          </small>
        </h1>
        </div>									
      </div>	


      </div>	
  </div>
  @if(!empty($firstRow))
  <div class="col-sm-9">
    <div class="d-flex">
      <div class="flex-scratch-item-title">
        <strong>@lang('label.lb224'):&nbsp;</strong>
      </div>
      <div class="flex-scratch-item-vlue">
        <span class="blue"><strong>{{ $firstRow->pd_id??'' }}</strong></span>
      </div>
    </div>
   <div class="d-flex">
      <div class="flex-scratch-item-title">
        <strong>@lang('label.barcode'):&nbsp;</strong> 
      </div>
      <div class="flex-scratch-item-vlue">
        <span class="blue"><strong>{{ $firstRow->barcode??'' }}</strong></span>
      </div>
    </div>
    
    <div class="d-flex" style="margin-bottom: 8px;">
      <div class="flex-scratch-item-title">
        <strong>@lang('label.lb226'):&nbsp;</strong> 
      </div>
      <div class="flex-scratch-item-vlue">
        <span class="blue"><strong>{{ $firstRow->title??'' }}</strong></span>
      </div>
    </div>
  
  </div>
  <div class="col-sm-3">
    <div class="d-flex">
      <div class="flex-scratch-item-title-1">
        <strong>@lang('label.lb225'):&nbsp;</strong> 
      </div>
      <div class="flex-scratch-item-vlue">
        @if(isset($firstRow->as_id))
        <span class="blue"><strong>{{config('sysconfig.addstock')}}{{ formatID($firstRow->as_id??0) }}</strong></span>
        @endif
      </div>
    </div>
    <div class="d-flex">
      <div class="flex-scratch-item-title-1">
        <strong>@lang('label.title'):&nbsp;</strong> 
      </div>
      <div class="flex-scratch-item-vlue">
        <span class="blue"><strong>{{ $firstRow->stock_title??'' }}</strong></span>
      </div>
    </div>
    <div class="d-flex"  style="margin-bottom: 8px;">
      <div class="flex-scratch-item-title-1">
        <strong>@lang('label.date'):&nbsp;</strong> 
      </div>
      <div class="flex-scratch-item-vlue">
        <span class="blue"><strong>{{ isset($firstRow->add_date) ? date('d/m/Y', strtotime($firstRow->add_date)): '' }}</strong></span>
      </div>
    </div>
  </div>
  @endif
</div>
<table class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th width="35">@lang('label.no')</th>
      <th class="blue" width="90">@lang('label.on')</th>
      <th class="orange" width="100">@lang('label.lb223')</th>
      <th width="100">@lang('label.lb64')</th>
      <th class="green" width="100">@lang('label.date')</th>
      <th width="90">@lang('label.lb21')</th>
    </tr>
  </thead>
  <tbody>
    @if($results->count())
      @php $runRow = 1; $grand_qty=0; @endphp
      @foreach($results as $row)
        @php 
          $qty = json_decode($row->qty, true);
          $grand_qty += array_values($qty)[0] ?? 0;
        @endphp
        <tr>
          <td>{{ $runRow }}</td>
          <td class="blue">{{ ucfirst($row->tracking_on) }}</td>
          <td class="orange">{{ $row->tracking_ref }}</td>
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
          <td class="green">{{ date('d/m/Y h:i:s A', strtotime($row->track_date)) }}</td>
          <td>{{ $row->username }}</td>
        </tr>
        @php $runRow++; @endphp
      @endforeach
      <tr>
        <td colspan="3">&nbsp;</td>
        <td>
          <span class="badge badge-yellow w100"><b style="color:green">{{ number_format($grand_qty, 2, '.', '') }}</b></span>
        </td>
        <td colspan="2">&nbsp;</td>
      </tr>
    @else
      <tr><td class="red" colspan="6">@lang('ccms.norecord')</td></tr>
    @endif
  </tbody>
</table>