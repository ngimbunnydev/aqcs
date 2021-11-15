
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
    @if(!empty($record['file_name']))
    <div style="width: 100%;height: 54px;border: 1px solid #c3c3c3;display: flex; flex-direction: row; margin-bottom:5px">
      <div>
        <img width="50" height="50" alt="{{$record['file_name']}}" 
      src="{{ URL::asset('/resources/filelibrary/_150/'.$record['file_name']) }}" />
      </div>
      <div>
        {{$record['file_name']}}
        <input type="hidden" name="{{$setting['givent_txtbox']}}" value="{{$record['file_name']}}">
      </div>
      <div>
        <a href="#" class="deletfile">
        <i class="ace-icon fa fa-times"></i>
      </a>
      </div>
    </div>
    @endif

  @endforeach                                    
                                                                              
