<?php namespace App\Http\Controllers;

use App\Models\Scheduled;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Source;
use App\Models\Notification;
use Laracasts\Flash\Flash;
use App\Models\ScheduledPost;

class SourceController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function getRemove($id)
    {
        $user = Auth::user();

        $source = Source::where([
            "id"                => $id,
            "user_id"           => $user->id
        ])->first();

        if (isset($source->id)) {
            // Store some friendly variables
            $network = $source->network_name;
            $username = $source->external_name;

            // Remove any scheduled posts with this source
            ScheduledPost::where("source_id", "=", $id)->delete();

            // Delete this source
            $source->delete();

            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->message = "Removed source account '$username' for network ".ucwords($network);
            $notification->save();

            Flash::success($notification->message);

            // Redirect
            return Redirect::to("/settings/sources");
        } else {
            // Inform the user
            Flash::error("Unable to delete the provided source");

            // Redirect
            return Redirect::to("/settings/sources");
        }
    }
}
