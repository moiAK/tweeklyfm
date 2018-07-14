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

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\BaseController;
use App\Logic\Payment\Paypal\IpnListener;
use App\Models\TransactionPaypal;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

/**
 * Class SettingsController.
 */
class PaypalBillingProviderController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => 'postHandleWebhook']);
    }

    public function getRemoveSubscription()
    {
        // Placeholder for future development
        //        $input = Input::all();
        //
        //        $req = array(
        //            'USER'      => env("PAYPAL_USERNAME"),
        //            'PASSWORD'  => env("PAYPAL_PASSWORD"),
        //            'SIGNATURE' => env("PAYPAL_SIGNATURE"),
        //            'VERSION'   => '76.0',
        //            'METHOD'    => 'ManageRecurringPaymentsProfileStatus',
        //            'PROFILEID' => urlencode($input['paypal_id']),
        //            'ACTION'    => 'Cancel',
        //            'NOTE'      => 'User cancelled on website',
        //        );
        //
        //        $ch = curl_init();
        //
        //        // Swap these if you're testing with the sandbox
        //        // curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
        //        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
        //        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //        curl_setopt($ch, CURLOPT_POST, 1);
        //        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
        //        curl_exec($ch);
        //        curl_close($ch);
        //
        //        return Redirect::to('settings')->with('cancelled', true);
    }

    public function postHandleWebhook()
    {
        $listener = new IpnListener();

        try {
            $data = Input::all();

            $verified = $listener->processIpn($data);

            if ($verified) {
                if (isset($data['custom'])) {
                    $user_id = json_decode($data['custom'])->user_id;
                    $user = User::find($user_id);

                    if (!isset($data['txn_id'])) {
                        $data['txn_id'] = 'None';
                    }

                    // Log the transaction in the database
                    $transaction = new TransactionPaypal();
                    $transaction->transaction_id = $data['txn_id'];
                    $transaction->user_id = $user_id;
                    $transaction->data = json_encode($data);
                    $transaction->save();

                    // Subscription signup
                    if ($data['txn_type'] == 'subscr_signup') {
                    }

                    if ($data['txn_type'] == 'subscr_failed') {
                        // Recurring payment failed, mail user?
                        $user->subscription_active = false;
                        $user->save();
                    }

                    if ($data['txn_type'] == 'subscr_cancel') {
                        // Recurring payment failed, mail user?
                        $user->subscription_active = false;
                        $user->subscription_plan = '';
                        $user->subscription_provided_by = '';
                        $user->subscription_ends_at = Carbon::now();
                        $user->save();
                    }

                    // Subscription payment
                    if ($data['txn_type'] == 'subscr_payment') {
                        if ($data['payment_gross'] == '16.00') {
                            $plan = 'premium';
                            $expiry = Carbon::now()->addYears(1);
                        } elseif ($data['payment_gross'] == '15.00') {
                            $plan = 'premium';
                            $expiry = Carbon::now()->addYears(1);
                        } elseif ($data['payment_gross'] == '1.69') {
                            $plan = 'premium-multiple-monthly';
                            $expiry = Carbon::now()->addMonths(1);
                        } elseif ($data['payment_gross'] == '10.00') {
                            $plan = 'premium-single-year';
                            $expiry = Carbon::now()->addYears(1);
                        } elseif ($data['payment_gross'] == '1.00') {
                            $plan = 'premium-single-monthly';
                            $expiry = Carbon::now()->addMonths(1);
                        } else {
                            abort(500);
                        }

                        // Perform an active depending on how the transaction went
                        if ($data['payment_status'] == 'Completed') {
                            $old_premium = $user->subscription_active;

                            $user->subscription_provided_by = 'paypal';
                            $user->subscription_plan = $plan;
                            $user->subscription_ends_at = $expiry;
                            $user->subscription_active = true;
                            $user->save();

                            // Send premium email, this is the first time they've paid
                            if ($old_premium == false) {
                                Mail::send('emails.premium.welcome', [
                                    'user' => $user,
                                ], function ($message) use ($user) {
                                    $message->to($user->email, $user->name)->subject('Thank You!');
                                });
                            } else {
                                // They were already premium, so send a payment receipt?
                            }
                        }
                    }
                } else {
                    Log::error('No user ID to work with: '.var_export($data, true));
                }
            } else {
                Log::error('Transaction not verified: '.var_export($data, true));
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
