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
            $announcements = Article::where('type', 'announcement')->latest()->get();

            $announcements->transform(function ($announcement) {
                $announcement->content = Helper::processContent($announcement->content);
                $announcement->thumbnail = Helper::convertImageUrl($announcement->thumbnail);
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

            $announcement->thumbnail = Helper::convertImageUrl($announcement->thumbnail);
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
