<?php namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;

class PerformFollowRequestsCommand extends Command
{

    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tweekly:scheduled:follow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This performs the make sure I am follow setting for users.';

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
        $query = DB::select("select * from meta where meta.key = 'flag.allow.auto-follow' and value = 1");

        foreach ($query as $result) {
            $user = User::find($result->metable_id);

            $this->info("Follow: ".$user->name);

            $connections = $user->connections();


            foreach ($connections->get() as $connection) {
                if ($connection->network_name == "twitter") {
                    try {
                        $client = new \Guzzle\Http\Client('https://api.twitter.com/{version}', array(
                            'version'                   => '1.1'
                        ));

                        // Sign all requests with the OAuthPlugin
                        $client->addSubscriber(new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                            "consumer_key"              => env("TWITTER_CLIENT_ID"),
                            "consumer_secret"           => env("TWITTER_CLIENT_SECRET"),
                            "token"                     => (string)$connection->oauth_token,
                            "token_secret"              => (string)$connection->oauth_token_secret
                        )));

                        $request = $client->post('friendships/create.json', null, array(
                            'screen_name' => 'ssxio'
                        ));

                        $request->send()->json();

                        $request = $client->post('friendships/create.json', null, array(
                            'screen_name' => 'tweeklyfm'
                        ));
                        $request->send()->json();

                        // Do nothing if it was successful
                        $this->info("User ".$connection->external_name." has/is followed ssx");
                    } catch (\Guzzle\Http\Exception\ClientErrorResponseException $exception) {
                        // 99% of the time this will be an invalid token error
                        $this->error("Exception: ".$exception);
                    } catch (\Guzzle\Http\Exception\ServerErrorResponseException $exception) {
                        // Server is having capacity issues, requeue this job
                        $this->error("Exception: ".$exception);
                    } catch (\Guzzle\Http\Exception\BadResponseException $exception) {
                        // This should rarely happen, it means the response back from the server was
                        // invalid, which means a connectivity issue usually
                        $this->error("Exception: ".$exception);
                    } catch (\Exception $exception) {
                        $this->error("Exception: ".$exception);
                    }
                }
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
