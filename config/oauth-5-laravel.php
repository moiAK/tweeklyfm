<?php

return [

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session',

	/**
	 * Consumers
	 */
	'consumers' => [

		'Tumblr' => [
			'client_id'     => env('TUMBLR_CLIENT_ID'),
			'client_secret' => env('TUMBLR_CLIENT_SECRET')
		],

	]

];