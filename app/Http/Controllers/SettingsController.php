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

class SettingsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function anyIndex()
    {
        return view('settings.index', $this->data);
    }

    public function getConnections()
    {
        $this->data['connections'] = Auth::user()->connections;

        return view('settings.connections.list', $this->data);
    }

    public function getApps()
    {
        return view('settings.apps.list', $this->data);
    }

    public function getSources()
    {
        $this->data['sources'] = Auth::user()->sources;

        return view('settings.sources.list', $this->data);
    }

    public function getEmails()
    {
        return view('settings.emails.index', $this->data);
    }
}
