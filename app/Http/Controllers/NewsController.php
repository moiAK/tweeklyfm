<?php namespace App\Http\Controllers;

use App\Logic\Connection\Twitter;
use App\Logic\Source\LastFM;
use App\Logic\Common\CreateTwitterUpdateFromLastFM;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Maknz\Slack\Facades\Slack;
use App\Models\News;
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

        foreach (News::orderBy("created_at", "DESC")->get() as $post) {
            $converter = new CommonMarkConverter();

            $this->data["news"][] = [
                "date"      => $post->created_at,
                "title"     => $post->title,
                "slug"      => $post->slug,
                "content"   => $converter->convertToHtml($post->content)
            ];
        }

        return view('news.index', $this->data);
    }


    public function getSinglePostBySlug($slug)
    {

        foreach (News::where("slug", "=", $slug)->get() as $post) {
            $converter = new CommonMarkConverter();

            $this->data["news"][] = [
                "date"      => $post->created_at,
                "title"     => $post->title,
                "slug"      => $post->slug,
                "content"   => $converter->convertToHtml($post->content)
            ];
        }

        return view('news.post', $this->data);
    }
}
