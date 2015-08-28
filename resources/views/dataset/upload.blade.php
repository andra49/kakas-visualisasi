@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Unggah Dataset Baru</div>
                <div class="panel-body">
                  <div class="about-section">
                     <div class="text-content">
                       <div class="span7 offset1">
                          @if(Session::has('success'))
                            <div class="alert-box success">
                            <h2>{!! Session::get('success') !!}</h2>
                            </div>
                          @endif
                          <div class="secure">Upload dataset</div>
                          {!! Form::open(array('url'=>'dataset/upload','method'=>'POST', 'files'=>true)) !!}
                           <input type="hidden" name="_token" value="{{ csrf_token() }}">
                           <div class="control-group">
                            <div class="controls">
                            {!! Form::file('dataset') !!}
                            </div>
                            @if(Session::has('error'))
                            <p class="errors">{!! Session::get('error') !!}</p>
                            @endif
                          </div>
                          <div id="success"> </div>
                          {!! Form::submit('Submit', array('class'=>'btn btn-default send-btn pull-right')) !!}
                          {!! Form::close() !!}
                        </div>
                     </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection