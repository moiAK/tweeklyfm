<?php namespace App\Http\Controllers;

use App\Logic\Common\CreateTumblrUpdateFromLastFM;
use App\Logic\Common\CreateWordpressUpdateFromLastFM;
use App\Logic\Connection\Tumblr;
use App\Logic\Common\CreateFacebookUpdateFromLastFM;
use App\Logic\Connection\Facebook;
use App\Logic\Connection\Twitter;
use App\Logic\Connection\Wordpress;
use App\Logic\Source\LastFM;
use App\Logic\Common\CreateTwitterUpdateFromLastFM;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use Maknz\Slack\Facades\Slack;

/**
 * Class PublishController
 * @package App\Http\Controllers
 */
class PublishController extends BaseController
{

    use DispatchesJobs;

    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders your application's "dashboard" for users that
    | are authenticated. Of course, you are free to change or remove the
    | controller as you wish. It is just here to get your app started!
    |
    */

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }


    public function getPublishStart()
    {
        $this->data["connections"]  = Auth::user()->connections;
        $this->data["sources"]      = Auth::user()->sources;

        if ((count($this->data["sources"]) > 0) && (count($this->data["connections"]) > 0)) {
            return view('publish.start', $this->data);
        } else {
            return view('publish.not-configured', $this->data);
        }
    }


    public function postPublishStart()
    {
        $this->data["connections"] = Auth::user()->connections;
        $this->data["sources"] = Auth::user()->sources;

        $input = Input::all();

        if (!isset($input["connection"]) || (!isset($input["source"]))) {
            Flash::error("No connection or source selected");
            return Redirect::to("/publish/start");
        }

        $source = Auth::user()->sources()->where("id", "=", $input["source"]);
        $connection = Auth::user()->connections()->where("id", "=", $input["connection"]);

        // Safety net
        if (($source->count() == 0) || ($connection->count() == 0)) {
            return view('publish.not-configured', $this->data);
        }

        $source = $source->first();
        $connection = $connection->first();

        if ($source->network_name == "lastfm") {
            // Update from Last.fm
            $network = new LastFM(Auth::user(), $source);

            // Make the request to pull data
            $artists = $network->pull();

            if (count($artists->getItems()) == 0) {
                Flash::error("Last.fm is reporting nothing to publish for your account.");

                return Redirect::to("/publish/nothing-to-publish");
            }

            // Post to Twitter?
            if ($connection->network_name == "twitter") {
                // Build an update from the artists given back
                $update = new CreateTwitterUpdateFromLastFM($artists->getItems(), Auth::user());

                $job = (new \App\Jobs\Connection\PublishToTwitter(Auth::user(), $connection, (string)$update))->onQueue('publish.twitter');
                $this->dispatch($job);

                return Redirect::to("/publish/success");
            }

            // Post to Facebook?
            if ($connection->network_name == "facebook") {
                // Build an update from the artists given back
                $update = new CreateFacebookUpdateFromLastFM($artists->getItems(), Auth::user());

                $facebook = new Facebook(Auth::user(), $connection, $update);
                $facebook->post();

                return Redirect::to("/publish/success");
            }

            // Post to Tumblr?
            if ($connection->network_name == "tumblr") {
                // Build an update from the artists given back
                $update = new CreateTumblrUpdateFromLastFM($artists->getItems(), Auth::user());

                $tumblr = new Tumblr(Auth::user(), $connection, $update);
                $tumblr->post();

                return Redirect::to("/publish/success");
            }


            // Post to Wordpress?
            if ($connection->network_name == "wordpress") {
                // Build an update from the artists given back
                $update = new CreateWordpressUpdateFromLastFM($artists->getItems(), Auth::user());

                $wordpress = new Wordpress(Auth::user(), $connection, $update);
                $wordpress->post();

                return Redirect::to("/publish/success");
            }


            Flash::error("Unable to publish to your selected network");
            return Redirect::to("/publish/error");
        }

        // Friendly error message
        Flash::error("Unable to publish to fetch data from your selected source");

        // Fall back to returning error publishing
        return Redirect::to("/publish/error");
    }


    public function getPublishSuccess()
    {
        return view('publish.published', $this->data);
    }


    public function getPublishError()
    {
        return view('publish.error', $this->data);
    }


    public function getPublishNothingToPublish()
    {
        return view('publish.nothing-to-publish', $this->data);
    }
}
