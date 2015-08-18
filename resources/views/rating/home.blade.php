@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Daftar Visualisasi</div>
                <div class="panel-body">
					<table class="table table-hover">
						<thead>
							<tr>
								@foreach ($columnnames as $name)
									<th>{{$name}}</th>
								@endforeach
								<th>Pilihan</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($data as $row)
							<tr>
								@foreach ($row as $cell)
									<td>{{$cell}}</td>
								@endforeach
								<td><a href="{{URL::to('setup/visualization/'.$row['id'])}}" class="btn btn-default" role="button">Pilih</a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<a href="{{URL::to('setup/new')}}" class="btn btn-default pull-right" role="button">Buat Visualisasi Baru</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection