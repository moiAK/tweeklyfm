<?php namespace App\Logic\Connection;

use Carbon\Carbon;
use App\Models\Update;
use App\Models\Notification;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Wordpress
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

        $curl_post = curl_init('https://public-api.wordpress.com/rest/v1.1/sites/'.$connection->external_user_id.'/posts/new');
        curl_setopt($curl_post, CURLOPT_USERAGENT, 'Tweekly.fm <scott@tweekly.fm>');
        curl_setopt($curl_post, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$connection->oauth_token));
        curl_setopt($curl_post, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_post, CURLOPT_POST, true);
        curl_setopt($curl_post, CURLOPT_POSTFIELDS, array(
            "date"              => date("c"),
            "title"             => 'My Top Artists from Last.fm',
            "content"           => $this->status,
            "status"            => "publish",
            "tags"              => "music",
        ));
        $post                   = curl_exec($curl_post);
        $post_info              = curl_getinfo($curl_post);

        if ($post_info["http_code"] == 200) {
            $post_data = json_decode($post);

            $post_id = $post_data->ID;
            $post_url = $post_data->URL;

            // Store this update historically
            $update                     = new Update;
            $update->user_id            = $user->id;
            $update->content            = $this->status;
            $update->network            = $connection->network_name;
            $update->published_at       = Carbon::now();
            $update->external_id        = "Published update to Wordpress: ID: ".$post_id;
            $update->log                = "Successfully posted";
            $update->save();

            // Add a notification to the system to say that its posted
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Published update to Wordpress: ID: ".$post_id;
            $notification->save();

            return true;
        } else {
            // Add a notification to alert the user that it failed to post
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Failed to publish update to Wordpress";
            $notification->save();

            return false;
        }
    }
}
