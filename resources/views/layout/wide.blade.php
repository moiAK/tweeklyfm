@include("layout.partials.header")

<div class="container navbar-container">

    <div class="alert alert-info">
        <a target="_blank" href="https://www.patreon.com/user?u=5066287">All subscriptions have been cancelled. This service is now free to use. If you would still like to support development and/or donate to this service, click here to visit Patreon to do so.</a>
    </div>

    @if (Session::has('flash_notification.message'))
        <div class="alert alert-{{ Session::get('flash_notification.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            {{ Session::get('flash_notification.message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            @yield("content")
        </div>
    </div>
</div>

@include("layout.partials.footer")
