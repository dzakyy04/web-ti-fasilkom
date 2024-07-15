<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function getAll()
    {
        $articles = Article::where('type', 'news')->latest()->get();

        $articles->transform(function ($article) {
            $article->content = Helper::processContent($article->content);
            $article->thumbnail = Helper::processThumbnail($article->thumbnail);
            return $article;
        });

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Success'
            ],
            'data' => [
                'news' => $articles
            ]
        ]);
    }

    public function getBySlug($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('type', 'news')
            ->firstOrFail();

        $article->content = Helper::processContent($article->content);
        $article->thumbnail = Helper::processThumbnail($article->thumbnail);

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Success'
            ],
            'data' => [
                'news' => $article
            ]
        ]);
    }
}
