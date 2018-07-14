<?php namespace App\Logic\Common;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CreateWordpressUpdateFromLastFM
{

    protected $status;
    protected $artists;

    public function __construct($artists = [], $user = false)
    {
        $this->artists = $artists;

        if ($user != false) {
            $this->user = $user;
        } else {
            $this->user = Auth::user();
        }

        if (!isset($this->user->id)) {
            throw new \Exception("No user provided.");
        }

        if (count($this->artists) === 0) {
            throw new \Exception("No data to build an update with.");
        }

        return $this->build();
    }

    public function build()
    {
        if ($this->user->isPremium()) {
            $artists_to_display = $this->user->getMeta("publish.max", 5);
        } else {
            $artists_to_display = 3;
        }

        // Split our array down into something we can use
        $this->artists = array_slice($this->artists, 0, $artists_to_display);

        // Initialise vars
        $total = count($this->artists);
        $x = 0;

        // Our initial text
        if ($total == 1) {
            $text = 'My Top <a href="http://last.fm" target="_blank">Last.fm</a> artist: ';
        } else {
            $text = 'My Top ' . $total . ' <a href="http://last.fm" target="_blank">Last.fm</a> artists: ';
        }

        // Iterate our loop
        foreach ($this->artists as $artist) {
            $text .= $artist["title"]." (".$artist["count"].")";

            // Work out what punctuation to use.
            if ($x < $total-2) {
                // If before the last two artists
                $text .= ", ";
            } elseif ($x+2 == $total) {
                // Is the second to last artist
                $text .= " & ";
            }

            $x++;
        }

        // If this is a premium, append their hashtag
        if ($this->user->isPremium()) {
            $text .= " #".$this->user->getMeta("publish.hashtag", "music");
        }

        // Add signature icon
        $this->status = "♫ ".$text."<br><br><small><a href=\"https://tweekly.fm\">Powered by Tweekly.fm</a></small>";

        // Return our text
        return $this->status;
    }

    public function __toString()
    {
        return $this->status;
    }
}
