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

    /**
     * @OA\Get(
     *   path="/pengumuman",
     *   tags={"Pengumuman"},
     *   operationId="getAllPengumuman",
     *   summary="Dapatkan Semua Pengumuman",
     *   description="Mengambil semua pengumuman",
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
     *   @OA\Parameter(
     *     name="panjangKonten",
     *     in="query",
     *     description="Jumlah karakter dalam konten",
     *     required=false,
     *     @OA\Schema(type="integer", default=250)
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
     *           property="pengumuman",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="{id}"),
     *             @OA\Property(property="judul", type="string", example="{judul pengumuman}"),
     *             @OA\Property(property="slug", type="string", example="{slug pengumuman}"),
     *             @OA\Property(property="konten", type="string", example="{konten pengumuman}"),
     *             @OA\Property(property="thumbnail", type="string", example="{url thumbnail pengumuman}"),
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
            $length = $request->query('panjangKonten', 250);
            $limit = $request->input('batas', 10);
            $page = $request->input('halaman', 1);

            $announcements = Article::where('type', 'announcement')
                ->latest()
                ->paginate($limit, ['*'], 'halaman', $page);

            $announcements->getCollection()->transform(function ($announcement) use ($length) {
                $announcement->content = Helper::processContent($announcement->content, $length);
                $announcement->thumbnail = Helper::convertFileUrl($announcement->thumbnail);
                return $announcement;
            });

            $mappedAnnouncements = $this->mapArticles($announcements->getCollection());

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'pengumuman' => $mappedAnnouncements,
                    'paginasi' => [
                        'halamanSekarang' => $announcements->currentPage(),
                        'halamanTerakhir' => $announcements->lastPage(),
                        'batasPerHalaman' => $announcements->perPage(),
                        'totalItem' => $announcements->total()
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
     *   path="/pengumuman/{slug}",
     *   tags={"Pengumuman"},
     *   operationId="getPengumumanBySlug",
     *   summary="Dapatkan Pengumuman berdasarkan Slug",
     *   description="Mengambil pengumuman tertentu berdasarkan slug",
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
     *           property="pengumuman",
     *           type="object",
     *           @OA\Property(property="id", type="integer", example="{id}"),
     *           @OA\Property(property="judul", type="string", example="{judul pengumuman}"),
     *           @OA\Property(property="slug", type="string", example="{slug pengumuman}"),
     *           @OA\Property(property="konten", type="string", example="{konten pengumuman}"),
     *           @OA\Property(property="thumbnail", type="string", example="{url thumbnail pengumuman}"),
     *           @OA\Property(property="tanggalDibuat", type="string", format="date-time", example="{tanggal pembuatan}"),
     *           @OA\Property(property="tanggalDiperbarui", type="string", format="date-time", example="{tanggal pembaruan}")
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
     *         @OA\Property(property="message", type="string", example="Pengumuman tidak ditemukan.")
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
            $announcement = Article::where('slug', $slug)
                ->where('type', 'announcement')
                ->firstOrFail();

            $announcement->thumbnail = Helper::convertFileUrl($announcement->thumbnail);
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
