@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Preferensi Pengguna</div>
                <div class="panel-body">
                	<form action="/user/preference" method="POST">
                      	<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nama Visualisasi</th>
									<th>Pilihan</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($visualizations as $visualization)
								<tr>
									<td>{{$visualization->name}}</td>
									<td>
										<ul class="likert">
										  <li> Tidak pernah menggunakan&nbsp;</li>
										  <li><input type="radio" name="preference[{{$visualization->id}}]" value="1" /></li>
										  <li><input type="radio" name="preference[{{$visualization->id}}]" value="2" /></li>
										  <li><input type="radio" name="preference[{{$visualization->id}}]" value="3" /></li>
										  <li><input type="radio" name="preference[{{$visualization->id}}]" value="4" /></li>
										  <li><input type="radio" name="preference[{{$visualization->id}}]" value="5" /></li>
										  <li>&nbsp;Biasa menggunakan</li>
										</ul>
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