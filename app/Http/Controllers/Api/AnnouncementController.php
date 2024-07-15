<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Article;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnnouncementController extends Controller
{
    use MapsResponse;

    public function getAll()
    {
        try {
            $announcements = Article::where('type', 'announcement')->latest()->get();

            $announcements->transform(function ($announcement) {
                $announcement->content = Helper::processContent($announcement->content);
                $announcement->thumbnail = Helper::processThumbnail($announcement->thumbnail);
                return $announcement;
            });

            $mappedAnnouncements = $this->mapArticles($announcements);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'pengumuman' => $mappedAnnouncements
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
            $announcement = Article::where('slug', $slug)
                ->where('type', 'announcement')
                ->firstOrFail();

            $announcement->thumbnail = Helper::processThumbnail($announcement->thumbnail);
            $mappedAnnouncement = $this->mapArticles(collect([$announcement]));

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'pengumuman' => $mappedAnnouncement[0]
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Pengumuman tidak ditemukan.'
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
