@extends("layout.settings")

@section("content")
    <div class="row">
        @foreach($news as $news_post)
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="/news/{{ $news_post["slug"] }}">{{ $news_post["date"]->toFormattedDateString() }} &mdash; {{ $news_post["title"] }}</a>
                    </div>
                </div>
            </div>
            <hr>
        @endforeach
    </div>
@stop
