<?php namespace App\Http\Controllers\Connection;

use App\Http\Controllers\BaseController;
use App\Models\Connection;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite as Socialize;

class Spotify extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getConnect()
    {
        return Socialize::with('spotify')
                        ->scopes([
                            'user-read-email',
                            'user-follow-read',
                            'user-library-read',
                            'playlist-read-private',
                            'playlist-read-collaborative'
                        ])
                        ->redirect();
    }

    public function getCallback(Request $request)
    {
        // Get all the input vars
        $input = $request->all();

        // Get the current user
        $user = Auth::user();

        // check for error=access_denied
        if (isset($input["error"])) {
            Flash::error("The request for a token from Spotify was denied.");
            return Redirect::to("/settings/connections");
        }


        // Get the spotify data
        $spotify = Socialize::driver('spotify')->user();

        // Add a connection for this service
        $connection = Connection::firstOrNew([
            "user_id"           => $user->id,
            "network_id"        => 7,
            "network_name"      => "spotify",
            "external_user_id"  => $spotify->getId()
        ]);

        // Fill in the connection details
        $connection->user_id            = $user->id;
        $connection->network_id         = 7;
        $connection->network_name       = "spotify";
        $connection->oauth_token        = $spotify->token;
        $connection->oauth_token_secret = "";
        $connection->external_name      = $spotify->getName();
        $connection->external_user_id   = $spotify->getId();
        $connection->external_username  = $spotify->getNickname();
        $connection->external_avatar    = $spotify->getAvatar();
        $connection->checked_at         = Carbon::now();
        $connection->message            = "Successfully connected";
        $connection->save();

        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->message = "Added Spotify Connection: ".$spotify->getName();
        $notification->save();

        Mail::send('emails.connection-added', [
            'network'   => 'Spotify',
            'avatar'    => $spotify->getAvatar(),
            'name'      => $spotify->getName()
        ], function ($message) use ($user, $spotify) {
            $message->to($user->email, $user->name)->subject('Spotify Connection Added: '.$spotify->getName());
        });

        return Redirect::to("/settings/connections");
    }
}
