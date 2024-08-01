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
     *   @OA\Parameter(
     *     name="halaman",
     *     in="query",
     *     description="Nomor halaman",
     *     required=false,
     *     @OA\Schema(type="integer", default=1)
     *   ),
     *   @OA\Parameter(
     *     name="batas",
     *     in="query",
     *     description="Jumlah item per halaman",
     *     required=false,
     *     @OA\Schema(type="integer", default=10)
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
     *         ),
     *         @OA\Property(
     *           property="paginasi",
     *           type="object",
     *           @OA\Property(property="halamanSekarang", type="integer", example="{halaman sekarang}"),
     *           @OA\Property(property="halamanTerakhir", type="integer", example="{halaman terakhir}"),
     *           @OA\Property(property="batasPerHalaman", type="integer", example="{batas per halaman}"),
     *           @OA\Property(property="totalItem", type="integer", example="{total item}")
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
    public function getAll(Request $request)
    {
        try {
            $limit = $request->input('batas', 10);
            $page = $request->input('halaman', 1);

            $news = Article::where('type', 'news')
                ->latest()
                ->paginate($limit, ['*'], 'halaman', $page);

            $news->getCollection()->transform(function ($newsItem) {
                $newsItem->content = Helper::processContent($newsItem->content);
                $newsItem->thumbnail = Helper::convertImageUrl($newsItem->thumbnail);
                return $newsItem;
            });

            $mappedNews = $this->mapArticles($news->getCollection());

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'berita' => $mappedNews,
                    'paginasi' => [
                        'halamanSekarang' => $news->currentPage(),
                        'halamanTerakhir' => $news->lastPage(),
                        'batasPerHalaman' => $news->perPage(),
                        'totalItem' => $news->total()
                    ]
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
