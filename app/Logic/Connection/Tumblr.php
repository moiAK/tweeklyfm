<?php namespace App\Logic\Connection;

use Carbon\Carbon;
use App\Models\Update;
use App\Models\Notification;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Tumblr
{

    private $user, $connection, $status;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, Connection $connection, $status)
    {
        $this->user                         = $user;
        $this->connection                   = $connection;
        $this->status                       = (string)$status;
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

        $post_to_tumblr = \App\Logic\Common\Helper\OAuthHelper::query(
            Auth::user(),
            env("TUMBLR_CLIENT_ID"),
            env("TUMBLR_CLIENT_SECRET"),
            $connection->oauth_token,
            $connection->oauth_token_secret,
            'post',
            'https://api.tumblr.com/v2/',
            'blog/'.$connection->external_user_id.'.tumblr.com/post',
            [
                'type'  => 'text',
                'state' => 'published',
                'tweet' => 'off',
                'body'  => $this->status
            ]
        );

        if ($post_to_tumblr == true) {
            // Store this update historically
            $update                     = new Update;
            $update->user_id            = $user->id;
            $update->content            = $this->status;
            $update->network            = $connection->network_name;
            $update->published_at       = Carbon::now();
            $update->external_id        = "Published update to Tumblr: ".$post_to_tumblr["response"]["id"];
            $update->log                = "Successfully posted";
            $update->save();

            // Add a notification to the system to say that its posted
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Published update to Tumblr: ".$post_to_tumblr["response"]["id"];
            $notification->save();
        } else {
            // Add a notification to alert the user that it failed to post
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Failed to publish update to Tumblr";
            $notification->save();
        }

        // Finally return our status
        return $post_to_tumblr;
    }
}
