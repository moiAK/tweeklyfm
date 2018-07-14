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

namespace App\Http\Controllers\Language;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends BaseController
{
    public function getSwitchLanguage($language)
    {
        if (($language == 'en') || ($language == 'pt') || ($language == 'de') || ($language == 'es')) {
            Session::put('user.locale', $language);
            App::setLocale($language);
        }

        return Redirect::back();
    }
}
