<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Article;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsController extends Controller
{
    use MapsResponse;

    public function getAll()
    {
        try {
            $news = Article::where('type', 'news')->latest()->get();

            $news->transform(function ($newsItem) {
                $newsItem->content = Helper::processContent($newsItem->content);
                $newsItem->thumbnail = Helper::convertImageUrl($newsItem->thumbnail);
                return $newsItem;
            });

            $mappedNews = $this->mapArticles($news);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'berita' => $mappedNews
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
            $newsItem = Article::where('slug', $slug)
                ->where('type', 'news')
                ->firstOrFail();

            $newsItem->thumbnail = Helper::convertImageUrl($newsItem->thumbnail);
            $mappedNews = $this->mapArticles(collect([$newsItem]));

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'berita' => $mappedNews
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Berita tidak ditemukan.'
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
