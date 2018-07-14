<?php namespace App\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Network;

class NetworkInformationSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $twitter = new Network;
        $twitter->name = "twitter";
        $twitter->name_friendly = "Twitter";
        $twitter->save();
        $this->command->info("Added Twitter");

        $facebook = new Network;
        $facebook->name = "facebook";
        $facebook->name_friendly = "Facebook";
        $facebook->save();
        $this->command->info("Added Facebook");

        $lastfm = new Network;
        $lastfm->name = "lastfm";
        $lastfm->name_friendly = "Last.fm";
        $lastfm->save();
        $this->command->info("Added Last.fm");
    }
}
