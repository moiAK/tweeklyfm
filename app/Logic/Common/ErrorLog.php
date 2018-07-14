<?php namespace App\Logic\Common;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ErrorLog
{

    public static function log($e)
    {
        if (Auth::user()) {
            // Log against user
            Log::error($e, [
                'person' => [
                    'id'        => Auth::user()->id,
                    'name'      => Auth::user()->name,
                    'username'  => Auth::user()->username,
                    'email'     => Auth::user()->email,
                ]
            ]);
        } else {
            // Log anonymously
            Log::error($e);
        }
    }
}
