@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Daftar Dataset</div>
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
							@foreach ($data as $key => $row)
							<tr>
								@foreach ($row as $cell)
									<td>{{$cell}}</td>
								@endforeach
								<td>
									<a href="{{URL::to('dataset/edit/'.$row['id'])}}" class="btn btn-default" role="button">Edit</a>
									@if ($hasVisualization[$key])
										<a href="{{URL::to('dataset/delete/'.$row['id'])}}" class="btn btn-danger" role="button" disabled>Hapus</a>
									@else
										<a href="{{URL::to('dataset/delete/'.$row['id'])}}" class="btn btn-danger" role="button">Hapus</a>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<a href="{{URL::to('dataset/upload')}}" class="btn btn-default pull-right" role="button">Unggah Dataset Baru</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection