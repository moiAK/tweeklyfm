@section("title")
    Scott Wilcox
@stop

@include("layout.partials.header")

<div class="tweekly-brand-background">
    <br/><br />
    <div class="text-center" style="padding-top: 18px !important;">
        <iframe style="width: 100%; min-height: 450px;" src="https://www.youtube.com/embed/_DboMAghWcA" frameborder="0" allowfullscreen></iframe>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
            <h2>{{ $user->name }}</h2>
            <p>This user has been a member of Tweekly.fm for 5 years, 14 days.</p>
        </div>
        <div class="col-md-2">&nbsp;</div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-1">
            <div class="playPosition">1</div>
        </div>
        <div class="col-md-5">
            <h3 style="margin-top: 0px;">Rise Against</h3>
            <span>Hero of War, Smarter Man</span>
        </div>
        <div class="col-md-4"><h4 style="margin-top: 15px;">3,214 plays this week</h4></div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-1">
            <div class="playPosition">2</div>
        </div>
        <div class="col-md-5">
            <h3 style="margin-top: 0px;">Bruce Springsteen</h3>
            <span>Atlantic City, Badlands, Born in the USA</span>
        </div>
        <div class="col-md-4"><h4 style="margin-top: 15px;">161 plays this week</h4></div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-1">
            <div class="playPosition">3</div>
        </div>
        <div class="col-md-5">
            <h3 style="margin-top: 0px;">Green Day</h3>
            <span>Amy, Jesus of Suburbia, Christie Road, Longview</span>
        </div>
        <div class="col-md-4"><h4 style="margin-top: 15px;">47 plays this week</h4></div>
    </div>

    <hr>



    <div class="row">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-6 text-center">
            <a class="btn tweekly-brand-button" href="/auth/register">Add Friend on Last.fm</a>
            <a class="btn tweekly-brand-button" href="/auth/register">Follow @ssx on Twitter</a>
        </div>
        <div class="col-md-3">&nbsp;</div>
    </div>

    <hr>

</div>

@include("layout.partials.footer")
