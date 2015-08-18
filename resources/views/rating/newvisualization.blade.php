@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Buat Visualisasi Baru</div>
                <div class="panel-body">
                	<form action="new" method="POST">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
					  <div class="form-group">
					    <label for="visname">Nama Visualisasi</label>
					    <input type="text" class="form-control" id="visname" placeholder="Nama Visualisasi" name="name">
					  </div>
					  <div class="form-group">
					    <label for="dataset">Dataset yang digunakan</label>
					    <select class="form-control" name="datasetid" id="dataset">
					    	@foreach ($datasets as $dataset)
							<option value="{{$dataset->id}}">{{$dataset->table_name}}</option>
							@endforeach
						</select>
					  </div>
					  <button type="submit" class="btn btn-default">Simpan</button>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection