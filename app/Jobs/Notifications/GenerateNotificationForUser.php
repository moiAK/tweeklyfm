<?php namespace App\Jobs\Notifcations;

use App\Jobs\Job;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pusher;

/**
 * Class GenerateNotificationForUser
 *
 * @package App\Jobs\Notifcations
 */
class GenerateNotificationForUser extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var User
     */
    protected $user;
    /**
     * @var
     */
    protected $message;
    /**
     * @var
     */
    protected $type;

    /**
     * GenerateNotificationForUser constructor.
     *
     * @param User $user
     * @param $type
     * @param $message
     */
    public function __construct(User $user, $type, $message)
    {
        $this->user = $user;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     *
     */
    public function handle()
    {
        // Remove job if its fails more than once
        if ($this->attempts() > 1) {
            $this->delete();
        }

        // Get an instance of the Pusher class
        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));

        // Test to see whether or not the user is online
        $response = $pusher->get('/channels/private-user-'.$this->user->id);

        // Depending on the outcome, send either a push notice or an email
        if ($response["result"]["occupied"] == true) {
            // User is online somewhere, so we'll send a push notification
            $pusher->trigger('private-user-'.$this->user->id, 'notification', [
                "type"      => $this->type,
                'message'   => $this->message
            ]);
        } else {
            // User isn't online, so we'll email the notification instead
            echo "Send email instead";
        }
    }
}
