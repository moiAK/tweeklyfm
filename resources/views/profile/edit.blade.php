@extends("layout.settings")

@section("navigation.title")

@stop

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Profile Settings
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <form method="post" action="{{ route("profile.update") }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <label for="flagLovedPost">
                        <input type="checkbox" id="publish_loved" name="publish_loved" value="1" @if ($user->getMeta("flag.lastfm.loved.autopost") == 1) checked="checked" @endif /> &nbsp; Automatically publish my loved Last.fm tracks
                    </label>

                    <br><br>

                    <div class="form-group">
                        <div class="col-md-4">
                            Number of Artists to Post:
                        </div>
                        <div class="col-md-8">
                            <select name="publish_max" class="form-control">
                                <option @if ($user->getMeta("publish.max") == 1) selected="selected" @endif>1</option>
                                <option @if ($user->getMeta("publish.max") == 2) selected="selected" @endif>2</option>
                                <option @if ($user->getMeta("publish.max") == 3) selected="selected" @endif>3</option>
                                <option @if ($user->getMeta("publish.max") == 4) selected="selected" @endif>4</option>
                                <option @if ($user->getMeta("publish.max") == 5) selected="selected" @endif>5</option>
                            </select>
                        </div>
                    </div>

                    <br><br>

                    <div class="form-group">
                        <div class="col-md-4">
                            Timezone
                        </div>
                        <div class="col-md-8">
                            <select name="timezone" class="form-control">
                                @foreach ($timezones as $timezone)
                                    <option @if ($user->timezone == $timezone) selected="selected" @endif>{{ $timezone }}</option>
                                @endforeach
                            </select>
                            <small>Select the closest timezone to your city.</small>
                        </div>
                    </div>

                    <br><br><br>

                    <div class="form-group">
                        <div class="col-md-4">
                            Hashtag (don't include #):
                            </div>
                        <div class="col-md-8">
                            <input id="hashtag" type="text" name="publish_hashtag" class="form-control" value="{{ $user->getMeta("publish.hashtag", "tweeklyfm") }}"/>
                            <small>Example: mm, music, tweeklyfm</small><br>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Update" />
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            Remove Account
        </div>
        <div class="panel-body">
            <div class="col-md-12">
                <p>If you would like to remove your account, please click the button below.</p>
                <form method="post" action="{{ route("profile.remove") }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <input type="submit"
                               class="btn btn-danger"
                               value="Remove Account"
                               onclick="return confirm('Are you sure you want to remove your account?');"  />
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
