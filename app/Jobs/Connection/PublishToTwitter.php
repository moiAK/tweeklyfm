<?php

/*
 * This file is part of tweeklyfm/tweeklyfm
 *
 *  (c) Scott Wilcox <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace App\Jobs\Connection;

use App\Jobs\Job;
use App\Logic\Common\Helper\OAuthHelper;
use App\Models\Connection;
use App\Models\Notification;
use App\Models\Update;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class PublishToTwitter.
 */
class PublishToTwitter extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var User
     */
    protected $user;
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var
     */
    protected $status;

    /**
     * Create a new job instance.
     *
     * @param User       $user       The user instance.
     * @param Connection $connection
     * @param $status
     */
    public function __construct(User $user, Connection $connection, $status)
    {
        $this->user = $user;
        $this->connection = $connection;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Remove job if its fails more than 2 times
            if ($this->attempts() > 2) {
                $this->delete();
            }

            $twitter = OAuthHelper::query(
                $this->user,
                env('TWITTER_CLIENT_ID'),
                env('TWITTER_CLIENT_SECRET'),
                $this->connection->oauth_token,
                $this->connection->oauth_token_secret,
                'post',
                'https://api.twitter.com/1.1/',
                'statuses/update.json',
                [
                    'status' => $this->status,
                ]
            );

            // Test to see if our update was successful
            if ($twitter != false) {
                // Add a notification to the system to say that its posted
                $notification = new Notification();
                $notification->user_id = $this->user->id;
                $notification->message = 'Published update to Twitter (Q1): '.$twitter['id_str'];
                $notification->save();

                // Store this update historically
                $update = new Update();
                $update->user_id = $this->user->id;
                $update->content = $this->status;
                $update->network = $this->connection->network_name;
                $update->published_at = Carbon::now();
                $update->external_id = 'Published update to Twitter (Q1): '.$twitter['id_str'];
                $update->log = 'Successfully posted';
                $update->save();
            } else {
                // Add a notification to alert the user that it failed to post
                $notification = new Notification();
                $notification->user_id = $this->user->id;
                $notification->message = 'Failed to publish update to Twitter (Q1): '.$this->status;
                $notification->save();

                $this->release();
            }
        } catch (Exception $e) {
            Log::error('Queue publish error: '.$e->getMessage());
        }
    }
}
