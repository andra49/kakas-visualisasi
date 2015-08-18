@extends('layouts.master')

@section('content')
<div class="about-section">
   <div class="text-content">
     <div class="span7 offset1">
        <div class="secure">Dataset Configuration</div>
        {!! Form::open(array('url'=>'dataset/configuration','method'=>'POST')) !!}
         <div class="control-group">
          <input type="text" name="tablename" value="{{$tablename}}" />
         </div>
         <div class="control-group">
          @foreach ($attributes as $attribute)
            <input type="text" name="header[]" value="{{$attribute}}" />
          @endforeach
        </div>
        {!! Form::submit('Submit', array('class'=>'send-btn')) !!}
        {!! Form::close() !!}
      </div>
   </div>
</div>
@endsection