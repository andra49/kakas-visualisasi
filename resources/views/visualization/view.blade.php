@extends('layouts.master')

@section('cssimports')
	<link href="{{ URL::asset('css/c3.css') }}" rel="stylesheet" type="text/css" >
@endsection

@section('content')
    <body ng-controller="mainController">
        <div id="chart"></div>
    </body>
@endsection

@section('jsimports')
	<!-- d3/c3 -->
    <script type="text/javascript" src="{{ asset('js/lib/d3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lib/c3.min.js') }}"></script>

    <!-- AngularJS -->
    <script type="text/javascript" src="{{ asset('js/lib/angular.min.js') }}"></script>

    <!-- script -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/mainController.js') }}"></script>
@endsection