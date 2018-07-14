@include("layout.partials.header")

<div class="jumbotron tweekly-brand-background">
    <div class="container">
        <div class="col-md-4">&nbsp;</div>
        <div class="text-center padding20 height20 col-md-4">
            <img src="https://pbs.twimg.com/profile_images/561527850231021568/9s1cnaSw_400x400.png" alt="Tweekly.fm">
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-6 text-center">
            <p>{{ trans('home.welcome') }}</p>

            <p>{{ trans('home.strapline') }}</p>
            
            <p>If you would like to help keep the service running, you can <a href="https://www.patreon.com/user?u=5066287">donate here</a>.</p>
        </div>
        <div class="col-md-3">&nbsp;</div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-6 text-center">
            <a class="btn tweekly-brand-button" href="/auth/login">{{ trans('common.login') }}</a>
            <a class="btn tweekly-brand-button" href="/auth/register">{{ trans('common.register') }}</a>
        </div>
        <div class="col-md-3">&nbsp;</div>
    </div>

    <hr>

</div>

@include("layout.partials.footer")
