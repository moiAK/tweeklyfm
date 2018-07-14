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

namespace App\Logic\Source;

use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LastFM_Loved
{
    protected $user;
    protected $source;
    protected $data;

    private $apiUrl = '';
    private $accessKey = '';
    private $secretKey = '';

    public function __construct(User $user, Source $source)
    {
        $this->user = $user;
        $this->source = $source;
        $this->apiUrl = env('LASTFM_API_URL');
        $this->accessKey = env('LASTFM_KEY');
        $this->secretKey = env('LASTFM_SECRET');
        $this->data = [];
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function pull()
    {
        $user = $this->user;

        if ($user['lastfm_token']) {
            // Build API signature
            $signature = md5('api_key'.$this->accessKey.'user.getLovedTracksuser'.$this->source->external_username.$this->source->oauth_token.$this->secretKey);

            // Build URL to query
            $url = $this->apiUrl.'user.getLovedTracks&user='.$this->source->external_username.'&api_key='.$this->accessKey.'&format=json&api_sig='.$signature;
        } else {
            // Build URL to query
            $url = $this->apiUrl.'user.getLovedTracks&user='.$this->source->external_username.'&api_key='.$this->accessKey.'&format=json';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Tweekly.fm OSS - https://github.com/tweeklyfm/tweeklyfm');
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($returnedUser = json_decode($response, true)) {
            if (isset($returnedUser['lovedtracks']) && isset($returnedUser['lovedtracks']['track'])) {
                $tracks = $returnedUser['lovedtracks']['track'];
                $key = $user->id.'.lastfm.loved.timestamp';

                if (!Cache::has($key)) {
                    // Set the key to being the current timestamp
                    Cache::forever($key, time());
                }

                // Get this into a variable so we're don't have to requery cache
                $sinceTimestamp = Cache::get($key);

                foreach ($tracks as $track) {
                    if ($track['date']['uts'] > $sinceTimestamp) {
                        $this->data[] = [
                            'name_artist'    => $track['artist']['name'],
                            'name_track'     => $track['name'],
                            'url'            => $track['url'],
                            'image'          => $track['image'][0]['#text'],
                        ];
                    }
                }

                // Now mark the last time we ran for this user
                Cache::forever($key, time());
            }

            $this->source->message = 'Updated successfully (loved tracks) from Last.fm API (JSON)';
        } else {
            $this->source->message = 'Error when requesting user data from Last.fm API (JSON)';
            Log::error('Last.fm error when fetching data for: '.$this->user->id." \n\n".var_export($response, true));
        }

        // Update the source
        $this->source->save();

        // Return our items
        return $this->data;
    }
}
