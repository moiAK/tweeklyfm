<?php namespace App\Http\Controllers\Connection;

use App\Http\Controllers\BaseController;
use App\Models\Connection;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use App\Models\ConnectionFacebookApp;

class FacebookApp extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }


    public function getApp()
    {
        // Default view data
        $app = [
            "app_id" => "",
            "app_secret" => ""
        ];

        // See if we've set up our app here
        $facebookApp = Auth::user()->connection_facebook_app();
        if ($facebookApp->exists()) {
            $app["app_id"]      = $facebookApp->first()->app_id;
            $app["app_secret"]  = $facebookApp->first()->app_secret;
        }

        return view("settings.connections.facebookapp", $app);
    }
    
    
    public function postApp(Request $request)
    {
        // Check the validator
        $validator = Validator::make($request->all(), [
            'app_id'        => 'required',
            'app_secret'    => 'required'
        ]);

        // If they didn't provide the details, redirect back
        if ($validator->fails()) {
            Flash::error("Please provide an application ID and application secret.");
            return Redirect::back();
        }

        // Store the app details
        $facebook_app               = ConnectionFacebookApp::firstOrNew([ 'user_id' => Auth::user()->id ]);
        $facebook_app->app_id       = $request->get("app_id");
        $facebook_app->app_secret   = $request->get("app_secret");
        $facebook_app->user_id      = Auth::user()->id;
        $facebook_app->save();

        Flash::success("Your Facebook application details have been saved. You can now try to add a connection.");

        return Redirect::back();
    }


    public function postDeleteApp()
    {
        $facebook_app = ConnectionFacebookApp::firstOrNew([ 'user_id' => Auth::user()->id ]);
        $facebook_app->delete();

        Flash::success("Your Facebook application details have been removed.");

        return Redirect::back();
    }
}
