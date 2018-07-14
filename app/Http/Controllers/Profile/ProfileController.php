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

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\BaseController;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class ProfileController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    public function editProfile()
    {
        $this->data['user'] = $this->user;
        $this->data['timezones'] = DateTimeZone::listIdentifiers();

        return view('profile.edit', $this->data);
    }

    public function updateProfile()
    {
        $input = Input::all();

        $validator = Validator::make($input, [
            'flag_allow_promote'        => 'integer',
            'flag_allow_auto_follow'    => 'integer',
            'publish_max'               => 'integer|max:5',
            'publish_hashtag'           => 'max:255',
            'publish_loved'             => 'integer',
            'timezone'                  => 'min:1|max:100',
        ]);

        if ($validator->fails()) {
            Flash::error('There was an error updating your metadata, check each setting and try again');

            return redirect(route('profile.edit'));
        }

        // Remove old meta options
        $this->user->deleteMeta('flag.allow.promote');
        $this->user->deleteMeta('flag.allow.auto-follow');
        $this->user->deleteMeta('flag.lastfm.loved.autopost');
        $this->user->deleteMeta('publish.max');
        $this->user->deleteMeta('publish.hashtag');
        $this->user->deleteMeta('publish.hashtag');

        // Set the metadata
        if (isset($input['flag_allow_promote'])) {
            $this->user->updateMeta('flag.allow.promote', (bool) $input['flag_allow_promote']);
        }

        if (isset($input['flag_allow_auto_follow'])) {
            $this->user->updateMeta('flag.allow.auto-follow', (bool) $input['flag_allow_auto_follow']);
        }

        // If we're a premium user there will be a few things more to save
        if (isset($input['publish_loved'])) {
            $this->user->updateMeta('flag.lastfm.loved.autopost', (int) $input['publish_loved']);
        }

        if (isset($input['publish_max'])) {
            $this->user->updateMeta('publish.max', (int) $input['publish_max']);
        }

        if (isset($input['publish_hashtag'])) {
            $this->user->updateMeta('publish.hashtag', $input['publish_hashtag']);
        }

        if (isset($input['timezone'])) {
            $this->user->timezone = $input['timezone'];
        }

        // Save changes to the user
        $this->user->save();

        Flash::success('Your profile settings have been updated.');

        return Redirect::to(route('profile.edit'));
    }

    public function removeProfile()
    {
        $user = Auth::user();

        // Remove connections and sources
        $user->connections()->delete();
        $user->sources()->delete();

        // Update the user record
        $user->status = 'deleted';
        $user->email = 'AUTO_REMOVED_'.$user->email;
        $user->save();

        Auth::logout();

        return redirect('/');
    }
}
