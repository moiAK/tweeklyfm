@extends("layout.settings")

@section("content")
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Welcome!
                </div>
                <div class="panel-body">
                    The fact that you can see this page means you're one of the great fans of this service that has
                    taken the time to stick by the service and see it grow.<br>
                    <br>
                    The past few months have seen a complete rewrite from top to bottom of the systems that power this
                    site and to prepare for mobile apps arriving later this year.<br>
                    <br>
                    The order in which these are implemented depends on you folks, obviously auto-posting is the most
                    important at the moment. I'm hoping to have that complete within a few days (ideally before tuesday).<br>
                    <br>
                    If there is a feature you would like to see turned back on, written or if you have an idea, get in touch
                    using the support link at the top of the page and I'm more than happy to listen.<br>
                    <br>
                    <a href="http://twitter.com">Scott<br>@ssx</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    What's next?
                </div>
                <div class="panel-body">
                    <ul>
                        <li style="text-decoration: line-through">Auto-publishing turned back on for you fine folks</li>
                        <li>Fix Facebook integration (this is to do with their rule change in April)</li>
                        <li>Tweet/Update customisation</li>
                        <li>Next integration: Wordpress, Instagram</li>
                        <li>Profile pages</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @include("common.comments")
    </div>
@stop
