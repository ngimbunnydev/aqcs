<!-- Modal -->
<div id="modal_windows" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header pding-5">
        @if(!isset($btnclose) || $btnclose!='no')
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        @endif
        <h5 class="modal-title">AnAoffice
          <small>
              <i class="fa fa-leaf"></i>
              
            </small>
            
            <span class="ajaxloading" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></span>
           
          </h5>
        
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-lg-12">        
                    <span id="air_windows">
                      
                        
                    </span>
                </div>
                
            </div>
            <!-- /#row -->
      </div>
      <div class="modal-footer pding-5">
        @if(!isset($btnclose) || $btnclose!='no')
        <button type="button" class="btn btn-sm" data-dismiss="modal">@lang('ccms.close')</button>
        @endif
      </div>
    </div>

  </div>
</div>



<!-- Modal -->
<div id="modal_windows2" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header pding-5">
        <button type="button" class="close" data-dismiss="modal" onclick="$('#modal_windows').modal('show')">&times;</button>
        <h5 class="modal-title">AnAoffice-2 
          <small>
              <i class="fa fa-leaf"></i>
            </small>
            <span class="ajaxloading" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></span>
          </h5>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-lg-12">
                    <span id="air_windows2">
                        
                    </span>
                </div>
                
            </div>
            <!-- /#row -->
      </div>
      <div class="modal-footer pding-5">
        <button type="button" class="btn btn-sm" data-dismiss="modal" onclick="$('#modal_windows').modal('show')">@lang('ccms.close')</button>
      </div>
    </div>

  </div>
</div>