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

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\Notification;
use App\Models\ScheduledPost;
use App\Models\Source;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite as Socialize;

class ConnectController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function getConnectToNetwork($network)
    {
        switch ($network) {
            case 'lastfm':
                return Redirect::to('http://www.last.fm/api/auth/?api_key='.env('LASTFM_KEY'));
                break;

            default:
                return view('errors.500');
                break;
        }
    }

    public function getCallbackFromNetwork($network)
    {
        // Get the current user
        $user = Auth::user();

        switch ($network) {
            case 'lastfm':
                $token = Input::get('token');
                $signature = md5('api_key'.env('LASTFM_KEY').'methodauth.getSessiontoken'.$token.env('LASTFM_SECRET'));

                try {
                    $reply = @file_get_contents(env('LASTFM_API_URL').'auth.getSession&token='.$token.'&api_key='.env('LASTFM_KEY').'&api_sig='.$signature.'&format=json');
                    if ($reply != false) {
                        if ($json = json_decode($reply)) {
                            if (!isset($json->session)) {
                                Flash::error('Last.fm provided an invalid response to authorising your account. Please try again.');

                                return Redirect::to('/settings/sources');
                            }

                            $username = $json->session->name;
                            $key = $json->session->key;

                            $source = Source::firstOrNew([
                                'user_id'          => $user->id,
                                'network_id'       => 3,
                                'network_name'     => 'lastfm',
                                'external_user_id' => $username,
                            ]);

                            $source->user_id = $user->id;
                            $source->network_id = 3;
                            $source->network_name = 'lastfm';
                            $source->oauth_token = $key;
                            $source->external_name = $username;
                            $source->external_username = $username;
                            $source->checked_at = Carbon::now();
                            $source->message = 'Successfully added';
                            $source->save();

                            $notification = new Notification();
                            $notification->user_id = $user->id;
                            $notification->message = 'Added Last.fm Source: '.$username;
                            $notification->save();

                            Mail::send('emails.source-added', [
                                'network' => 'Last.fm',
                                'name'    => $username,
                            ], function ($message) use ($user, $username) {
                                $message->to($user->email, $user->name)->subject('Last.fm Source Added: '.$username);
                            });

                            return Redirect::to('/settings/sources');
                        }
                    } else {
                        $notification = new Notification();
                        $notification->user_id = $user->id;
                        $notification->message = 'Last.fm failed to provide a session token';
                        $notification->save();

                        Flash::error($notification->message);

                        return Redirect::to('/settings/sources');
                    }
                } catch (Exception $e) {
                    Log::error($e);

                    return view('errors.500');
                }

                break;

            case 'twitter':
                break;

            case 'instagram':
                $instagram = Socialize::with('instagram')->user();

                $connection = Connection::firstOrNew([
                    'user_id'           => $user->id,
                    'network_id'        => 4,
                    'network_name'      => 'instagram',
                    'external_user_id'  => $instagram->getId(),
                ]);

                $connection->user_id = $user->id;
                $connection->network_id = 4;
                $connection->network_name = 'instagram';
                $connection->oauth_token = $instagram->token;
                $connection->oauth_token_secret = '';
                $connection->external_name = $instagram->getName();
                $connection->external_user_id = $instagram->getId();
                $connection->external_username = $instagram->getNickname();
                $connection->external_avatar = $instagram->getAvatar();
                $connection->checked_at = Carbon::now();
                $connection->message = 'Successfully connected';
                $connection->save();

                $notification = new Notification();
                $notification->user_id = $user->id;
                $notification->message = 'Added Instagram Connection: '.$instagram->getName();
                $notification->save();

                Mail::send('emails.connection-added', [
                    'network'   => 'Instagram',
                    'avatar'    => $instagram->getAvatar(),
                    'name'      => $instagram->getName(),
                ], function ($message) use ($user, $instagram) {
                    $message->to($user->email, $user->name)->subject('Instagram Connection Added: '.$instagram->getName());
                });

                return Redirect::to('/settings/connections');
                break;

            default:
                return view('errors.500');
                break;
        }
    }

    public function getRemove($id)
    {
        $user = Auth::user();

        $connection = Connection::where([
            'id'                => $id,
            'user_id'           => $user->id,
        ])->first();

        if (isset($connection->id) && ($connection->id == $id)) {
            // Store some friendly variables
            $network = $connection->network_name;
            $username = $connection->external_name;

            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->message = "Removed connection '$username' for network ".ucwords($network);
            $notification->save();

            // Remove any scheduled posts with this connection
            ScheduledPost::where('connection_id', '=', $id)->delete();

            // Delete this source
            $connection->delete();

            Flash::success($notification->message);

            // Redirect
            return Redirect::to('/settings/connections');
        } else {
            Flash::error('Unable to delete the provided connection.');

            // Redirect
            return Redirect::to('/settings/connections');
        }
    }
}
