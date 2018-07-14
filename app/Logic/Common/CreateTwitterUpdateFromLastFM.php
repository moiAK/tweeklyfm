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

namespace App\Logic\Common;

use Illuminate\Support\Facades\Auth;

class CreateTwitterUpdateFromLastFM
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
            throw new \Exception('No user provided');
        }

        if (count($this->artists) === 0) {
            throw new \Exception('No data to build an update with.');
        }

        return $this->build();
    }

    public function build()
    {
        $artists_to_display = 5;

        // Split our array down into something we can use
        $this->artists = array_slice($this->artists, 0, $artists_to_display);

        // Initialise vars
        $total = count($this->artists);
        $x = 0;

        // Our initial text
        if ($total == 1) {
            $text = 'My Top #lastfm artist: ';
        } else {
            $text = 'My Top '.$total.' #lastfm artists: ';
        }

        // Iterate our loop
        foreach ($this->artists as $artist) {
            $text .= $artist['title'].' ('.$artist['count'].')';

            // Work out what punctuation to use.
            if ($x < $total - 2) {
                // If before the last two artists
                $text .= ', ';
            } elseif ($x + 2 == $total) {
                // Is the second to last artist
                $text .= ' & ';
            }

            $x++;
        }

        // Fix tweets not going out
        if (strlen($text) > 255) {
            $text = str_replace('Weekly', 'Wkly', $text);
            $text = str_replace('artists ', '', $text);
            $text = str_replace('My Top '.$total, '', $text);
            // $text = str_replace($hashtag,"",$text);
        }

        // If its still above 130 then truncate
        if (strlen($text) > 245) {
            $text = substr($text, 0, 240).'..';
        }

        $text .= ' via @tweeklyfm';

        // Add signature icon
        $text = '♫ '.$text;

        // Set a default hash
        $hashtag = '';

        // If this is a premium, append their hashtag
        if ($this->user->isPremium()) {
            $hashtag = ' #'.$this->user->getMeta('publish.hashtag', 'music');
        }

        // Add the hashtag
        $text .= $hashtag;

        if (strlen($text) > 280) {
            $text = str_replace($hashtag, '', $text);
        }

        // Now set the status text
        $this->status = $text;

        // Return our text
        return $this->status;
    }

    public function __toString()
    {
        return $this->status;
    }
}
