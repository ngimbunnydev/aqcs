
@php
    $fileextension= config('ccms.fileextension');
    $recently=$recentlyadd;
    $newboder = ''; 
    if($recently=='yes')
    {
      $newboder = 'border-recent'; 
    }
@endphp

  @foreach ($filedata as $record)
    @php 
      
      $file_spr= explode('.', $record['file_name']);
      $extensions = end($file_spr);
    @endphp

   <li class="{{$newboder}}" >
    <a href="#" class="filteitemzoom" id="{{$record['objf_id']}}" filename="{{$record['file_name']}}">
      @if($fileextension[$extensions]=='image')
      <img width="150" height="150" alt="{{$record['file_name']}}" 
      src="{{ URL::asset('/resources/filelibrary/_150/'.$record['file_name']) }}" />
      @else
      <!--<div class="file_{{$fileextension[$extensions]}}">{{$record['file_name']}}</div>-->
      <img width="150" height="150" alt="{{$record['file_name']}}" 
      src="{{ URL::asset('/app/Plugins/Filemanager/imgs/'.$fileextension[$extensions].'.png') }}" />
      @endif
      <div class="text">
        <div class="inner">{{$record['file_name'].$record['as_bg']}}</div>
      </div>
    </a>
    <div class="tools tools-top">
      <a href="#" class="filteitemobj-del" id="{{$record['objf_id']}}" style="float: left;">
        <i class="ace-icon fa fa-times"></i>
      </a>
      
      <a href="#">
        <i class="ace-icon fa fa-download"></i>
      </a>

      <a href="#" class="filteitemobj-setting" id="{{$record['objf_id']}}" style="float: right;">
        <i class="ace-icon fa fa-cog"></i>
      </a>
      
    </div>

    <div class="tags">
      
      <span id="cover-{{$record['objf_id']}}" class="label-holder as_cover">
        @if($record['as_cover']=='yes')
        <span class="label label-success arrowed">Cover</span>
        @endif
      </span>
      

      
      <span id="bg-{{$record['objf_id']}}" class="label-holder as_bg">
        @if($record['as_bg']=='yes')
        <span class="label label-danger arrowed">Background</span>
        @endif
      </span>
      
    </div>

    

  </li> 

  @endforeach                                    
                                                                              
