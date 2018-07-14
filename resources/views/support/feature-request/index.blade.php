@extends("layout.settings")

@section("navigation.title")

@stop

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Feature Requests
            <?php
            /*
            <div class="pull-right">
                <a href="{{ route("support.feature-request.create") }}" class="btn btn-primary btn-xs">Request New Feature</a>
            </div>
            */
            ?>
        </div>
        <div class="panel-body">
            If there is a feature you would like to see, perhaps something from the old service that you miss or
            something completely new. Post it here and we'll add the most popular ones in to the service. You can view
            the current list of feature requests below, click 'View' to view more information about each one and to
            vote on it.
        </div>
    </div>

    <br>

    @if ($features->count() > 0)

        @foreach ($features as $category)
            @if ($category->features->count() > 0)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ $category->name }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            @foreach ($category->features as $feature)
                                <tr>
                                    <td>
                                        <div class="pull-right">
                                            <a class="btn btn-xs btn-primary" href="{{ route('support.feature-request.view', [ $feature->id ]) }}">View</a>
                                        </div>

                                        {{ $feature->title }}<br>
                                        <small>Requested by {{ $feature->user->username }}, {{ $feature->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif

        @endforeach


    @else
        <p>There are currently no feature requests in the system.</p>
    @endif

@stop
