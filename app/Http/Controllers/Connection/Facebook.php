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

namespace App\Http\Controllers\Connection;

use App\Http\Controllers\BaseController;
use App\Models\Connection;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class Facebook extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getConnect(LaravelFacebookSdk $facebookSdk)
    {
        $facebook_app = Auth::user()->connection_facebook_app();
        if ($facebook_app->exists() === false) {
            Flash::error('You need to store your Facebook application details to continue.');

            return Redirect::to('/connect/facebook/app');
        }

        $facebookSdk = $facebookSdk->newInstance([
            'app_id'                => $facebook_app->first()->app_id,
            'app_secret'            => $facebook_app->first()->app_secret,
            'default_graph_version' => 'v2.5',
        ]);

        $url = $facebookSdk->getReRequestUrl(['email', 'publish_actions']);

        return Redirect::to($url);
    }

    public function getCallback(LaravelFacebookSdk $facebookSdk, Request $request)
    {
        $facebook_app = Auth::user()->connection_facebook_app();
        if ($facebook_app->exists() === false) {
            Flash::error('You need to store your Facebook application details to continue.');

            return Redirect::to('/connect/facebook/app');
        }

        $facebookSdk = $facebookSdk->newInstance([
            'app_id'                => $facebook_app->first()->app_id,
            'app_secret'            => $facebook_app->first()->app_secret,
            'default_graph_version' => 'v2.5',
        ]);

        // Get all the input vars
        $input = $request->all();

        // Get the current user
        $user = Auth::user();

        // check for error=access_denied
        if (isset($input['error'])) {
            Flash::error('The request for a token from Facebook was denied.');

            return Redirect::to('/settings/connections');
        }

        try {
            $token = $facebookSdk->getAccessTokenFromRedirect();

            $access_token = $token->getValue();
            $expiry = $token->getExpiresAt();

            $facebookSdk->setDefaultAccessToken($access_token);

            $permissions = $facebookSdk->get('/me/permissions');
            $permissions_array = $permissions->getDecodedBody();

            foreach ($permissions_array['data'] as $permission_type) {
                if ($permission_type['status'] != 'granted') {
                    Flash::error('The required permissions were not granted by Facebook, so this connection can not be added.');

                    return Redirect::to('/settings/connections');
                }
            }

            // Fetch the users data
            $user_data = $facebookSdk->get('/me?fields=id,name,email');
            $user_actual_data = $user_data->getDecodedBody();

            // Now add the connection to the database
            $connection = Connection::firstOrNew([
                'user_id'          => $user->id,
                'network_id'       => 2,
                'network_name'     => 'facebook',
                'external_user_id' => $user_actual_data['id'],
            ]);

            $profile_url = 'https://graph.facebook.com/v2.3/'.$user_actual_data['id'].'/picture';

            $connection->user_id = $user->id;
            $connection->network_id = 2;
            $connection->network_name = 'facebook';
            $connection->oauth_token = $token->getValue();
            $connection->oauth_token_secret = '';
            $connection->external_name = $user_actual_data['name'];
            $connection->external_user_id = $user_actual_data['id'];
            $connection->external_username = '';
            $connection->external_avatar = $profile_url;
            $connection->checked_at = Carbon::now();
            $connection->message = 'Successfully connected';
            $connection->expires_at = $expiry;
            $connection->save();

            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->message = 'Added Facebook Connection: '.$user_actual_data['name'];
            $notification->save();

            Mail::send('emails.connection-added', [
                'network' => 'Facebook',
                'avatar'  => $profile_url,
                'name'    => $user_actual_data['name'],
            ], function ($message) use ($user, $user_actual_data) {
                $message->to($user->email, $user->name)->subject('Facebook Connection Added: '.$user_actual_data['name']);
            });

            Flash::success('You have successfully added a Facebook connection.');
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // Failed to obtain access token
            Flash::error('The request for a token from Facebook was denied.');
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            Flash::error('The request for a token from Facebook was denied.');
        }

        return Redirect::to('/settings/connections');
    }
}
