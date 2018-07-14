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

namespace App\Seeders;

use App\Models\Network;
use Illuminate\Database\Seeder;

class NetworkInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $twitter = new Network();
        $twitter->name = 'twitter';
        $twitter->name_friendly = 'Twitter';
        $twitter->save();
        $this->command->info('Added Twitter');

        $facebook = new Network();
        $facebook->name = 'facebook';
        $facebook->name_friendly = 'Facebook';
        $facebook->save();
        $this->command->info('Added Facebook');

        $lastfm = new Network();
        $lastfm->name = 'lastfm';
        $lastfm->name_friendly = 'Last.fm';
        $lastfm->save();
        $this->command->info('Added Last.fm');
    }
}
