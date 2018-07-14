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

namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Pusher;

class PusherController extends BaseController
{
    protected $pusher;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');

        $this->pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
    }

    /**
     * Authenticates logged-in user in the Pusher JS app
     * For presence channels.
     */
    public function postAuth()
    {
        $input = Input::all();

        $user_provided = str_replace('private-user-', '', $input['channel_name']);

        //We see if the user is logged in our laravel application.
        if (\Auth::check()) {
            $user = \Auth::user();

            if ($user_provided == $user->id) {
                //Presence Channel information. Usually contains personal user information.
                //See: https://pusher.com/docs/client_api_guide/client_presence_channels
                $presence_data = [
                    'name'  => $user->name,
                    'email' => $user->email,
                ];

                // Registers users' presence channel.
                return Response::make($this->pusher->presence_auth(Input::get('channel_name'), Input::get('socket_id'), $user->id, $presence_data), 200);
            } else {
                return Response::make('Forbidden', 403);
            }
        } else {
            return Response::make('Forbidden', 403);
        }
    }
}
