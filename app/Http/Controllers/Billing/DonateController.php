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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

/**
 * Class SettingsController.
 */
class DonateController extends BaseController
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

    public function getDonate()
    {
        return view('billing.donate.form', $this->data);
    }

    public function postDonate()
    {
        $input = Input::all();

        $amount = (int) $input['amount'];

        if ($this->user->isPremium()) {
            if ($this->user->legacy_premium == false) {
                if ($this->user->charge($amount)) {
                    Flash::success('Thank you, your donation was processed successfully');
                } else {
                    Flash::error('There was an error processing your donation. Please try again.');
                }
            } else {
                Flash::error('You are subscribed via a legacy plan which donations cannot be taken through.');
            }
        } else {
            Flash::error('You are not currently subscribed to the service.');
        }

        return Redirect::to(route('donate.form'));
    }
}
