@extends('layouts.master')

@section('content')
<div class="container-fluid" ng-controller="configurationController">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Konfigurasi Visualisasi</div>
                <div class="panel-body">
                    <div class="row" >
                        <div class="col-md-6" >
                            <h4>Pilih atribut yang divisualisasikan</h4>
                            <div class="col-md-12">
                                <form>
                                    @foreach ($attributes as $attribute)
                                    <div class="checkbox">
                                      <label>
                                        <input type="checkbox" 
                                            name="selectedAttrbutes[]" 
                                            value="{{$attribute->name}}" 
                                            ng-checked="selection.indexOf('{{$attribute->name}}') > -1" 
                                            ng-click="toggle('{{$attribute->name}}')">
                                            {{$attribute->name}}
                                      </label>
                                    </div>
                                    @endforeach
                                    <div class="col-md-6 col-md-offset-4">
                                        <button class="btn btn-default" ng-click="loadRecommendation()">Masukkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <h4>Rekomendasi dari sistem</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('jsimports')
    <!-- AngularJS -->
    <script type="text/javascript" src="{{ asset('js/lib/angular.min.js') }}"></script>

    <!-- script -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/configurationController.js') }}"></script>
@endsection