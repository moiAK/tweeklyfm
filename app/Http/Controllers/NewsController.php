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

use App\Models\News;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\CommonMarkConverter;

class NewsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function getNewsIndex()
    {
        foreach (News::orderBy('created_at', 'DESC')->get() as $post) {
            $converter = new CommonMarkConverter();

            $this->data['news'][] = [
                'date'      => $post->created_at,
                'title'     => $post->title,
                'slug'      => $post->slug,
                'content'   => $converter->convertToHtml($post->content),
            ];
        }

        return view('news.index', $this->data);
    }

    public function getSinglePostBySlug($slug)
    {
        foreach (News::where('slug', '=', $slug)->get() as $post) {
            $converter = new CommonMarkConverter();

            $this->data['news'][] = [
                'date'      => $post->created_at,
                'title'     => $post->title,
                'slug'      => $post->slug,
                'content'   => $converter->convertToHtml($post->content),
            ];
        }

        return view('news.post', $this->data);
    }
}
