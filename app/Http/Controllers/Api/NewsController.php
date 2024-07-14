<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function getAll()
    {
        $articles = Article::where('type', 'news')->latest()->get();

        $articles->transform(function ($article) {
            $article->content = $this->processContent($article->content);
            $article->thumbnail = $this->processThumbnail($article->thumbnail);
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

        $article->content = $this->processContent($article->content);
        $article->thumbnail = $this->processThumbnail($article->thumbnail);

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

    private function processThumbnail($thumbnail)
    {
        // Ambil APP_URL dari .env
        $appUrl = env('APP_URL');

        // Ubah URL thumbnail jika ada
        if (isset($thumbnail)) {
            $thumbnail = str_replace('public/', 'storage/', $thumbnail);
            $thumbnail = $appUrl . '/' . $thumbnail;
        }

        return $thumbnail;
    }

    private function processContent($content)
    {
        $contentWithoutHtml = preg_replace('/\.\s*/', '. ', str_replace(["\n", "\r"], " ", strip_tags($content)));

        if (strlen($contentWithoutHtml) > 250) {
            $truncatedContent = substr($contentWithoutHtml, 0, 250);

            $lastSpace = strrpos($truncatedContent, ' ');
            if ($lastSpace !== false) {
                $truncatedContent = substr($truncatedContent, 0, $lastSpace);
            }

            $contentWithoutHtml = $truncatedContent . '...';
        }

        return $contentWithoutHtml;
    }
}
