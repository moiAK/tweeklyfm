@extends("layout.settings")

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            Emails
        </div>
        <div class="panel-body">
            <p>You can control the emails that Tweekly.fm sends you by using the configuration options below.</p>

            <form method="post" action="/settings/emails">
                <label class="checkbox-inline">
                    <input type="checkbox" id="inlineCheckbox1" value="Y"> Receive News & Updates from the Mailing List
                </label>
                <br>
                <label class="checkbox-inline">
                    <input type="checkbox" id="inlineCheckbox2" value="Y"> Email when Tweekly.fm detects a problem
                </label>
                <br>
                <input type="submit" class="btn tweekly-brand-button" value="Update" />
            </form>
        </div>
    </div>
@stop
