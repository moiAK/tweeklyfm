<?php namespace App\Logic\Common\Helper;

use App\Models\Notification;
use Exception;

class OAuthHelper
{

    // Store an error message for a user
    public static function addError($user, $message)
    {
        $notification               = new Notification();
        $notification->user_id      = $user->id;
        $notification->message      = "API Error: ".$message;
        $notification->save();
    }


    public static function uploadTwitterMedia($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $file, $attempt = 0)
    {

        if (!file_exists($file)) {
            throw new Exception("File does not exist");
        }

        try {
            $client = new \Guzzle\Http\Client("https://upload.twitter.com");
            $client->addSubscriber(new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                "consumer_key"      => $consumer_key,
                "consumer_secret"   => $consumer_secret,
                "token"             => $access_token,
                "token_secret"      => $access_token_secret
            )));

            $request = $client->post("/1.1/media/upload.json")
                ->setPostField('media_data', base64_encode(file_get_contents($file)));

            $body = $request->send()->json();

            return $body["media_id"];
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::uploadTwitterMedia($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $attempt);
            }
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::uploadTwitterMedia($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $attempt);
            }
        } catch (\Guzzle\Http\Exception\BadResponseException $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::uploadTwitterMedia($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $attempt);
            }
        } catch (\Exception $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::uploadTwitterMedia($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $attempt);
            }
        }
    }

    public static function query($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $method, $base_url, $base_path, $params = [], $attempt = 0, $media = [])
    {

        try {
            $client = new \Guzzle\Http\Client($base_url);

            // Sign all requests with the OAuthPlugin
            $client->addSubscriber(new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                "consumer_key"              => $consumer_key,
                "consumer_secret"           => $consumer_secret,
                "token"                     => $access_token,
                "token_secret"              => $access_token_secret
            )));

            $request    = $client->$method($base_path, null, $params);
            $response   = $request->send()->json();

            return $response;
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::query($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $method, $base_url, $base_path, $params, $attempt);
            }
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::query($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $method, $base_url, $base_path, $params, $attempt);
            }
        } catch (\Guzzle\Http\Exception\BadResponseException $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::query($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $method, $base_url, $base_path, $params, $attempt);
            }
        } catch (\Exception $exception) {
            $attempt++;
            if ($attempt > 3) {
                self::addError($user, $exception->getMessage());
                return false;
            } else {
                return self::query($user, $consumer_key, $consumer_secret, $access_token, $access_token_secret, $method, $base_url, $base_path, $params, $attempt);
            }
        }
    }
}
