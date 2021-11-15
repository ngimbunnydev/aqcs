<div id="setting_center" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">
                File Setting
                <span id="filesettingloading" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></span>
            </h5>
          </div>
          <div class="modal-body">
                <!-- form --><form name="frm_filesetting" id="frm_filesetting" action="" method="POST">
                    <div class="row">
                        <input class="en label_b" type="hidden" name="txt_objid" id="txt_objid"/>
                        <input class="en label_b" type="hidden" name="txt_objfid" id="txt_objfid"/>
                        <!--Media :<br />-->
                       
                        <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                            <label class="en label_b" for="txt_scrshot">Screenshot: (double click to remove)</label>
                            <div class="input-group">
                                <input class="form-control input-sm" type="text" name="txt_scrshot" id="txt_scrshot" value="" readonly="readonly" ondblclick="this.value=''"/>
                                <span class="input-group-btn">
        
                                    <button id="btn-browe-scrsh" class="btn btn-default btn-sm" type="button" style="height: 30px">
                                                Browse...
                                    </button>
                                </span>
                            </div>
                        </div>



                    </div>
                    <div class="row">
                            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
                                <label class="en label_b" for="txt_w">Width:</label>
                                <input class="en label_b form-control input-sm" type="text" name="txt_w" id="txt_w" />
                            </div>
                            <div class="col-xs-12 col-sm-6 col-lg-6 form-group">
                                <label class="en label_b" for="txt_h">Height:</label>
                                <input class="en label_b form-control input-sm" type="text" name="txt_h" id="txt_h" />
                            </div>


                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-lg-4">
                            <label class="en label_b" for="txt_forder">Ordering:</label>
                            <input class="en label_b form-control input-sm" type="text" name="txt_forder" id="txt_forder" value="" onkeypress="return  intOnly(this, event)" />
                        </div>
                        <div class="col-xs-4 col-sm-4 col-lg-4">
                            <label class="en label_b" for="chk_fcover">Cover:</label><br />
                            <input type="checkbox" name="chk_fcover" id="chk_fcover" value="yes"/>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-lg-4">
                            <label class="en label_b" for="chk_fbg">Backround:</label><br />
                            <input type="checkbox" name="chk_fbg" id="chk_fbg" value="yes"/>
                        </div>
                        <!--<br />Tag :<br />
                        <input class="en label_b" type="text" name="txt_ftag" id="txt_ftag" style="width:134px;" value=""/>-->
                    </div>
                    @if(isset($allcolors) & $allcolors!=null)
                    <div class="row">
                            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                                <label class="en label_b" for="txt_ftag">Color:</label>
                                <select class="en label_b form-control input-sm" name="cmb_color" id="cmb_color">
                                    <option>-- {{__('ccms.ps')}} --</option>
                                    {!!cmb_listing($allcolors,[''],'','')!!} 
                                </select>
                            </div>
                    </div>
                    @endif

                    <div class="row">
                            <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
                                <label class="en label_b" for="txt_ftag">Tag:</label>
                                <input class="en label_b form-control input-sm" type="text" name="txt_ftag" id="txt_ftag" />
                            </div>
                    </div>
                    <!-- start title --><div class="row">
                        @foreach (config('ccms.multilang') as $lang)
                            
                            <div class="col-xs-12 col-sm-12 col-lg-12">
                                <label class="en label_b" for="txtfiletitle_">Title (@lang('ccms.'.$lang[0]))</label>
                                <input class="en label_b form-control input-sm" type="text" name="txtfiletitle_{{$lang[0]}}" id="txtfiletitle_{{$lang[0]}}" value="" />
                            </div>
                        @endforeach  
                    <!-- start title --></div>
                    
                    
                    <!-- end form --></form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btn-setting-submit" class="btn btn-default btn-sm" data-dismiss="modal">Save</button>
          </div>
        </div>
    
    </div>
</div>
<!-- FOR SETTING Center  -->