<?php namespace App\Http\Controllers\Connection;

use App\Http\Controllers\BaseController;
use App\Models\Connection;
use App\Models\Notification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class Wordpress extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getConnect(Request $request)
    {
        $url = 'https://public-api.wordpress.com/oauth2/authorize?client_id='.env('WORDPRESS_CLIENT_ID').'&redirect_uri='.env('WORDPRESS_CALLBACK').'&response_type=code';
        return Redirect::to($url);
    }

    public function getCallback(Request $request)
    {
        // Get all the input vars
        $input = $request->all();

        // Get the current user
        $user = Auth::user();

        // check for error=access_denied
        if (isset($input["error"])) {
            Flash::error("The request for a token from Wordpress was denied. Please try again.");

            return Redirect::to("/settings/connections");
        }

        try {
            $input = Input::all();

            $curl = curl_init('https://public-api.wordpress.com/oauth2/token');
            curl_setopt($curl, CURLOPT_USERAGENT, 'Tweekly.fm <scott@tweekly.fm>');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                'client_id'         => env('WORDPRESS_CLIENT_ID'),
                'redirect_uri'      => env('WORDPRESS_CALLBACK'),
                'client_secret'     => env('WORDPRESS_CLIENT_SECRET'),
                'code'              => $input['code'],
                'grant_type'        => 'authorization_code'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $auth = curl_exec($curl);
            $auth_info = curl_getinfo($curl);

            if ($auth_info["http_code"] == 200) {
                $auth_decoded = json_decode($auth);

//    +"access_token": "UvCCqA5ihwpGGQR7W9nUEY9W#J6!)gZhyFTrk(TRZ5JUZ&#$NQH$lmdV3$BH)ohR"
//    +"token_type": "bearer"
//    +"blog_id": "95070731"
//    +"blog_url": "http://tweeklyfm.wordpress.com"
//    +"scope": ""

                $connection = Connection::firstOrNew([
                    "user_id" => $user->id,
                    "network_id" => 5,
                    "network_name" => "wordpress",
                    "external_user_id" => $auth_decoded->blog_id
                ]);

                $name_friendly = str_replace(".wordpress.com", "", str_replace("http://", "", $auth_decoded->blog_url));

                $connection->user_id = $user->id;
                $connection->network_id = 5;
                $connection->network_name = "wordpress";
                $connection->oauth_token = $auth_decoded->access_token;
                $connection->oauth_token_secret = "";
                $connection->external_name = $name_friendly;
                $connection->external_user_id = $auth_decoded->blog_id;
                $connection->external_username = $auth_decoded->blog_url;
                $connection->external_avatar = 'https://www.google.com/s2/favicons?domain_url='.urlencode($connection->external_username);
                $connection->checked_at = Carbon::now();
                $connection->expires_at = null;
                $connection->message = "Successfully connected";
                $connection->save();

                $notification = new Notification();
                $notification->user_id = $user->id;
                $notification->message = "Added Wordpress Blog: " . $auth_decoded->blog_url;
                $notification->save();

                Mail::send('emails.connection-added', [
                    'network' => 'Wordpress',
                    'avatar' => "",
                    'name' => $auth_decoded->blog_url
                ], function ($message) use ($user, $auth_decoded) {
                    $message->to($user->email, $user->name)->subject('Wordpress Blog Added: ' . $auth_decoded->blog_url);
                });

                Flash::success("You have successfully added a Wordpress connection for ".$auth_decoded->blog_url);

                return Redirect::to("/settings/connections");
            } else {
                throw new Exception("Failed to get a token from Wordpress API");
            }
        } catch (\Exception $e) {
            Flash::error("The request for a token from Wordpress failed");
            return Redirect::to("/settings/connections");
        }
    }
}
