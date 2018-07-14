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

use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    protected $user;
    public $notifications = 0;
    public $connections = 0;
    public $apps = 0;
    public $data;

    public function __construct()
    {
        $this->middleware('secure');

        $this->data['notifications'] = 0;
        $this->data['connections'] = 0;
        $this->data['apps'] = 0;
        $this->data['premium'] = false;

        if (Auth::check() == true) {
            $this->user = Auth::user();

            // Tag it in for views too
            $this->data['user'] = $this->user;

            if ((Auth::user()->legacy_premium == true) || (Auth::user()->subscribed())) {
                $this->data['premium'] = true;
            }

            $notifications = Auth::user()->notifications();
            if ($notifications->count() > 0) {
                $this->data['notifications'] = $notifications->orderBy('created_at', 'DESC')->limit(10)->get();
            }
        }
    }
}
