<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
        @yield('cssimports')
        <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" >

        <link href="//fonts.googleapis.com/css?family=Lato:400" rel="stylesheet" type="text/css">

        <style>
            body {
                font-family: 'Lato';
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-default">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="{{URL::to('home')}}">Kakas Visualisasi</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">              
              <ul class="nav navbar-nav">
                @if (Auth::check())
                    <li><a href="{{URL::to('dataset')}}">Dataset</a></li>
                    <li><a href="{{URL::to('setup')}}">Visualisasi</a></li>
                    <li><a href="{{URL::to('home')}}">Pengaturan</a></li>
                @endif
              </ul>
              <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="{{URL::to('auth/register')}}">Register</a></li>
                    <li><a href="{{URL::to('auth/login')}}">Login</a></li>
                @else
                    <li><a href="{{URL::to('auth/signout')}}">Sign out</a></li>
                @endif
              </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
        @yield('content')
    </body>
    @yield('jsimports')
</html>