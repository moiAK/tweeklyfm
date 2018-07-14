@extends("layout.settings")

@section("navigation.title")

@stop

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Feature Request: {{ $feature->title }}
        </div>
        <div class="panel-body">
            <div class="pull-right">
                <img src="http://www.gravatar.com/avatar/{{ md5($feature->user->email) }}" class="img-circle img-thumbnail" />
            </div>
            This feature request was submitted by {{ $feature->user->name }} ({{ $feature->user->username }}) {{ $feature->created_at->diffForHumans() }}.<br>
            <br>
            The status of this feature request is <strong>{{ $feature->status }}</strong>.
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Feature Description
        </div>
        <div class="panel-body">
            {{ $feature->content }}
        </div>
    </div>

    @if ($feature->status == "accepted")
        <div class="alert alert-success">This feature has been accepted and will be developed shortly.</div>
    @endif

    @if ($feature->status == "rejected")
        <div class="alert alert-danger">This feature wasn't accepted and voting has closed.</div>
    @endif

    @if ($feature->status == "completed")
        <div class="alert alert-success">This feature is now live on Tweekly.fm! Say thank you to {{ $feature->user->username }}!</div>
    @endif

    @if ($feature->status == "new")
        @if ($votes->sum('weight') > 0)
            <div class="alert alert-success">This vote is currently passing and will get added to the service.</div>
        @else
            <div class="alert alert-danger">This vote is currently failing and won't get added to the service.</div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading">
                Vote
            </div>
            <div class="panel-body">
                <form method="post" action="{{ route('support.feature-request.vote', [ $feature->id])}}" class="pull-left" style="margin-right: 10px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" name="vote" class="btn btn-success" value="Yes">
                </form>

                <form method="post" action="{{ route('support.feature-request.vote', [ $feature->id])}}" class="pull-left">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="submit" name="vote" class="btn btn-danger" value="No">
                </form>

                @if ($vote->count() != 0)
                    <div class="pull-left" style="margin-left: 10px;">
                        @if ($vote->first()->weight == -1)
                            You previously voted <strong>no</strong> for this feature.
                        @else
                            You previously voted <strong>yes</strong> for this feature.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            Discussion
        </div>
        <div class="panel-body">
            @include("common.comments")
        </div>
    </div>

@stop
