@extends("layout.settings")

@section("content")
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Premium Subscriptions
                </div>
                <div class="panel-body">
                    <p>There is a subscription for users to allow access to a certain set of features, like
                        auto-publishing, update customisation and more.</p>

                    <p>Along with the benefits listed on this page, you help the website and service continue by
                        directly supporting the day to day running and support of the service.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Benefits of Premium
                </div>
                <div class="panel-body">
                    <ul>
                        <li>Schedule automatic posts to your connections</li>
                        <li>Set the number of artists in your update</li>
                        <li>Customisation options for updates</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @include("common.comments")
    </div>
@stop
