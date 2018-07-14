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

use Illuminate\Support\Facades\DB;

class PageController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Welcome Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders the "marketing page" for the application and
    | is configured to only allow guests. Like most of the other sample
    | controllers, you are free to modify or remove it as you desire.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest', ['except' => ['getAbout', 'getTerms']]);
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function getWelcome()
    {
        return view('pages.welcome');
    }

    public function getAbout()
    {
        return view('pages.about');
    }

    public function getPatrons()
    {
        return view('pages.patrons');
    }

    public function getTerms()
    {
        return view('pages.terms');
    }

    public function getStats()
    {
        $rows = DB::select("SELECT COUNT(published_at) as published_total, DATE_FORMAT(published_at, '%Y/%m/%d') 
                                as date_formatted FROM updates  
                                GROUP BY YEAR(published_at), MONTH(published_at), DAY(published_at) ");

        return view('pages.stats')->with('rows', $rows);
    }
}
