<?php namespace App\Logic\Source;

use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\SharedDataObject;

class LastFM
{

    protected $user, $source, $data;

    private $apiUrl = "";
    private $accessKey = "";
    private $secretKey = "";

    public function __construct(User $user, Source $source)
    {
        $this->user                 = $user;
        $this->source               = $source;
        $this->apiUrl               = env("LASTFM_API_URL");
        $this->accessKey            = env("LASTFM_KEY");
        $this->secretKey            = env("LASTFM_SECRET");
        $this->data                 = new SharedDataObject;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function pull()
    {
        $user = $this->user;

        if ($user["lastfm_token"]) {
            $signature = md5("api_key".$this->accessKey."user.getTopArtistsperiod7daysk".$this->source->oauth_token."user".$this->source->external_username.$this->secretKey);

        // Build URL to query
            $url = $this->apiUrl."user.getTopArtists&period=7day&user=".$this->source->external_username."&sk=".$this->source->oauth_token."&api_key=".$this->accessKey."&format=json&api_sig=".$signature;
        } else {
            // Build URL to query
            $url = $this->apiUrl."user.getTopArtists&period=7day&user=".$this->source->external_username."&api_key=".$this->accessKey."&format=json";
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
            // If a user only has one artist, we can't interate the list
            if (isset($returnedUser["topartists"]["artist"]["name"])) {
                $single_artist = $returnedUser["topartists"]["artist"];
                $returnedUser["topartists"]["artist"] = [];
                $returnedUser["topartists"]["artist"][] = $single_artist;
            }

            if (isset($returnedUser["topartists"]["artist"])) {
                $intPosition        = 0;

                foreach ($returnedUser["topartists"]["artist"] as $artist) {
                    $intPosition++;

                    $this->data->addItem([
                        "id"        => $artist["mbid"],
                        "title"     => $artist["name"],
                        "count"     => $artist["playcount"],
                        "image"     => $artist["image"][4]["#text"],
                        "position"  => $intPosition
                    ]);
                }

                $this->source->message = "Updated from Last.fm API (JSON)";
            } else {
                $this->source->message = "No top artists from Last.fm API (JSON)";
            }
        } else {
            $this->source->message = "Error when requesting user data from Last.fm API (JSON)";

            Log::error("Last.fm error when fetching data for: ".$this->user->id. " \n\n".var_export($response, true));
        }

        // Update the source
        $this->source->save();

        // Return our items
        return $this->data;
    }
}
