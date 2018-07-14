@extends("layout.settings")

@section("content")
    <form method="post" action="/publish/start">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Take Data From:
                </div>
                <div class="panel-body">
                    <select name="source" class="col-md-12">
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}">{{ $source->network_name }} - {{ $source->external_username }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Post Data To:
                </div>
                <div class="panel-body">
                    <select name="connection" class="col-md-12">
                        @foreach ($connections as $connection)
                            <option value="{{ $connection->id }}">{{ $connection->network_name }} - {{ $connection->external_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Publish
                </div>
                <div class="panel-body">
                    <input type="submit" class="btn btn-primary" value="Publish">
                </div>
            </div>
        </div>
    </form>
@stop
