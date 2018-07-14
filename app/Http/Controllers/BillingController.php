<?php namespace App\Http\Controllers;

use App\Logic\Common\ErrorLog;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Laracasts\Flash\Flash;

/**
 * Class SettingsController
 * @package App\Http\Controllers
 */
class BillingController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function getInvoice($id)
    {
        return $this->user->downloadInvoice($id, [
            'vendor'  => 'Tweekly.fm',
            'product' => 'premium',
        ]);
    }

    public function postAddSubscription()
    {
        $x = Input::all();

        try {
            $subscription = $this->user->subscription($x["subscription_type"]);

            if ($x["coupon"] != "") {
                $subscription->withCoupon($x["coupon"]);
            }

            $subscription->create($x["stripeToken"], [
                'email' => $this->user->email
            ]);

            $this->data["premium"] = true;

            $user = $this->user;

            // Update the users subscription information
            $user->subscription_provided_by = 'stripe';
            $user->subscription_plan = $x["subscription_type"];
            $user->subscription_active = true;
            $user->save();

            Mail::send('emails.premium.welcome', [
                'user'   => $user
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject('Thank You!');
            });

            Flash::success("You have successfully added your subscription, thank you!");

            return Redirect::to("/billing");
        } catch (Exception $e) {
            ErrorLog::log($e);

            Flash::error("There was an error processing your subscription. Please try again. If you used a coupon it may have expired or been used a maximum amount of times.");

            return Redirect::to("/billing");
        }
    }

    public function postResumeSubscription()
    {
        $x = Input::all();

        try {
            $subscription = $this->user->subscription($x["subscription_type"]);
            $subscription->resume($x["stripeToken"], [
                'email' => $this->user->email
            ]);

            $this->data["premium"] = true;

            Flash::success("Successfully resumed your subscription");

            return Redirect::to("/billing");
        } catch (Exception $e) {
            Flash::error("There was an error processing your subscription. Please try again");

            ErrorLog::log($e);

            return Redirect::to("/billing");
        }
    }

    public function getRemoveSubscription()
    {
        try {
            $this->user->subscription()->cancel();
            return Redirect::to("/billing");
        } catch (Exception $e) {
            ErrorLog::log($e);

            Flash::error("There was an error processing your subscription. Please try again");

            return Redirect::to("/billing");
        }
    }

    public function getBillingOverview()
    {
        $this->data["user"] = $this->user;
        if ($this->user->subscription_provided_by == "legacy") {
            return view('billing.legacy.subscribed', $this->data);
        } elseif ($this->user->subscription_provided_by == "paypal") {
            return view('billing.paypal.subscribed', $this->data);
        } else {
            if ($this->user->subscribed()) {
                $this->data["invoices"] = [];
                $this->data["expiry_date"] = $this->user["subscription_ends_at"];

                return view('billing.stripe.subscribed', $this->data);
            } else {
                return view('billing.stripe.free', $this->data);
            }
        }
    }
}
