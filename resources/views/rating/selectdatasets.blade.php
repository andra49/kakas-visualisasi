@extends('layouts.master')

@section('content')
<div class="container">
    <div class="content">
        <h2>Select Dataset</h2>
        {!! Form::open(array('url'=>'setup/rating','method'=>'GET')) !!}
            <div class="control-group">
                <div class="controls">
                    <select name="dataset">
                        @foreach ($datasets as $dataset)
                            <option value="{{$dataset->id}}">{{$dataset->table_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        {!! Form::submit('Submit', array('class'=>'send-btn')) !!}
        {!! Form::close() !!}
    </div>
</div>
@endsection