@extends("layout.wide")

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Please Select Blog to Publish To
        </div>
        <div class="panel-body">
            <form method="post" action="/connect/tumblr/select">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <select class="form-control" name="blog_id">
                    @foreach ($blogs as $blog)
                        <option value="{{ $blog["name"] }}">{{ $blog["title"] }}</option>
                    @endforeach
                </select><br>
                <input type="submit" class="btn btn-primary" value="Select Blog">
            </form>
        </div>
    </div>
@endsection
