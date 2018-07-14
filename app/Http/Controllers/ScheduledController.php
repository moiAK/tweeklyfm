<?php namespace App\Http\Controllers;

use App\Logic\Connection\Twitter;
use App\Logic\Source\LastFM;
use App\Logic\Common\CreateTwitterUpdateFromLastFM;
use App\Models\Connection;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use Maknz\Slack\Facades\Slack;

class ScheduledController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getScheduledList()
    {

        $this->data["scheduled"]  = Auth::user()->scheduled()->get();

        return view('scheduled.list', $this->data);
    }


    public function getScheduledDelete($id)
    {
        Auth::user()->scheduled()->findOrFail($id)->delete();

        Flash::success("Scheduled post has been deleted.");

        return Redirect::to("/scheduled");
    }


    public function getScheduledCreate()
    {

        $this->data["connections"]  = Auth::user()->connections;
        $this->data["sources"]      = Auth::user()->sources;

        if ((count($this->data["sources"]) > 0) && (count($this->data["connections"]) > 0)) {
            // Check to see if this user is premium
            if (Auth::user()->isPremium()) {
                if (Auth::user()->canSchedulePost()) {
                    // TODO: Now check to see what plan
                    return view('scheduled.create', $this->data);
                } else {
                    Flash::error("Sorry, your subscription only allows one scheduled post.");

                    return Redirect::to("/scheduled");
                }
            } else {
                Flash::error("Sorry, only subscribed users can set up scheduled posts.");

                return Redirect::to("/scheduled");
            }
        } else {
            return view('publish.not-configured', $this->data);
        }
    }


    public function postScheduledCreate()
    {
        // TODO: check subscription level at this point

        if (Auth::user()->canSchedulePost()) {
            $input = Input::all();

            // Load each model and check we own it, it'll 404 at this point if they don't exist
            $connection                 = Auth::user()->connections()->findOrFail($input["connection"]);
            $source                     = Auth::user()->sources()->findOrFail($input["source"]);

            // Add new scheduled post model
            $scheduled                  = new ScheduledPost();
            $scheduled->user_id         = Auth::user()->id;
            $scheduled->source_id       = $source->id;
            $scheduled->connection_id   = $connection->id;
            $scheduled->status          = "active";
            $scheduled->post_day        = $input["day"];
            $scheduled->post_hour       = $input["time"];
            $scheduled->save();

            Flash::success("Your new scheduled post has been added to the system.");
        } else {
            Flash::error("Sorry, your subscription only allows one scheduled post.");
        }
        return Redirect::to("/scheduled");
    }
}
