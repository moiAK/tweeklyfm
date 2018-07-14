<?php namespace App\Http\Controllers\Support\FeatureRequest;

use App\Models\FeatureRequest;
use App\Models\FeatureRequestCategory;
use App\Models\FeatureRequestPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Maknz\Slack\Facades\Slack;
use App\Http\Controllers\BaseController;

class FeatureRequestController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }


    public function getAllFeatureRequests()
    {
        $this->data["user"] = $this->user;

        $this->data["features"] = FeatureRequestCategory::with("features")->get();

        return view('support.feature-request.index', $this->data);
    }


    public function getCreateFeatureRequest()
    {
        $this->data["user"] = $this->user;

        return view('support.feature-request.create', $this->data);
    }


    public function storeCreateFeatureRequest(Request $request)
    {
        dd("add a feature request", $request->all());
    }


    public function getFeatureRequest($feature_id)
    {
        $this->data["user"]     = $this->user;
        $this->data["feature"]  = FeatureRequest::with("user")->findOrFail($feature_id);
        $this->data["vote"]     = FeatureRequestPivot::where("user_id", "=", Auth::user()->id)
            ->where("request_id", "=", $feature_id);

        $this->data["votes"]     = FeatureRequestPivot::where("request_id", "=", $feature_id);

        return view('support.feature-request.view', $this->data);
    }


    public function postVoteForFeatureRequest($feature_id, Request $request)
    {
        $vote = FeatureRequestPivot::where("user_id", "=", Auth::user()->id)
                                    ->where("request_id", "=", $feature_id);

        if ($vote->count() == 0) {
            $vote = new FeatureRequestPivot;
        } else {
            $vote = FeatureRequestPivot::find($vote->first()->id);
        }

        $vote->user_id = Auth::user()->id;
        $vote->request_id = $feature_id;


        $weight = strtolower($request->only("vote")["vote"]);
        if ($weight == "yes") {
            if (Auth::user()->isPremium()) {
                $weight = 3;
            } else {
                $weight = 1;
            }
        } else {
            $weight = -1;
        }

        $vote->weight = $weight;
        $vote->save();

        Flash::success("Your vote has successfully been saved, thank you for participating!");

        return Redirect::back();
    }
}
