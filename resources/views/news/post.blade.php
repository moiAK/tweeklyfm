@extends("layout.settings")

@section("content")
    <div class="row">
        @foreach($news as $news_post)
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ $news_post["title"] }} - {{ $news_post["date"]->toFormattedDateString() }}
                    </div>
                    <div class="panel-body">
                        {!! $news_post["content"] !!}
                    </div>
                </div>
            </div>

            @include("common.comments")

        @endforeach
    </div>
@stop
