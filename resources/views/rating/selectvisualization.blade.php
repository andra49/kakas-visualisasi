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
                                        <label>
                                            <input type="checkbox" ng-model="isExact"> Exact match
                                        </label>
                                        <button class="btn btn-default" ng-click="loadRecommendation()">Masukkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <h4>Rekomendasi dari sistem</h4>
                            <div class="panel panel-default" ng-repeat="recommendation in recommendations">
                              <div class="panel-body">
                                <% recommendation.visualization %> <a ng-href="{{URL::to('visualization')}}?mappingid=<% $index %>" class="btn btn-primary pull-right" role="button">Pilih</a>
                              </div>
                              <div class="panel-footer">Rating <strong><% recommendation.rating | number : 0 %></strong></div>
                            </div>
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