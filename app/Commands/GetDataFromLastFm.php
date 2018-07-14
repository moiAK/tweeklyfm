<?php namespace App\Commands;

use App\Commands\Command;

use App\Models\Source;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class GetDataFromLastFm extends Command implements SelfHandling, ShouldBeQueued
{

    use InteractsWithQueue, SerializesModels;

    protected $user, $source;
    private $apiUrl         = "";
    private $accessKey      = "";
    private $secretKey      = "";
    private $maxResults     = 10;
    private $topArtists     = [];
    private $defaultArtists = [
        'lastfm_band_0' => '',
        'lastfm_band_1' => '',
        'lastfm_band_2' => '',
        'lastfm_band_3' => '',
        'lastfm_band_4' => '',
        'lastfm_band_5' => '',
        'lastfm_band_6' => '',
        'lastfm_band_7' => '',
        'lastfm_band_8' => '',
        'lastfm_band_9' => '',
        'lastfm_count_0' => '',
        'lastfm_count_1' => '',
        'lastfm_count_2' => '',
        'lastfm_count_3' => '',
        'lastfm_count_4' => '',
        'lastfm_count_5' => '',
        'lastfm_count_6' => '',
        'lastfm_count_7' => '',
        'lastfm_count_8' => '',
        'lastfm_count_9' => ''
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, Source $source)
    {
        $this->user         = $user;
        $this->source       = $source;
        $this->apiUrl       = env("LASTFM_API_URL");
        $this->accessKey    = env("LASTFM_KEY");
        $this->secretKey    = env("LASTFM_SECRET");
    }


    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;

        if ($user["lastfm_token"]) {
            // Build API signature
            $signature = md5("api_key".$this->accessKey."user.getTopArtistsperiod7dayuser".$user["lastfm_user"].$user["lastfm_token"].$this->secretKey);

            // Build URL to query
            $url = $this->apiUrl."user.getTopArtists&period=7day&user=".$user["lastfm_user"]."&api_key=".$this->accessKey."&format=json&api_sig=".$signature;
        } else {
            // Build URL to query
            $url = $this->apiUrl."user.getTopArtists&period=7day&user=".$user["lastfm_user"]."&api_key=".$this->accessKey."&format=json";
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Tweekly.fm OSS - https://github.com/tweeklyfm/tweeklyfm");
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($returnedUser = json_decode($response, true)) {
            if (isset($returnedUser["topartists"]["artist"])) {
                $intIterate         = 0;
                $intArtistFound     = 0;
                $intArtistNotFound  = 0;

                while ($intIterate < $this->maxResults) {
                    if (isset($returnedUser["topartists"]["artist"][$intIterate])) {
                        $this->topArtists["lastfm_band_".$intIterate]    = $returnedUser["topartists"]["artist"][$intIterate]["name"];
                        $this->topArtists["lastfm_count_".$intIterate]   = $returnedUser["topartists"]["artist"][$intIterate]["playcount"];
                        $intArtistFound++;
                    } else {
                        $this->topArtists["lastfm_band_".$intIterate]    = "";
                        $this->topArtists["lastfm_count_".$intIterate]   = "";
                        $intArtistNotFound++;
                    }

                    $intIterate++;
                }


                $arrayUpdatedData = array_merge($this->topArtists, array(
                    "message"           => "Updated from Last.fm API (JSON) ".date("r"),
                    "flag_publish"      => true
                ));
            } else {
                $arrayUpdatedData = array_merge($this->defaultArtists, array(
                    "message"           => "No top artists from Last.fm API (JSON) ".date("r"),
                    "flag_publish"      => false
                ));

                // No top artists, requeue?
                \Log::info("No top artists: ".$user["lastfm_user"]);

                /*
                \Mail::send('emails.update-fail', ["username" => $user["lastfm_user"]], function($message) use ($user)
                {
                    $message->to($user["email"], $user["name"])->subject("No Scrobbles Found this Week for '".$user["lastfm_user"]."'");
                });
                */
            }
        } else {
            $arrayUpdatedData = array_merge($this->defaultArtists, array(
                "message"           => "Error when requesting user data from Last.fm API (JSON) ".date("r"),
                "flag_publish"      => false
            ));

            \Log::error("Error fetching data for: ".$user["lastfm_user"]);

            /*
            // No top artists, requeue?
            \Mail::send('emails.update-fail', ["username" => $user["lastfm_user"]], function($message) use ($user)
            {
                $message->to($user["email"], $user["name"])->subject("No Scrobbles Found this Week for '".$user["lastfm_user"]."'");
            });
            */
        }

        if (!isset($user["total_pull"])) {
            $user["total_pull"] = 0;
        }

        // Update the user pull count
        $arrayUpdatedData["total_pull"]         = $user["total_pull"]+1;

        // Update the timestamp for pulled
        $arrayUpdatedData["timestamp_pulled"]   = time();

        // At this point, we can run an update on this user
        $updatedObject = \DB::collection('users')->where('_id', $user["_id"]{'$id'})->update($arrayUpdatedData);
    }
}
