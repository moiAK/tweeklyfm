<?php namespace App\Commands;

use App\Commands\Command;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Support\Facades\Mail;
use App\Models\Update;
use App\Models\Notification;
use App\Models\Connection;
use App\Models\User;

/**
 * Class PostUpdateToTwitter
 * @package App\Commands
 */
class PostUpdateToTwitter extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;
    /**
     * @var User
     */
    /**
     * @var Connection|User
     */
    private $user, $connection, $status;
    private $apiVersion                     = "1.1";
    private $consumer_key                   = "";
    private $consumer_secret                = "";
    private $access_token                   = "";
    private $access_token_secret            = "";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, Connection $connection, $status)
    {
        $this->user                         = $user;
        $this->connection                   = $connection;
        $this->status                       = $status;
        $this->consumer_key                 = env("TWITTER_CLIENT_ID");
        $this->consumer_secret              = env("TWITTER_CLIENT_SECRET");
        $this->access_token                 = $connection->oauth_token;
        $this->access_token_secret          = $connection->oauth_token_secret;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $user                               = $this->user;
        $connection                         = $this->connection;

        try {
            $client = new \Guzzle\Http\Client('https://api.twitter.com/{version}', array(
                'version'                   => $this->apiVersion
            ));

            // Sign all requests with the OAuthPlugin
            $client->addSubscriber(new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                "consumer_key"              => $this->consumer_key,
                "consumer_secret"           => $this->consumer_secret,
                "token"                     => $this->access_token,
                "token_secret"              => $this->access_token_secret
            )));

            $request = $client->post('statuses/update.json', null, array(
                'status'                    => $this->status
            ));

            $response                       = $request->send()->json();

            if (isset($response["id_str"])) {
                // Add a notification to the system to say that its posted
                $notification               = new Notification();
                $notification->user_id      = $user->id;
                $notification->message      = "Published update to Twitter: ".$response["id_str"];
                $notification->save();

                // Store this update historically
                $update                     = new Update;
                $update->user_id            = $user->id;
                $update->content            = $this->status;
                $update->network            = $connection->network_name;
                $update->published_at       = Carbon::now();
                $update->external_id        = "Published update to Twitter: ".$response["id_str"];
                $update->log                = "Successfully posted";
                $update->save();
            } else {
                // Add a notification to alert the user that it failed to post
                $notification               = new Notification();
                $notification->user_id      = $user->id;
                $notification->message      = "Failed to publish update to Twitter: ".$this->status;
                $notification->save();
            }
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $exception) {
            // 99% of the time this will be an invalid token error

            // Add a notification to alert the user that it failed to post
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Failed to publish update to Twitter: ClientErrorResponseException: " . $exception;
            $notification->save();
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $exception) {
            // Server is having capacity issues, requeue this job
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Failed to publish update to Twitter: ServerErrorResponseException: " . $exception;
            $notification->save();
        } catch (\Guzzle\Http\Exception\BadResponseException $exception) {
            // This should rarely happen, it means the response back from the server was
            // invalid, which means a connectivity issue usually
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Failed to publish update to Twitter: BadResponseException: " . $exception;
            $notification->save();
        } catch (\Exception $exception) {
            $notification               = new Notification();
            $notification->user_id      = $user->id;
            $notification->message      = "Failed to publish update to Twitter: Exception: " . $exception;
            $notification->save();
        }
    }
}
