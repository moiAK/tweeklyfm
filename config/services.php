<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mandrill' => [
		'secret'            => env("MANDRILL_SECRET"),
	],

    'stripe' => [
        'model'             => '\App\Models\User',
        'secret'            => env("STRIPE_SECRET"),
    ],

    'twitter' => [
        'client_id'         => env("TWITTER_CLIENT_ID"),
        'client_secret'     => env("TWITTER_CLIENT_SECRET"),
        'redirect'          => env("TWITTER_CALLBACK"),
    ],

    'facebook' => [
        'client_id'         => env("FACEBOOK_CLIENT_ID"),
        'client_secret'     => env("FACEBOOK_CLIENT_SECRET"),
        'redirect'          => env("FACEBOOK_CALLBACK"),
    ],

    'instagram' => [
        'client_id'         => env("INSTAGRAM_CLIENT_ID"),
        'client_secret'     => env("INSTAGRAM_CLIENT_SECRET"),
        'redirect'          => env("INSTAGRAM_CALLBACK"),
    ],

    'lastfm' => [
        'key'               => env("LASTFM_KEY"),
        'redirect'          => env("LASTFM_CALLBACK"),
    ],

    'rollbar' => array(
        'access_token'      => env("ROLLBAR_SERVER"),
        'level'             => 'debug'
    ),

    'spotify' => [
        'client_id'         => env('SPOTIFY_CLIENT_ID'),
        'client_secret'     => env('SPOTIFY_CLIENT_SECRET'),
        'redirect'          => env('SPOTIFY_CALLBACK'),
    ],
];
