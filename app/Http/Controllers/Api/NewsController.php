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

    /**
     * @OA\Get(
     *   path="/berita",
     *   tags={"Berita"},
     *   operationId="getAllBerita",
     *   summary="Dapatkan Semua Berita",
     *   description="Mengambil semua berita",
     *   @OA\Response(
     *     response=200,
     *     description="Berhasil",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="status",
     *         type="object",
     *         @OA\Property(property="code", type="integer", example=200),
     *         @OA\Property(property="message", type="string", example="Success")
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(
     *           property="berita",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="{id}"),
     *             @OA\Property(property="judul", type="string", example="{judul berita}"),
     *             @OA\Property(property="slug", type="string", example="{slug berita}"),
     *             @OA\Property(property="konten", type="string", example="{konten berita}"),
     *             @OA\Property(property="thumbnail", type="string", example="{url thumbnail berita}"),
     *             @OA\Property(property="tanggalDibuat", type="string", format="date-time", example="{tanggal pembuatan}"),
     *             @OA\Property(property="tanggalDiperbarui", type="string", format="date-time", example="{tanggal pembaruan}")
     *           )
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Kesalahan Server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="status",
     *         type="object",
     *         @OA\Property(property="code", type="integer", example=500),
     *         @OA\Property(property="message", type="string", example="{Pesan kesalahan}")
     *       )
     *     )
     *   )
     * )
     */
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

    /**
     * @OA\Get(
     *   path="/berita/{slug}",
     *   tags={"Berita"},
     *   operationId="getBeritaBySlug",
     *   summary="Dapatkan Berita berdasarkan Slug",
     *   description="Mengambil berita tertentu berdasarkan slug",
     *   @OA\Parameter(
     *     name="slug",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Berhasil",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="status",
     *         type="object",
     *         @OA\Property(property="code", type="integer", example=200),
     *         @OA\Property(property="message", type="string", example="Success")
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         @OA\Property(
     *           property="berita",
     *           type="object",
     *           @OA\Property(property="id", type="integer", example="{id}"),
     *           @OA\Property(property="judul", type="string", example="{judul berita}"),
     *           @OA\Property(property="slug", type="string", example="{slug berita}"),
     *           @OA\Property(property="konten", type="string", example="{konten berita}"),
     *           @OA\Property(property="thumbnail", type="string", example="{url thumbnail berita}"),
     *             @OA\Property(property="tanggalDibuat", type="string", format="date-time", example="{tanggal pembuatan}"),
     *             @OA\Property(property="tanggalDiperbarui", type="string", format="date-time", example="{tanggal pembaruan}")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Tidak Ditemukan",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="status",
     *         type="object",
     *         @OA\Property(property="code", type="integer", example=404),
     *         @OA\Property(property="message", type="string", example="Berita tidak ditemukan.")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Kesalahan Server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="status",
     *         type="object",
     *         @OA\Property(property="code", type="integer", example=500),
     *         @OA\Property(property="message", type="string", example="{Pesan kesalahan}")
     *       )
     *     )
     *   )
     * )
     */
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
                    'berita' => $mappedNews[0]
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
