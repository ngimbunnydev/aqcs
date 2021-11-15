<!-- Media Light box -->
<div id="media_center" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header pding-5">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>
            <h5 class="modal-title">
              @lang('ccms.filemanager')
              <span id="fileloading" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></span>
            </h5>
            
          </div>


          <div class="modal-body">

              <div class="row">
                                <div class="col-xs-12 col-sm-12 col-lg-12">
                                  <span id="filemanagermsgtop"></span>
                                </div>
              </div>

              <div class="file-category-panel">
                <div id="file_category" class="split split-horizontal">
                  <!-- Tree --><div id="file-category-tree" class="demo">
                  </div>
                  <!-- End tree -->
                </div>
                <div id="file_listing" class="split split-horizontal pding-10">
                    <!--==================================================================================-->
                        <!-- Retriew Media from DATABASE -->
                
                            <!-- Media Gallery --><div class="row">
<!--                             <div class="col-xs-12 col-md-12 col-lg-12" style="height: 90%;"> -->
                                  <div class="col-xs-12 col-md-12 col-lg-12"> 
                                      <span id="filepreview">
                                        <!-- file item will list here -->
                                      </span>
                                      <input type="hidden" name="txt_chked_tmp" id="txt_chked_tmp" />

                                  </div>
                            </div>
                            <!--/.row-->
                            <div class="row" style="margin-top:20px">
                            <!-- Researve for Paginatino -->
                            <div class="col-xs-12 col-md-12 col-lg-12 center" style="height: auto;">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pding-0 margin-0" id="pagination"></ul>
                                </nav>
                            </div>
                            <!-- End Media Gallery --></div>

                            
                        <!-- End Retriew Media from DATABASE -->
                   
                    <!--==================================================================================-->
                </div>
              </div>

              <!--test--><form name="frm_mediacenter" id="frm_mediacenter" target="multiple_upload" action="" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="widget-box collapsed">
                        <div class="widget-header">
                          
<!--                           <h5 class="widget-title">@lang('ccms.upload')</h5>
                          
                          <div class="widget-toolbar">
                            <a href="#" data-action="collapse">
                              <i class="ace-icon fa fa-chevron-down"></i>
                            </a>
                          </div> -->
                          <div class="widget-title">
                            <a href="#" data-action="collapse">
                              @lang('ccms.upload')
                              <i class="ace-icon fa fa-chevron-down"></i>
                            </a>
                          </div>
                          
                        </div>

                        <div class="widget-body" style="display: none;">
                          <div class="widget-main">
                              <!-- upload -->
                              <div class="row">
                                <div class="col-xs-12 col-sm-12 col-lg-12">
                                  <span id="filemanagermsg"></span>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
                                        <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                                            <label class="en label_b">
                                                @lang('ccms.customize_media')
                                            </label>
                                            <input type="hidden" name="filecategory" id="filecategory" value="0">
                                            <input class="en label_b" type="file" name="f_media[]" id="f_media" multiple="multiple" />
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                                            <label class="en label_b">
                                                 @lang('ccms.external_media')
                                            </label>
                                            
                                            <input class="en label_b form-control" type="text" name="txt_media" id="txt_media"value="" />
                        
                                        </div>
                                        
                                        <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
                                            <label class="en label_b"> @lang('ccms.width')</label>
                                            <input class="en label_b form-control" type="text" name="mwidth" value="0" onkeypress="return  intOnly(this, event)" placeholder="0" />
                                        </div>
                                        
                                        <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
                                            <label class="en label_b">@lang('ccms.height')</label>
                                            <input class="en label_b form-control" type="text" name="mheight" value="0" onkeypress="return  intOnly(this, event)" placeholder="0" />
                        
                                        </div>

                                        <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                                          <input type="checkbox" name="chk_uc" id="chk_uc" value="yes" checked="checked" />
                                          <label class="en label_b" for="chk_uc"> @lang('ccms.upload_close') </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
                                      <label class="en label_b">@lang('ccms.camera') </label>
                                        <div id="webcam-control">
                                            <div id="cameralive"></div>
                                            <input type="hidden" id="webcam" name="webcam" value="">
                                            <div id="takestopbtncontrol" style="display:none;margin-top:15px;" class="text-center">
                                                <button type="button" class="btn btn-white btn-info btn-sm" onclick="takeSnapshot();">
                                                <i class="ace-icon fa fa-camera blue"></i>
                                                @lang('ccms.take')
                                              </button>
                                              <button type="button" class="btn btn-white btn-danger btn-sm" onclick="closeSnapshot();">
                                                <i class="ace-icon fa fa-times-circle"></i>
                                                @lang('ccms.close')
                                              </button>
                                                                </div>
                                                                <div id="resumebtncontrol" style="display:none;margin-top:15px;" class="text-center">
                                                                    <button type="button" class="btn btn-white btn-warning btn-sm" onclick="resumeSnapshot();">
                                                <i class="ace-icon fa fa-refresh bigger-120"></i>
                                                <i class="ace-icon fas fa-sync-alt bigger-120"></i>
                                                @lang('ccms.take_another')
                                              </button>
                                                                </div>
                                                                <div id="webcam-open-box">
                                                                    <button type="button" class="btn btn-white btn-info btn-block btn-sm" id="btnopencamera">
                                                <i class="ace-icon fa fa-camera bigger-120 blue"></i>
                                                  @lang('ccms.take_new')
                                              </button>
                                                                </div>
                                                            </div>
                                    </div>

                                        <div class="col-xs-12 col-sm-12 col-lg-12 form-group" style="text-align:center">

                                          <button class="btn btn-primary btn-sm" name="btn-media-submit" id="btn-media-submit">
                                            <i class="ace-icon fa fa-upload align-top"></i>
                                            @lang('ccms.upload')
                                            <i id="span-media-submit" style="display: none;" class="fa fa-spinner fa-pulse"></i>
                                          </button>
                        
                                        </div>
             
                </div>
              <!-- end upload-->

                          </div>
                        </div>
                      </div>
              <!-- ==================== --></form>

          </div>
          
        </div>
    
    </div>
</div>
