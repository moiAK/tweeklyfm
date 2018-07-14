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

use App\Models\User;

Route::get('/visual/{username}.jpg', 'VisualPostController@getVisualPost');
Route::any('/locale/{language}', 'Language\LanguageController@getSwitchLanguage');
Route::post('/push/auth', 'Common\PusherController@postAuth');

Route::get('/connect/wordpress/go', 'Connection\Wordpress@getConnect');
Route::get('/connect/wordpress/callback', 'Connection\Wordpress@getCallback');

Route::post('/connect/facebook/app/delete', 'Connection\FacebookApp@postDeleteApp');
Route::post('/connect/facebook/app', 'Connection\FacebookApp@postApp');
Route::get('/connect/facebook/app', 'Connection\FacebookApp@getApp');

Route::get('/connect/facebook/go', 'Connection\Facebook@getConnect');
Route::get('/connect/facebook/callback', 'Connection\Facebook@getCallback');
Route::get('/connect/twitter/go', 'Connection\Twitter@getConnect');
Route::get('/connect/twitter/callback', 'Connection\Twitter@getCallback');
Route::get('/connect/spotify/go', 'Connection\Spotify@getConnect');
Route::get('/connect/spotify/callback', 'Connection\Spotify@getCallback');

Route::get('/connect/tumblr/go', 'Connection\Tumblr@getConnect');
Route::get('/connect/tumblr/callback', 'Connection\Tumblr@getConnect');
Route::get('/connect/tumblr/select', 'Connection\Tumblr@getSelectBlog');
Route::post('/connect/tumblr/select', 'Connection\Tumblr@postSelectBlog');

Route::controllers([
    'auth'              => 'Auth\AuthController',
    'password'          => 'Auth\PasswordController',
    'settings'          => 'SettingsController',
    'source'            => 'SourceController',
    'connection'        => 'ConnectController',
]);

// Handle any subdomain views, this is the end user profile view
Route::group(['domain' => '{account}.tweekly.app'], function () {
    Route::get('/', function ($account) {
        // Check that this prefix isn't a www or the likes
        if ($account === 'www') {
            // Return the homepage
            return view('pages.welcome');
        } else {
            // Lookup this users account
            return view('pages.profile', ['user' => User::findByUsername($account)]);
        }
    });
});

/*
Route::get('/billing/paypal',               'Billing\PaypalBillingProviderController@getBillingOverview');
Route::post('/connect/paypal/webhook',      'Billing\PaypalBillingProviderController@postHandleWebhook');

Route::post('/billing/subscribe',           'BillingController@postAddSubscription');
Route::post('/billing/resume',              'BillingController@postResumeSubscription');
Route::get('/billing/unsubscribe',          'BillingController@getRemoveSubscription');
Route::get('/billing/info',                 'BillingController@getSubscription');
Route::get('/billing/invoices/{id}',        'BillingController@getInvoice');
Route::get('/billing',                      'BillingController@getBillingOverview');

Route::post('/connect/stripe/webhook',      'BillingProviderController@handleWebhook');
*/

Route::get('/premium/visual/generate', 'PremiumController@getGenerateVisualPost');
Route::get('/premium/visual', 'PremiumController@getVisualPost');
Route::get('/premium', 'PremiumController@getPremium');

Route::get('/news/{slug}', 'NewsController@getSinglePostBySlug');
Route::get('/news', 'NewsController@getNewsIndex');

Route::get('/scheduled', 'ScheduledController@getScheduledList');
Route::get('/scheduled/create', 'ScheduledController@getScheduledCreate');
Route::post('/scheduled/create', 'ScheduledController@postScheduledCreate');
Route::get('/scheduled/{id}/delete', 'ScheduledController@getScheduledDelete');

Route::get('/publish/start', 'PublishController@getPublishStart');
Route::post('/publish/start', 'PublishController@postPublishStart');
Route::get('/publish/success', 'PublishController@getPublishSuccess');
Route::get('/publish/error', 'PublishController@getPublishError');
Route::get('/publish/nothing-to-publish', 'PublishController@getPublishNothingToPublish');

Route::any('/connect/{network}/go', 'ConnectController@getConnectToNetwork');
Route::any('/connect/{network}/callback', 'ConnectController@getCallbackFromNetwork');
Route::get('/home', 'SettingsController@anyIndex');
Route::get('/terms', 'PageController@getTerms');
Route::get('/about', 'PageController@getAbout');
Route::get('/stats', 'PageController@getStats');
Route::get('/patrons', 'PageController@getPatrons');
Route::get('/', 'PageController@getWelcome');

Route::group(['prefix' => '/profile/', 'namespace' => 'Profile', 'middleware' => 'auth'], function () {
    Route::get('/', [
        'as'    => 'profile.edit',
        'uses'  => 'ProfileController@editProfile',
    ]);

    Route::post('/', [
        'as'    => 'profile.update',
        'uses'  => 'ProfileController@updateProfile',
    ]);

    Route::post('/remove', [
        'as'    => 'profile.remove',
        'uses'  => 'ProfileController@removeProfile',
    ]);
});

//Route::get("/test", function() {
//
//    $user = \App\Models\User::find(1);
//    $connection = $user->connections()->first();
//
//    $upload = \App\Logic\Common\Helper\OAuthHelper::uploadTwitterMedia(
//        $user,
//        env('TWITTER_CLIENT_ID'),
//        env('TWITTER_CLIENT_SECRET'),
//        $connection->oauth_token,
//        $connection->oauth_token_secret,
//        public_path('image/misc/ssx.png')
//    );
//
//    if ($upload != false) {
//
//        \App\Logic\Common\Helper\OAuthHelper::query(
//            $user,
//            env('TWITTER_CLIENT_ID'),
//            env('TWITTER_CLIENT_SECRET'),
//            $connection->oauth_token,
//            $connection->oauth_token_secret,
//            'post',
//            'https://api.twitter.com',
//            '/1.1/statuses/update.json',
//            [
//                'status' => 'Super fancy for @tweeklyfm',
//                'media_ids' => $upload
//            ]
//        );
//    }
//
//    dd($upload);
//
//});

//    $model->getAllMeta();
//    $model->getMeta('some_key', 'optional default value'); // default value only returned if no meta found.
//    $model->updateMeta('some_key', 'New Value');
//    $model->deleteMeta('some_key');
//    $model->deleteAllMeta();
//    $model->addMeta('new_key', ['First Value']);
//    $model->appendMeta('new_key', 'Second Value');
