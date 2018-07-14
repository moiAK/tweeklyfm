@extends("layout.settings")

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Scheduled Posts
        </div>
        <div class="panel-body">
            <p>Scheduled posts allow you to set your updates to be sent automatically.</p>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Current Scheduled Posts
            <div class="pull-right">
                <a href="/scheduled/create" class="btn btn-primary btn-xs">Add Scheduled Post</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered">
                <tr>
                    <th class="col-md-2">Created</th>
                    <th class="col-md-3">Source</th>
                    <th class="col-md-3">Connection</th>
                    <th class="col-md-1">Status</th>
                    <th class="col-md-1">Day</th>
                    <th class="col-md-1">Hour</th>
                    <th class="col-md-1">&nbsp;</th>
                </tr>
                @if (count($scheduled) < 1)
                    <tr>
                        <td colspan="7" class="text-center">
                            You have no scheduled posts.
                        </td>
                    </tr>
                @else
                    @foreach ($scheduled as $scheduled_item)
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($scheduled_item->created_at))->diffForHumans() }}</td>
                            <td>{{ ucwords($scheduled_item->source()->first()->network()->first()->name) }}</td>
                            <td>{{ ucwords($scheduled_item->connection()->first()->network()->first()->name) }}</td>
                            <td class="text-center">{{ ucwords($scheduled_item->status) }}</td>
                            <td class="text-center">{{ ucwords($scheduled_item->post_day) }}</td>
                            <td class="text-center">{{ $scheduled_item->post_hour }}-{{ $scheduled_item->post_hour+1 }}</td>
                            <td class="text-center"><a href="/scheduled/{{ $scheduled_item->id }}/delete" class="btn btn-xs btn-danger">Remove</a></td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>

@stop
