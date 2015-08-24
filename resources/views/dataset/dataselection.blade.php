@extends('layouts.master')

@section('content')
<div class="container-fluid" ng-controller="selectionController">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Seleksi Data</div>
                <div class="panel-body">
                	<div class="col-md-12" style="max-height: 300px; overflow: auto;">
						<table class="table table-hover">
							<thead>
								<tr>
									@foreach ($columnnames as $name)
										<th>{{$name}}</th>
									@endforeach
								</tr>
							</thead>
							<tbody>
								@foreach ($data as $row)
								<tr>
									@foreach ($row as $cell)
										<td>{{$cell}}</td>
									@endforeach
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="col-md-12" id="filter-list" style="margin-top: 15px;">
			    		@foreach ($filters as $filter)
							<div class="alert alert-info alert-dismissible" role="alert">
								<form class="form-inline" method="POST" action="/dataset/remove">
		                        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input type="hidden" value="{{$projectid}}" name="projectid">
									<input type="hidden" value="{{$filter->id}}" name="selectionid">
							  		<button type="submit" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							  	</form>
							  	<strong>Filter</strong> {{$filter->column_name}} {{$filter->operator}} {{$filter->operand}}
							</div>
						@endforeach
					</div>
					<div class="col-md-12">
						<form class="form-inline" method="POST" action="/dataset/selection">
                        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" value="{{$projectid}}" name="projectid">
						  	<div class="form-group">
						    	<label for="exampleInputName2">Kolom</label>
						    	<select class="form-control" name="column" ng-model="selection" ng-change="toggleSelection()">
						    		@foreach ($columnnames as $name)
								  		<option value="{{$name}}">{{$name}}</option>
									@endforeach
								</select>
						  	</div>
						  	<div class="form-group">
						    	<label for="exampleInputEmail2">Operator</label>
						    	<select class="form-control" name="operator">
								  <option value="=">=</option>
								  <option ng-hide="disable" value=">">></option>
								  <option ng-hide="disable" value=">=">>=</option>
								  <option ng-hide="disable" value="<"><</option>
								  <option ng-hide="disable" value="<="><=</option>
								</select>
						  	</div>
						  	<div class="form-group">
						    	<label for="input-value">Nilai</label>
						    	<input type="text" class="form-control" id="input-value" placeholder="100" name="operand">
						  	</div>
						  	<button type="submit" class="btn btn-info pull-right">Tambah Filter</button>
						</form>
					</div>
					<div class="col-md-12">
						
					</div>
					<hr>
					<div class="col-md-12">
						<a href="{{URL::to('setup/visualization/'.$projectid)}}" class="btn btn-default pull-right" role="button">Lanjutkan</a>
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
    <script type="text/javascript">
		var stringColumns = [
			@for ($i = 0; $i < count($stringColumns) - 1; $i++)
				'{{$stringColumns[$i]}}',
			@endfor
			'{{$stringColumns[count($stringColumns) - 1]}}'
		];
	</script>
    <script type="text/javascript" src="{{ asset('js/selectionController.js') }}"></script>
@endsection