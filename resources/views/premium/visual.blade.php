@extends("layout.settings")

@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Visual Post
                </div>
                <div class="panel-body">
                    The Visual Post service offers an image view of your top artists for the last seven days. You'll
                    need to sign in with your Last.fm account to use this feature at <a href="https://visual.tweekly.fm">https://visual.tweekly.fm</a>.
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @include("common.comments")
    </div>
@stop
