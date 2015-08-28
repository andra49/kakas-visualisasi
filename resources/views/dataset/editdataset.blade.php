@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Ubah Dataset</div>
                <div class="panel-body">
                	<form action="/dataset/edit" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="datasetid" value="{{$datasetid}}">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nama Atribut</th>
									<th>Tipe Variabel Data</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($attributes as $attribute)
								<tr>
									<td><input type="text" name="name[{{$attribute->id}}]" value="{{$attribute->name}}" class="form-control"></td>
									<td>
										<select name="type[{{$attribute->id}}]" class="form-control">
					                        <option value="nominal" 
					                        	@if ($attribute->data_variable_type == 'nominal')
					                        		selected="selected"
					                        	@endif
					                        >Nominal</option>
					                        <option value="ordinal" 
					                        	@if ($attribute->data_variable_type == 'ordinal')
					                        		selected="selected"
					                        	@endif
					                        >Ordinal</option>
					                        <option value="kuantitatif" 
					                        	@if ($attribute->data_variable_type == 'kuantitatif')
					                        		selected="selected"
					                        	@endif
					                        >Kuantitatif</option>
					                    </select>
					                </td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<input type="submit" class="btn btn-default pull-right" value="Simpan">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection