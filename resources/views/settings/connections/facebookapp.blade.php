@extends("layout.home")

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Facebook App
        </div>
        <div class="panel-body">
            <p>To post updates to Facebook, you will need to create an app. For instructions on how to do this, please see the <a href="https://vimeo.com/156076208">video below</a>:</p>
            <iframe src="https://player.vimeo.com/video/156076208" width="500" height="400" style="width: 100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            <br>
            <hr>
            <form method="post" action="/connect/facebook/app">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">

                <label>Application ID:</label><br>
                <input name="app_id" class="form-control" type="text" value="{{ $app_id }}">
                <br>
                <label>Application Secret:</label><br>
                <input name="app_secret" class="form-control" type="text" value="{{ $app_secret }}">
                <br>
                <input type="submit" class="btn btn-primary" value="Store App">
            </form>

            @if (!empty($app_id) && !empty($app_secret))
                <br>
                <hr>
                <p>To authorize your Facebook account with your application, click the button below:</p>
                <a href="/connect/facebook/go" class="btn btn-success">Authorize Facebook Account</a>
                <br>
                <hr>
                <p>To delete your saved application details, use the button below. This will prevent posting to Facebook.</p>
                <form method="post" action="/connect/facebook/app/delete">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <input type="submit" class="btn btn-danger" value="Delete App">
                </form>
            @endif

        </div>
    </div>
@endsection
