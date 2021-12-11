@php 
  $btnList = (isset($btnList)) ? $btnList:true;
  $btnExport = (isset($btnExport)) ? $btnExport:true;
  $btnExpCurrentPage = (isset($btnExpCurrentPage)) ? $btnExpCurrentPage:true;
  $btnExpAllFilterPage = (isset($btnExpAllFilterPage)) ? $btnExpAllFilterPage:true;
  $btnExpAll = (isset($btnExpAll)) ? $btnExpAll:true;
  $textTransAll = (isset($textTransAll))?$textTransAll:'label.lb210';
@endphp
@if($btnExport)
<div class="btn-group">
      <button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle" style="padding: 4.5px 5px;" aria-expanded="false">
         <i class="fas fa-file-export"></i>
        <span id="b2excel-text">@lang('label.export')</span>
        <i class="ace-icon fa fa-angle-down icon-on-right"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-right">
      
        <li>
          <a href="#" class="btnb2excel" id="b2excel-current-page" data-export-type="current-page">To Excel</a>
        </li>
     
        <li>
          <a href="#" class="btnb2excel" id="b2excel-current-page" data-export-type="all-page">Export Filter</a>
        </li>
       
        <li>
          <a href="#" class="btnb2excel" id="b2excel-all" data-export-type="pdf">To PDF</a>
        </li>
       
      </ul>
    </div>
@endif