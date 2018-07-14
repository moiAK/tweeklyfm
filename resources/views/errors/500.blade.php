@section("title", "Server Error")

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
            <div class="col-md-2">&nbsp;</div>
            <div class="col-md-8">
                <p class="text-center"><strong>Error:</strong> There was an error serving the page you requested. Please go back and try again.</p>
            </div>
            <div class="col-md-2">&nbsp;</div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-6 text-center">
                <a class="btn tweekly-brand-button" href="/">Return to Homepage</a>
            </div>
            <div class="col-md-2">&nbsp;</div>
        </div>

        <hr>

    </div>

    @include("layout.partials.footer")
