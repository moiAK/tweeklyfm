@extends("layout.settings")

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Sources
        </div>
        <div class="panel-body">
            <p>Sources provide tweekly.fm with data to build your update from. You can see your currently configured sources below or add new ones by clicking the Add button next to the service you want to add.</p>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Current Sources
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered">
                <tr>
                    <th class="col-md-1">&nbsp;</th>
                    <th class="col-md-2">Last Checked</th>
                    <th class="col-md-1">Type</th>
                    <th class="col-md-1">Status</th>
                    <th class="col-md-2">User</th>
                    <th class="col-md-3">Message</th>
                    <th class="col-md-1">&nbsp;</th>
                </tr>
                @if (count($sources) < 1)
                    <tr>
                        <td colspan="7" class="text-center">
                            You have no sources added.
                        </td>
                    </tr>
                @else
                    @foreach ($sources as $source)
                        <tr>
                            <td class="text-center"><i class="fa fa-lastfm fa-fw fa-2x"></i></td>
                            <td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($source->checked_at))->diffForHumans() }}</td>
                            <td class="text-center">{{ ucwords($source->network_name) }}</td>
                            <td class="text-center">{{ ucwords($source->status) }}</td>
                            <td>{{ $source->external_name }} @if ($source->external_username != NULL) ({{ $source->external_username }})@endif</td>
                            <td>{{ $source->message }}</td>
                            <td class="text-center"><a href="/source/remove/{{ $source->id }}" class="btn btn-xs btn-danger">Remove</a></td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Add New Source
        </div>
        <div class="panel-body">
            <a class="btn btn-primary" href="/connect/lastfm/go"><i class="fa fa-lastfm"></i> Last.fm</a>
            <a class="btn btn-primary" data-toggle="modal" data-target="#mdlSpotify"><i class="fa fa-spotify"></i> Spotify</a>
            <a class="btn btn-primary" disabled="disabled"><i class="fa fa-music"></i> Deezer</a>
        </div>
    </div>


    <div class="modal fade" id="mdlSpotify" tabindex="-1" role="dialog" aria-labelledby="mdlSpotify">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Spotify</h4>
                </div>
                <div class="modal-body">
                    <p>Spotify doesn't have an API at the moment that we can collect play data from  However there is a workaround. If you configure Spotify to scrobble to Last.fm then we can pull your data from there.</p>
                    <p>We are currently developing new features for Spotify which will use a linked account but that won't include your top artists.</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" href="/connect/spotify/go" target="_blank">Add Spotify Account</a>
                    <a class="btn btn-info" href="https://support.spotify.com/ee/learn-more/faq/#!/article/scrobble-to-last-fm" target="_blank">Setup Spotify with Last.fm</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@stop
