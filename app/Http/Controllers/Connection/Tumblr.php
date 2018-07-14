<?php namespace App\Http\Controllers\Connection;

use App\Http\Controllers\BaseController;
use App\Models\Connection;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite as Socialize;

class Tumblr extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getConnect(Request $request)
    {
        try {
            // Force removal of previous tokens
            $token = $request->get('oauth_token');
            $verify = $request->get('oauth_verifier');

            $tw = \OAuth::consumer('Tumblr');

            // if code is provided get user data and sign in
            if (!is_null($token) && !is_null($verify)) {
                // This was a callback request from twitter, get the token
                $token = $tw->requestAccessToken($token, $verify);

                $user_meta = \App\Logic\Common\Helper\OAuthHelper::query(
                    Auth::user(),
                    env("TUMBLR_CLIENT_ID"),
                    env("TUMBLR_CLIENT_SECRET"),
                    $token->getAccessToken(),
                    $token->getAccessTokenSecret(),
                    'get',
                    'https://api.tumblr.com/v2/',
                    'user/info',
                    []
                );

                if ($user_meta == true) {
                    // Get and store the username
                    $username = $user_meta["response"]["user"]["name"];

                    $connection = Connection::firstOrNew([
                        "user_id" => Auth::user()->id,
                        "network_id" => 5,
                        "network_name" => "tumblr",
                        "external_user_id" => $username
                    ]);

                    $connection->user_id = Auth::user()->id;
                    $connection->network_id = 5;
                    $connection->network_name = "tumblr";
                    $connection->oauth_token = $token->getAccessToken();
                    $connection->oauth_token_secret = $token->getAccessTokenSecret();
                    $connection->external_name = $username;
                    $connection->external_user_id = $username;
                    $connection->external_username = $username;
                    $connection->external_avatar = 'https://www.google.com/s2/favicons?domain_url='.urlencode('http://'.$username.'.tumblr.com');
                    $connection->checked_at = Carbon::now();
                    $connection->expires_at = null;
                    $connection->message = "Successfully connected";
                    $connection->save();

                    $notification = new Notification();
                    $notification->user_id = Auth::user()->id;
                    $notification->message = "Added Tumblr Connection: " . $username;
                    $notification->save();

                    Mail::send('emails.connection-added', [
                        'network' => 'Tumblr',
                        'avatar' => "",
                        'name' => $username
                    ], function ($message) use ($username) {
                        $message->to(Auth::user()->email, Auth::user()->name)->subject('Tumblr Connection Added: ' . $username);
                    });

                    // Store the connection_id for a moment
                    Session::put("connection_id", $connection->id);

                    return Redirect::to("/connect/tumblr/select");
                } else {
                    Flash::error("Unable to get a token from Tumblr. Please go back and try again.");

                    return Redirect::to("/settings/connections");
                }
            } else {
                // get request token
                $reqToken = $tw->requestRequestToken();

                // get Authorization Uri sending the request token
                $url = $tw->getAuthorizationUri(['oauth_token' => $reqToken->getRequestToken()]);

                // return to twitter login url
                return redirect((string)$url);
            }
        } catch (\Exception $e) {
            Flash::error("Unable to get a token from Tumblr. Please go back and try again.");

            return Redirect::to("/settings/connections");
        }
    }

    public function getSelectBlog(Request $request)
    {
        $input = Input::all();

        $user = Auth::user();
        $connection = $user->connections()->where("id", "=", Session::get("connection_id"));

        if ($connection->count() != 1) {
            abort(403);
        }

        $connection = $connection->first();

        $user_meta = \App\Logic\Common\Helper\OAuthHelper::query(
            Auth::user(),
            env("TUMBLR_CLIENT_ID"),
            env("TUMBLR_CLIENT_SECRET"),
            $connection->oauth_token,
            $connection->oauth_token_secret,
            'get',
            'https://api.tumblr.com/v2/',
            'user/info',
            []
        );

        if ($user_meta == true) {
            return view("settings.connections.select")->with('blogs', $user_meta["response"]["user"]["blogs"]);
        } else {
            Flash::error("Unable to get a blog list from Tumblr. Please go back and try again.");
            $connection->delete();
            return Redirect::to("/settings/connections");
        }
    }

    public function postSelectBlog(Request $request)
    {
        $input = Input::all();

        $user = Auth::user();
        $connection = $user->connections()->where("id", "=", Session::get("connection_id"));

        if ($connection->count() != 1) {
            abort(403);
        }

        $connection = $connection->first();
        $connection->external_user_id = $input["blog_id"];
        $connection->save();

        Session::remove("connection_id");

        Flash::success("You have successfully added the tumblr blog '".$input["blog_id"]."'.");

        return Redirect::to("/settings/connections");
    }
}
