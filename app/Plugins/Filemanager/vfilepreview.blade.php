
@php
    $fileextension= config('ccms.fileextension');
    $recently=explode(',',$recentlyadd);
    $displaymode=$setting['displaymode'];
@endphp
<ul class="ace-thumbnails clearfix" >
  @foreach ($filedata as $record)
    @php 
      $newboder = ''; 
      $file_spr= explode('.', $record['media']);
      $extensions = end($file_spr);
    @endphp
    @if (in_array($record['f_id'], $recently))
      @php $newboder='border-recent'; @endphp
    @endif
   <li class="{{$newboder}}" >
    <a href="#" class="filteitem" id="{{$record['f_id']}}" filename="{{$record['media']}}">
      @if($fileextension[$extensions]=='image')
      @php
        $image = $record['media'];       
      @endphp
      <img width="150" height="150" alt="{{$record['media']}}" 
      src="{{ URL::asset('/resources/filelibrary/_150/'.$image) }}" />
      @else
      <!--<div class="file_{{$fileextension[$extensions]}}">{{$record['media']}}</div>-->
      <img width="150" height="150" alt="{{$record['media']}}" 
      src="{{ URL::asset('/app/Plugins/Filemanager/imgs/'.$fileextension[$extensions].'.png') }}" />
      @endif
      <div class="text">
        <div class="inner">{{$record['media']}}</div>
      </div>
    </a>
    <div class="tools tools-top">
      <a href="#" class="filteitem-del" id="{{$record['f_id']}}" style="float: left;">
        <i class="ace-icon fa fa-times"></i>
      </a>
      <a href="#">
        <i class="ace-icon fa fa-search-plus"></i>
      </a>
      <a href="#" style="float: right;">
        <i class="ace-icon fa fa-download"></i>
      </a>
      
    </div>
    @if ($displaymode!=2)
    <div class="tags">
      <span class="label-holder">
        <input type="checkbox" name="chk_multiadd" class="chk_multiadd" value="{{$record['f_id']}}">&nbsp;
      </span>
    </div>
    @endif

  </li> 

  @endforeach                                    
                                                                              
</ul>