<!-- Media Light box -->
<div id="widget_center" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header pding-5">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>
            <h5 class="modal-title">
              Widget Center
              <span id="" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></span>
            </h5>
            
          </div>


          <div class="modal-body">
            <div class="row">
              @foreach (Storage::disk('widgetsview')->directories() as $widget)
                @php
                  $path = Storage::disk('widgetsview')->path($widget.'/readme.txt')
                @endphp
                @if(file_exists($path))
                  @php
                    $str = File::get($path);

                    $start = '#des';
                    $end = '#enddes';
                    $contents = get_between_strings($start, $end, $str);


                    $start = '#syntax';
                    $end = '#endsyntax';
                    $sytax = get_between_strings($start, $end, $str);


                  @endphp
                  
                  
                    <div class="col-sm-6">

                        <div class="widget-box">
                          <div class="widget-header">
                            <h4 class="smaller">
                              {{ucfirst($widget)}}
                              
                            </h4>
                          </div>

                          <div class="widget-body">
                            <div class="widget-main">
                                @php
                                  $contents = preg_replace('/\r?\n|\r/','<br/>', $contents);
                                  $contents = str_replace(array("\r\n","\r","\n"),"<br/>", $contents);
                                  $contents = nl2br($contents);
                                @endphp
                                {!!$contents!!}
                              <hr>
                                <button class="btn-widget btn btn-white btn-default btn-round">{!!$sytax!!}</button>
                            </div>
                          </div>
                          
                        </div>


                    </div><!--/.cell-->

                  
                  

                @endif
                
              @endforeach
              </div> <!--/.row-->
          </div>
          
        </div>
    
    </div>
</div>
