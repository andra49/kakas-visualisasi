<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <h2>Select Dataset</h2>
                {!! Form::open(array('url'=>'kakas/rating','method'=>'GET')) !!}
                    <div class="control-group">
                        <div class="controls">
                            <select name="dataset">
                                @foreach ($datasets as $dataset)
                                    <option value="{{$dataset->id}}">{{$dataset->table_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                {!! Form::submit('Submit', array('class'=>'send-btn')) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </body>
</html>
