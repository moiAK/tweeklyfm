@include("layout.partials.header")

<div class="container navbar-container">

    <div id="notification-holder"></div>

    @if (Session::has('flash_notification.message'))
        <div class="alert alert-{{ Session::get('flash_notification.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            {{ Session::get('flash_notification.message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('dashboard.your_tweekly') }}
                </div>
                <div class="panel-body">
                    <div class="collapse in left-menu">
                        <div class="filter-list list-group">
                            <a href="/home">
                                <li class="list-group-item">
                                    @if (isset($notifications) && ($notifications !== 0))
                                        <span class="badge">{{ number_format(count($notifications)) }}</span>
                                    @endif
                                    <i class="fa fa-bell fa-fw"></i> {{ trans('dashboard.dashboard') }}
                                </li>
                            </a>
                            <br>
                            <a href="/premium/visual">
                                <li class="list-group-item">
                                    <i class="fa fa-photo fa-fw"></i> {{ trans('dashboard.visual_post') }}
                                </li>
                            </a>
                            <br>
                            <a href="/settings/sources">
                                <li class="list-group-item">
                                    <i class="fa fa-lastfm fa-fw"></i> {{ trans_choice('dashboard.source', 2) }}
                                </li>
                            </a>
                            <a href="/settings/connections">
                                <li class="list-group-item">
                                    <i class="fa fa-twitter fa-fw"></i> {{ trans_choice('dashboard.connection', 2) }}
                                </li>
                            </a>
                            <a href="/publish/start">
                                <li class="list-group-item">
                                    <i class="fa fa-book fa-fw"></i> {{ trans('dashboard.publish_now') }}
                                </li>
                            </a>
                            <a href="/scheduled">
                                <li class="list-group-item">
                                    <i class="fa fa-calendar fa-fw"></i> {{ trans('dashboard.scheduled_posts') }}
                                </li>
                            </a>
                            <br>
                            <a href="/profile">
                                <li class="list-group-item">
                                    <i class="fa fa-cog fa-fw"></i> {{ trans_choice('dashboard.setting', 2) }}
                                </li>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            @yield("content")
        </div>
    </div>
</div>

@include("layout.partials.footer")
