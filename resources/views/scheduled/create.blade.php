@extends("layout.settings")

@section("content")
    <div class="panel panel-default">
        <div class="panel-heading">
            Create New Scheduled Post
        </div>
        <div class="panel-body">
            <form method="post" action="/scheduled/create">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="col-md-3">
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

                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Post Data To:
                        </div>
                        <div class="panel-body">
                            <select name="connection" class="col-md-12">
                                @foreach ($connections as $connection)
                                    <option value="{{ $connection->id }}">{{ $connection->network_name }} - {{ $connection->external_username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Day
                        </div>
                        <div class="panel-body">
                            <select name="day" class="col-md-12">
                                <option value="sun">Sunday</option>
                                <option value="mon">Monday</option>
                                <option value="tue">Tuesday</option>
                                <option value="wed">Wednesday</option>
                                <option value="thu">Thursday</option>
                                <option value="fri">Friday</option>
                                <option value="sat">Saturday</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Time
                        </div>
                        <div class="panel-body">
                            <select name="time" class="col-md-12">
                                <option value="08">Morning</option>
                                <option value="14">Afternoon</option>
                                <option value="18">Evening</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <input type="submit" value="Create Scheduled Post" class="btn btn-primary" />
                </div>
            </form>
        </div>
    </div>

@stop
