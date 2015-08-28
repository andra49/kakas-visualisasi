@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Edit Dataset</div>
                <div class="panel-body">
                  <div class="about-section">
                     <div class="text-content">
                        {!! Form::open(array('url'=>'dataset/configuration','method'=>'POST')) !!}
                         <div class="control-group">
                          <h4>Nama Tabel</h4>
                          <input type="text" name="tablename" value="{{$tablename}}" class="form-control" />
                         </div>
                         <div class="control-group">
                          <h4>Nama Atribut</h4>
                          @foreach ($attributes as $attribute)
                            <input type="text" name="header[]" value="{{$attribute}}" />
                          @endforeach
                        </div>
                        {!! Form::submit('Submit', array('class'=>'btn btn-default send-btn pull-right')) !!}
                        {!! Form::close() !!}
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection