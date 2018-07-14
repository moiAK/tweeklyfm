@extends("layout.settings")

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Connections
        </div>
        <div class="panel-body">
            <p>Connections provide a destination for Tweekly.fm to publish data to. You can see your currently configured connections below or add new ones by clicking the buttons at the bottom of this page.</p>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Current Connections
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered">
                <tr>
                    <th class="col-md-1">&nbsp;</th>
                    <th class="col-md-2">Last Checked</th>
                    <th class="col-md-1">Type</th>
                    <th class="col-md-1">Status</th>
                    <th class="col-md-2">User/Blog</th>
                    <th class="col-md-3">Message</th>
                    <th class="col-md-1">&nbsp;</th>
                </tr>
                @if (count($connections) < 1)
                    <tr>
                        <td colspan="7" class="text-center">
                            You have no connections added.
                        </td>
                    </tr>
                @else
                    @foreach ($connections as $connection)
                        <tr>
                            <td class="text-center"><img src="{{ $connection->external_avatar }}" alt="{{ $connection->external_username }}" class="img-circle" width="25" /></td>
                            <td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($connection->checked_at))->diffForHumans() }}</td>
                            <td class="text-center">{{ ucwords($connection->network_name) }}</td>
                            <td class="text-center">{{ ucwords($connection->status) }}</td>
                            @if ($connection->network_name == "tumblr")
                                <td>{{ $connection->external_user_id }}</td>
                            @elseif ($connection->network_name == "wordpress")
                                <td>{{ $connection->external_name }}</td>
                            @else
                                <td>{{ $connection->external_name }} @if ($connection->external_username != NULL) ({{ $connection->external_username }})@endif</td>
                            @endif

                            <td>{{ $connection->message }}</td>
                            <td class="text-center">
                                <a href="/connection/remove/{{ $connection->id }}" class="btn btn-xs btn-danger">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Add New Connection
        </div>
        <div class="panel-body">
            <a class="btn btn-primary" href="/connect/twitter/go"><i class="fa fa-twitter"></i> Twitter</a>
            <a class="btn btn-primary" href="/connect/facebook/app"><i class="fa fa-facebook"></i> Facebook</a>
            <a class="btn btn-primary" href="/connect/tumblr/go"><i class="fa fa-tumblr"></i> Tumblr</a>
            <a class="btn btn-primary" href="/connect/wordpress/go"><i class="fa fa-wordpress"></i> Wordpress</a>
            <a class="btn btn-primary" disabled="disabled"><i class="fa fa-instagram"></i> Instagram</a>
            <a class="btn btn-primary" disabled="disabled"><i class="fa fa-adn"></i> App.net</a>
            <a class="btn btn-primary" disabled="disabled"><i class="fa fa-google-plus"></i> Google+</a>
        </div>
    </div>

@stop
