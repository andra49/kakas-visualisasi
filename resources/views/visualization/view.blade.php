@extends('layouts.master')

@section('cssimports')
	<link href="{{ URL::asset('css/c3.css') }}" rel="stylesheet" type="text/css" >
@endsection

@section('content')
    <body ng-controller="mainController">
        <h2 style="text-align: center;">{{$projectname}}</h2>
        <div id="chart"></div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4" ng-hide="submitted">
                Apakah Anda menyukai visualisasi ini ?
                <input class="btn btn-default" type="button" value="Ya" ng-click="submitRating(true)">
                <input class="btn btn-default" type="button" value="Tidak" ng-click="submitRating(false)">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <a href="{{URL::to('visualization/save')}}" class="btn btn-primary" role="button">Simpan</a>
                <form action="/visualization/feedback" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="time" value="<%currTime%>">
                    <input type="submit" class="btn btn-danger" value="Kembali">
                </form>
            </div>
        </div>
    </body>
@endsection

@section('jsimports')
	<!-- d3/c3 -->
    <script type="text/javascript" src="{{ asset('js/lib/d3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lib/c3.min.js') }}"></script>

    <!-- AngularJS -->
    <script type="text/javascript" src="{{ asset('js/lib/angular.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/lib/mobile-detect.min.js') }}"></script>

    <!-- script -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/mainController.js') }}"></script>
@endsection