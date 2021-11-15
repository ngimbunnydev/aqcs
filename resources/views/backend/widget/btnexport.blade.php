@php 
  $btnList = (isset($btnList)) ? $btnList:true;
  $btnExport = (isset($btnExport)) ? $btnExport:true;
  $btnExpCurrentPage = (isset($btnExpCurrentPage)) ? $btnExpCurrentPage:true;
  $btnExpAllFilterPage = (isset($btnExpAllFilterPage)) ? $btnExpAllFilterPage:true;
  $btnExpAll = (isset($btnExpAll)) ? $btnExpAll:true;
  $textTransAll = (isset($textTransAll))?$textTransAll:'label.lb210';
@endphp
@if($btnList)
<button type="button" class="btn btn-primary btn-xs" id="btnLoadList">
  <i class="fa fa-list-ol"></i>&nbsp; @lang('label.list') <span id="countListItem" class="badge badge-warning">0</span>
</button>
@endif
@if($btnExport)
<div class="btn-group">
      <button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle" style="padding: 4.5px 5px;" aria-expanded="false">
         <i class="fas fa-file-export"></i>
        <span id="b2excel-text">@lang('label.export')</span>
        <i class="ace-icon fa fa-angle-down icon-on-right"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-right">
        @if($btnExpCurrentPage)
        <li>
          <a href="#" class="btnb2excel" id="b2excel-current-page" data-export-type="current-page">@lang('label.lb208')</a>
        </li>
        @endif
        @if($btnExpAllFilterPage)
        <li>
          <a href="#" class="btnb2excel" id="b2excel-all-current-page" data-export-type="all-current-page">@lang('label.lb209')</a>
        </li>
        @endif
        @if($btnExpAll)
        <li>
          <a href="#" class="btnb2excel" id="b2excel-all" data-export-type="all">@lang($textTransAll)</a>
        </li>
        @endif
      </ul>
    </div>
@endif