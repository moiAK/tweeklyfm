<?php namespace App\Console\Commands;

use App\Logic\Common\CreateFacebookUpdateFromLastFM;
use App\Logic\Common\CreateTumblrUpdateFromLastFM;
use App\Logic\Common\CreateTwitterUpdateFromLastFM;
use App\Logic\Common\CreateWordpressUpdateFromLastFM;
use App\Logic\Connection\Facebook;
use App\Logic\Connection\Tumblr;
use App\Logic\Connection\Twitter;
use App\Logic\Connection\Wordpress;
use App\Logic\Source\LastFM;
use App\Models\ScheduledPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Maknz\Slack\Facades\Slack;

class ScheduledPostCommand extends Command
{

    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tweekly:scheduled:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will publish the scheduled updates.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $scheduled = ScheduledPost::with("user");
        foreach ($scheduled->get() as $post) {
            try {
                $dateTimeUser = Carbon::now($post->user->timezone);
                $current_hour = $dateTimeUser->hour;
                $current_day = strtolower($dateTimeUser->format('D'));

                if (($current_hour === $post->post_hour) && ($current_day === $post->post_day)) {
                    $user = $post->user;

                    $source = $user->sources()->where("id", "=", $post->source_id)->first();
                    $connection = $user->connections()->where("id", "=", $post->connection_id)->first();

                    try {
                        if ($source->network_name == "lastfm") {
                            // Update from Last.fm
                            $network = new LastFM($user, $source);

                            // Make the request to pull data
                            $artists = $network->pull();

                            if (count($artists->getItems()) > 0) {
                                $this->info('[scheduled] User has updated from ' . $source->network_name . ': ' .
                                    $user->email);

                                if ($connection->network_name == "twitter") {
                                    $update = new CreateTwitterUpdateFromLastFM($artists->getItems(), $user);

                                    $job = (new \App\Jobs\Connection\PublishToTwitter(
                                        $user,
                                        $connection,
                                        (string)$update
                                    ))->onQueue('publish.twitter');
                                    $this->dispatch($job);
                                }

                                if ($connection->network_name == "facebook") {
                                    $update = new CreateFacebookUpdateFromLastFM($artists->getItems(), $user);

                                    $facebook = new Facebook($user, $connection, $update);
                                    $facebook->post();
                                }

                                // Post to Tumblr?
                                if ($connection->network_name == "tumblr") {
                                    // Build an update from the artists given back
                                    $update = new CreateTumblrUpdateFromLastFM($artists->getItems(), $user);

                                    $tumblr = new Tumblr($user, $connection, $update);
                                    $tumblr->post();
                                }

                                // Post to Wordpress?
                                if ($connection->network_name == "wordpress") {
                                    // Build an update from the artists given back
                                    $update = new CreateWordpressUpdateFromLastFM($artists->getItems(), $user);

                                    $wordpress = new Wordpress($user, $connection, $update);
                                    $wordpress->post();
                                }

                                $this->info('[scheduled] ' . $user->email . ': User has published update to ' .
                                    $connection->network_name);

                                // Update the timestamp
                                $post->last_message = 'Successfully published update to ' . $connection->network_name;
                                $post->posted_at = Carbon::now();
                            } else {
                                $post->last_message = 'Nothing to publish to ' . $connection->network_name;
                            }

                            $post->save();
                        }
                    } catch (\Exception $e) {
                        $this->error('[scheduled] ' . $user->email . ' - Error: ' . $e);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Failed to build post: ".$e->getMessage());
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [

        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [

        ];
    }
}
