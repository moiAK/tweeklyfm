<?php namespace App\Http\Controllers;

use App\Logic\Source\LastFM;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

class VisualPostController extends BaseController
{

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function getVisualPost($username)
    {

        $user = User::where(["username" => $username])->firstOrFail();

        $image = Cache::remember('visualpost2.'.$username, 1, function () use ($user) {
            // A few variables that we'll need
            $max_width      = 800;
            $max_height     = 800;
            $row            = 0;
            $current_height = 0;
            $current_width  = 0;
            $image_width    = 200;
            $image_height   = 200;
            $current        = 0;
            $fontPath       = base_path('resources/fonts/yanone.ttf');

            $img = Image::canvas($max_width, $max_height, '#ffffff');

            // Test to see that we have a source configured
            if ($user->sources()->count() == 0) {
                $img->text("Please configure a Last.fm Source.", 250, 400, function ($font) use ($fontPath) {
                    $font->file($fontPath);
                    $font->color(array(0, 0, 0, 1));
                    $font->size(30);
                });
            } else {
                $source = $user->sources()->first();

                // Get a last.fm instance
                $lastfm = new LastFM($user, $source);
                $artists = $lastfm->pull();
                $data = $artists->getItems();

                if (count($data) == 0) {
                    return file_get_contents(public_path("image/visual-post-error.png"));
                } else {
                    // Split into the number of results we want
                    $data = array_slice($data, 0, 16);

                    foreach ($data as $element) {
                        $current++;

                        if ($current_width >= $max_width) {
                            $row++;
                            $current_width = 0;
                            $current_height += $image_height;
                        }

                        // Try to load a remote image
                        try {
                            // We've loaded the remote image
                            $img2 = Image::make($element["image"]);
                        } catch (\Intervention\Image\Exception\NotReadableException $e) {
                            // Remote image load failed, so build a square
                            $img2 = Image::canvas(200, 200, '#ffffff');
                        }

                        $img2->fit(200, 200, function ($constraint) {
                            $constraint->upsize();
                        });

                        // Position
                        $img2->rectangle(0, 0, 200, 25, function ($draw) {
                            $draw->background('#000000');
                        });
                        $img2->text("#" . $current, 6, 20, function ($font) use ($fontPath) {
                            $font->file($fontPath);
                            $font->color(array(255, 255, 255, 1));
                            $font->size(18);
                        });

                        // Add this new image to the main image
                        $img->insert($img2, 'top-left', $current_width, $current_height);

                        // Update current width
                        $current_width += $image_width;
                    }
                }

                $watermark = Image::make(public_path("image/logo.png"));

                $watermark->fit(60, 60, function ($constraint) {
                    $constraint->upsize();
                });

                // Add tweekly.fm watermark
                $img->insert($watermark, "bottom-right", 10, 10);

                return $img->response('png');
            }

            return file_get_contents(public_path("image/visual-post-error.png"));
        });

        return Response::make($image, 200, ['Content-Type' => 'image/jpeg']);
    }
}
