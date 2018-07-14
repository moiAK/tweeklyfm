<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta property="og:title" content="Tweekly.fm"/>
    <meta property="og:description" content="Tweekly.fm posts your most played artists from last.fm to Twitter or Facebook once per week."/>
    <meta property="og:image" content="https://tweekly.fm/media/image/logo.png"/>
    <meta name="google-site-verification" content="AJrwoq-yGrWEl5vxIPvFn-lua9we7dDyXiB_AbKalkU"/>

    <title>Tweekly.fm | @yield('title', 'Post your Last.fm data to social networks like Facebook and Twitter.')</title>

    <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <link href="/css/languages.min.css" rel="stylesheet">
    <link href="/style.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Tweekly.fm</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/about">{{ trans('common.menu.about') }}</a></li>
                <li><a id="open-beamer" href="#">News/Updates</a></li>
                <li><a href="https://github.com/tweeklyfm/tweeklyfm/issues/new">{{ trans('common.menu.support') }}</a></li>
                @if (Auth::guest())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ trans('common.menu.account') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/auth/login">{{ trans('common.login') }}</a></li>
                            <li><a href="/auth/register">{{ trans('common.register') }}</a></li>
                        </ul>
                    </li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/home">{{ trans('dashboard.dashboard') }}</a></li>
                            <li><a href="/auth/logout">{{ trans('common.logout') }}</a></li>
                        </ul>
                    </li>
                @endif
                <li class="dropdown" id="language-switcher">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                         <span class="lang-sm lang-lbl" lang="{{ App::getLocale() }}"></span> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/locale/en"><span class="lang-sm lang-lbl-full" lang="en"></span></a></li>
                        <li><a href="/locale/pt"><span class="lang-sm lang-lbl-full" lang="pt"></span></a></li>
                        <li><a href="/locale/de"><span class="lang-sm lang-lbl-full" lang="de"></span></a></li>
                        <li><a href="/locale/es"><span class="lang-sm lang-lbl-full" lang="es"></span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

@if (session("success"))
    <div class="container" style="margin-top: 70px; margin-bottom: -80px;">
        <div class="alert alert-success">
            {{ session("success") }}
        </div>
    </div>
@endif

@if (count($errors) > 0)
    <div class="container" style="margin-top: 70px; margin-bottom: -80px;">
        <div class="alert alert-danger">
            <strong>Error:</strong><br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
