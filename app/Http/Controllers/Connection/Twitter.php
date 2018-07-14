<?php namespace App\Http\Controllers\Connection;

use App\Http\Controllers\BaseController;
use App\Logic\Common\ErrorLog;
use App\Models\Connection;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite as Socialize;

class Twitter extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getConnect()
    {
        return Socialize::with('twitter')->redirect();
    }


    public function getCallback(Request $request)
    {
        // Get all the input vars
        $input = $request->all();

        // Get the current user
        $user = Auth::user();

        // check for error=access_denied
        if (isset($input["denied"])) {
            Flash::error("The request for a token from Twitter was denied.");
            return Redirect::to("/settings/connections");
        }

        try {
            $twitter = Socialize::with('twitter')->user();

            $connection = Connection::firstOrNew([
                "user_id" => $user->id,
                "network_id" => 1,
                "network_name" => "twitter",
                "external_user_id" => $twitter->getId()
            ]);

            $connection->user_id = $user->id;
            $connection->network_id = 1;
            $connection->network_name = "twitter";
            $connection->oauth_token = $twitter->token;
            $connection->oauth_token_secret = $twitter->tokenSecret;
            $connection->external_name = $twitter->getName();
            $connection->external_user_id = $twitter->getId();
            $connection->external_username = $twitter->getNickname();
            $connection->external_avatar = $twitter->getAvatar();
            $connection->checked_at = Carbon::now();
            $connection->expires_at = null;
            $connection->message = "Successfully connected";
            $connection->save();

            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->message = "Added Twitter Connection: @" . $twitter->getNickname();
            $notification->save();

            Mail::send('emails.connection-added', [
                'network' => 'Twitter',
                'avatar' => $twitter->getAvatar(),
                'name' => $twitter->getName()
            ], function ($message) use ($user, $twitter) {
                $message->to($user->email, $user->name)->subject('Twitter Connection Added: @' . $twitter->getNickname());
            });

            Flash::success("You have successfully added a Twitter connection for ".$twitter->getNickname());

            return Redirect::to("/settings/connections");
        } catch (\Exception $e) {
            ErrorLog::log($e);

            Flash::error("The request for a token from Twitter failed, or you have blocked the app.");

            return Redirect::to("/settings/connections");
        }
    }
}
