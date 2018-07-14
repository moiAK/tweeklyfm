<?php namespace App\Console\Commands;

use App\Logic\Source\LastFM_Loved;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostNewlyLovedTracksCommand extends Command
{

    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tweekly:scheduled:lastfm-loved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This runs through all premium accounts that wish to post their loved tracks.';

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
        $query = DB::select("select * from meta where meta.key = 'flag.lastfm.loved.autopost' and value = 1");

        foreach ($query as $result) {
            $user = User::find($result->metable_id);

            $this->info("Process: ".$user->name);

            $connections    = $user->connections();
            $sources        = $user->sources()->where("network_name", "lastfm");

            if ($sources->exists()) {
                foreach ($sources->get() as $source) {
                    // Update from Last.fm
                    $lovedTracks = new LastFM_Loved($user, $source);

                    // Make the request to pull data
                    $tracks = $lovedTracks->pull();

                    if (count($tracks) > 0) {
                        // For each of the tracks...
                        foreach ($tracks as $track) {
                            // ...publish to each of their networks
                            foreach ($connections->get() as $connection) {
                                if ($connection->network_name == "twitter") {
                                    // Build an update from the artists given back
                                    $update = "#lastfm ❤️: " . $track["name_artist"] . " - " .
                                        $track["name_track"] . ": " . $track["url"];

                                    $job = (new \App\Jobs\Connection\PublishToTwitter(
                                        $user,
                                        $connection,
                                        (string)$update
                                    ))->onQueue('publish.twitter');
                                    $this->dispatch($job);
                                }
                            }
                        }
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
