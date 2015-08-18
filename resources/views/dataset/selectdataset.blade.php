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
							@foreach ($data as $row)
							<tr>
								@foreach ($row as $cell)
									<td>{{$cell}}</td>
								@endforeach
								<td><button>Pilih</button></td>
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