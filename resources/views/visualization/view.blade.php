<!DOCTYPE html>
<html ng-app="visualisasi">
    <head>
        <title>Laravel</title>
        <link href="{{ URL::asset('css/c3.css') }}" rel="stylesheet" type="text/css" >
    </head>
    <body ng-controller="mainController">
        <div id="chart"></div>
    </body>
    <!-- d3/c3 -->
    <script type="text/javascript" src="{{ asset('js/lib/d3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/lib/c3.min.js') }}"></script>

    <!-- AngularJS -->
    <script type="text/javascript" src="{{ asset('js/lib/angular.min.js') }}"></script>

    <!-- script -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/mainController.js') }}"></script>

</html>
