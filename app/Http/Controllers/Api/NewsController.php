<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsController extends Controller
{
    public function getAll()
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getBySlug($slug)
    {
        try {
            $article = Article::where('slug', $slug)
                ->where('type', 'news')
                ->firstOrFail();

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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'News not found.'
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
