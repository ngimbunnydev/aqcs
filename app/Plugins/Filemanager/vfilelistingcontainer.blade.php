<div class="multiple-add-files" data-id="{{$name??'filelist'}}" style="border: 1px dotted #ddd; padding: 10px; border-radius: 5px;">
                          
                          @if(isset($filelistings) && !empty($filelistings))
                            @php
                              $data_preview=[];
                              foreach ($filelistings as $record) {
                                  array_push($data_preview, 
                                      [ 
                                          'file_name' =>  $record,
                                      ]
                                  );
                              }
                            @endphp
                            @include('Filemanager.vfilepreviewobj_list', ['setting'=>['givent_txtbox'=>$name], 'filedata'=>$data_preview, 'recentlyadd'=>''])
                          @else
                            <label class="ace-file-input ace-file-multiple">
                              <span class="ace-file-container" data-title="{{$caption??''}}">
                                <span class="ace-file-name">
                                  <i class=" ace-icon ace-icon fa fa-images"></i>
                                </span>
                              </span>
                        
                          </label>
                          @endif
                          
</div>
<span class="hide" id="dffilelisting">
    <label class="ace-file-input ace-file-multiple">
                              <span class="ace-file-container" data-title="{{$caption??''}}">
                                <span class="ace-file-name">
                                  <i class=" ace-icon ace-icon fa fa-images"></i>
                                </span>
                              </span>
                        
                          </label>
</span>

