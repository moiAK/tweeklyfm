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

return [

    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /*
     * Storage
     */
    'storage' => 'Session',

    /*
     * Consumers
     */
    'consumers' => [

        'Tumblr' => [
            'client_id'     => env('TUMBLR_CLIENT_ID'),
            'client_secret' => env('TUMBLR_CLIENT_SECRET'),
        ],

    ],

];
