<?php namespace App\Logic\Connection;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Update;
use App\Models\Notification;
use App\Models\Connection;
use App\Models\User;
use Maknz\Slack\Facades\Slack;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class Facebook
{

    private $user, $connection, $status;
    private $apiVersion                     = "1.1";
    private $consumer_key                   = "";
    private $consumer_secret                = "";
    private $access_token                   = "";
    private $access_token_secret            = "";
    private $facebook;

    /**
     * Create a new command instance.
     *
     */
    public function __construct(User $user, Connection $connection, $status)
    {
        $this->user                         = $user;
        $this->connection                   = $connection;
        $this->status                       = (string)$status;
        $this->consumer_key                 = env("FACEBOOK_CLIENT_ID");
        $this->consumer_secret              = env("FACEBOOK_CLIENT_SECRET");
        $this->access_token                 = (string)$connection->oauth_token;
        $this->access_token_secret          = "";

        $this->facebook                     = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
    }

    /**
     * Execute the command.
     *
     * @return boolean
     */
    public function post()
    {
        $user                               = $this->user;
        $connection                         = $this->connection;

        $facebook_app = $user->connection_facebook_app();

        if ($facebook_app->exists()) {
            // Build a status update
            if ($user->isPremium()) {
                $status_data = array(
                    'message'   => $this->status
                    // 'picture'   => 'https://tweekly.fm/image/visual/'.$user->username.'.png'
                );
            } else {
                $status_data = array(
                    'message'   => $this->status
                );
            }

            // Build a new instance of the SDK with the users app_id and app_secret
            $facebook =  $this->facebook->newInstance([
                'app_id'                => $facebook_app->first()->app_id,
                'app_secret'            => $facebook_app->first()->app_secret,
                'default_graph_version' => 'v2.5'
            ]);

            // Attempt to post
            try {
                $response = $facebook->post('/me/feed', $status_data, $connection->oauth_token);
                $graphNode = $response->getGraphNode()->asArray();

                if (isset($graphNode["id"])) {
                    // Add a notification to the system to say that its posted
                    $notification               = new Notification();
                    $notification->user_id      = $user->id;
                    $notification->message      = "Published update to Facebook: ".$graphNode["id"];
                    $notification->save();

                    // Store this update historically
                    $update                     = new Update;
                    $update->user_id            = $user->id;
                    $update->content            = $this->status;
                    $update->network            = $connection->network_name;
                    $update->published_at       = Carbon::now();
                    $update->external_id        = "Published update to Facebook: ".$graphNode["id"];
                    $update->log                = "Successfully posted";
                    $update->save();

                    return true;
                } else {
                    // Add a notification to alert the user that it failed to post
                    $notification               = new Notification();
                    $notification->user_id      = $user->id;
                    $notification->message      = "Failed to publish update to Facebook: ".$this->status;
                    $notification->save();
                    return false;
                }
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                $notification               = new Notification();
                $notification->user_id      = $user->id;
                $notification->message      = "Failed to publish update to Facebook: FacebookResponseException";
                $notification->save();
                return false;
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                // Server is having capacity issues, requeue this job
                $notification               = new Notification();
                $notification->user_id      = $user->id;
                $notification->message      = "Facebook rejected the API call: FacebookSDKException";
                $notification->save();
                return false;
            }
        }

        return true;
    }
}
