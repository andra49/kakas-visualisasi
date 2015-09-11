@extends('layouts.master')

@section('content')
<div class="container-fluid" ng-controller="configurationController">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Konfigurasi Visualisasi</div>
                <div class="panel-body" ng-hide="loading">
                    <div class="row" >
                        <div class="col-md-6" >
                            <h4>Pilih atribut yang divisualisasikan</h4>
                            <div class="col-md-12">
                                <form>
                                    <div class="row">
                                    <div class="col-md-6" style="max-height: 300px; overflow: auto;">
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="well">
                                            <strong>Atribut yang dipilih (maks. 4)</strong>
                                            <ul class="nav nav-pills nav-stacked" ng-repeat="item in selection">
                                              <li><%item%></li>
                                            </ul>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="purpose">Tujuan visualisasi</label>
                                        <select class="form-control" ng-model="purpose" id="purpose">
                                            <option value="ALL">ALL</option>
                                            <option value="COMPARE_CATEGORIES">Membandingkan antar kategori</option>
                                            <option value="SHOW_COMPOSITION">Menunjukkan komposisi data</option>
                                            <option value="TEMPORAL_DATA">Menampilkan perubahan seiring waktu</option>
                                            <option value="SHOW_RELATIONSHIP">Menunjukkan relasi antara variabel</option>
                                        </select>
                                    </div>
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
                            <h4>Rekomendasi dari sistem 
                                <select class="pull-right" ng-init="numRecommendation = 3" ng-model="numRecommendation">
                                    <option value="1">1 Rekomendasi</option>
                                    <option value="3">3 Rekomendasi</option>
                                    <option value="5">5 Rekomendasi</option>
                                    <option value="100">Semua</option>
                                </select>
                            </h4>
                            <h5 ng-hide="recommendations.length > 0">Silahkan masukkan atribut</h5>
                            <div class="panel panel-default" ng-repeat="recommendation in recommendations | limitTo:numRecommendation">
                              <div class="panel-heading"><strong><% recommendation.visualization %></strong></div>
                              <div class="panel-body">
                                <div class="media">
                                  <div class="media-left">
                                    <a ng-href="{{URL::to('visualization')}}?mappingid=<% $index %>">
                                        <img ng-src="/images/charts/<%recommendation.visualization%>.jpg">
                                    </a>
                                  </div>
                                  <div class="media-body">
                                  </div>
                                </div>
                              </div>
                              <div class="panel-footer">
                                Rating <strong><% recommendation.rating | number : 0 %></strong>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body sk-fading-circle" ng-show="loading">
                    <div class="sk-circle1 sk-circle"></div>
                    <div class="sk-circle2 sk-circle"></div>
                    <div class="sk-circle3 sk-circle"></div>
                    <div class="sk-circle4 sk-circle"></div>
                    <div class="sk-circle5 sk-circle"></div>
                    <div class="sk-circle6 sk-circle"></div>
                    <div class="sk-circle7 sk-circle"></div>
                    <div class="sk-circle8 sk-circle"></div>
                    <div class="sk-circle9 sk-circle"></div>
                    <div class="sk-circle10 sk-circle"></div>
                    <div class="sk-circle11 sk-circle"></div>
                    <div class="sk-circle12 sk-circle"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('jsimports')
    <!-- AngularJS -->
    <script type="text/javascript" src="{{ asset('js/lib/angular.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/lib/mobile-detect.min.js') }}"></script>

    <!-- script -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/configurationController.js') }}"></script>
@endsection