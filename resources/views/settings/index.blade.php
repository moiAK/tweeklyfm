@extends("layout.settings")

@section("content")

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ trans('dashboard.dashboard') }}
        </div>
        <div class="panel-body">
            {{ trans('dashboard.thank_you_signing_in') }}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            {{ trans_choice("dashboard.notification", 2) }}
        </div>
        <div class="panel">
            <table class="table table-striped">
                <tr>
                    <th class="col-md-3">{{ trans("dashboard.date") }}</th>
                    <th class="col-md-9">{{ trans_choice("dashboard.notification", 1) }}</th>
                </tr>
                @if ($notifications === 0)
                    <tr>
                        <td colspan="2" class="text-center">
                            {{ trans('dashboard.no_new_notifications') }}
                        </td>
                    </tr>
                @else
                    @foreach ($notifications as $notification)
                        <tr>
                            <td>{{ $notification->created_at }}</td>
                            <td>{{ $notification->message }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@stop
