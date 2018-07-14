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

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;

class PremiumController extends BaseController
{
    use DispatchesJobs;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function getPremium()
    {
        if (Auth::user()->isPremium()) {
            return view('premium.welcome', $this->data);
        } else {
            return view('premium.why', $this->data);
        }
    }

    public function getVisualPost()
    {
        if (Auth::user()->isPremium()) {
            return view('premium.visual', $this->data);
        } else {
            Flash::error('Sorry, the requested page is for subscribed users only.');

            return Redirect::to('/home');
        }
    }

    public function getGenerateVisualPost()
    {
        if (Auth::user()->isPremium()) {
            $job = (new \App\Jobs\Premium\GenerateVisualPost(Auth::user()))->onQueue('premium.visual-posts');

            $this->dispatch($job);

            Flash::info('Your visual post has been queued to regenerate.');

            return Redirect::to('/premium/visual');
        } else {
            Flash::error('Sorry, the requested page is for subscribed users only.');

            return Redirect::to('/home');
        }
    }
}
