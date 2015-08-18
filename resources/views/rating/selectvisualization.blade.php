@extends('layouts.master')

@section('content')
<div class="container">
    <div class="content">
        <h2>Select Visualization</h2>
        {!! Form::open(array('url'=>'visualization','method'=>'GET')) !!}
            <div class="control-group">
                <div class="controls">
                    <select name="mappingid">
                        @for ($i = 0; $i < count($configuration); $i++)
                            <option value="{{$i}}">{{\App\Visualization::find($configuration[$i]->visualizationid)->name}} Score: {{$configuration[$i]->rating}}</option>
                        @endfor
                    </select>
                </div>
            </div>
        {!! Form::submit('Submit', array('class'=>'send-btn')) !!}
        {!! Form::close() !!}
    </div>
</div>
@endsection