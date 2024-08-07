<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Information;
use Illuminate\Http\Request;
use App\Models\VisionMission;
use App\Http\Controllers\Controller;
use App\Traits\MapsResponse;

class InformationController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/informasi-jurusan",
     *   tags={"Informasi, Visi, dan Misi"},
     *   operationId="getAllInformasiVisiMisi",
     *   summary="Dapatkan Informasi, Visi, dan Misi",
     *   description="Mengambil informasi terbaru, visi terbaru, dan semua misi",
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
     *         @OA\Property(property="informasi", type="string", example="{informasi jurusan}"),
     *         @OA\Property(
     *           property="visi",
     *           type="object",
     *           @OA\Property(property="judul", type="string", example="{judul visi}"),
     *           @OA\Property(property="deskripsi", type="string", example="{deskripsi visi}")
     *         ),
     *         @OA\Property(
     *           property="misi",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="icon", type="string", example="{url icon misi}"),
     *             @OA\Property(property="judul", type="string", example="{judul misi}"),
     *             @OA\Property(property="deskripsi", type="string", example="{deskripsi misi}")
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
            $information = Information::latest()->firstOrFail();
            $vision = VisionMission::where('type', 'vision')->latest()->firstOrFail();
            $missions = VisionMission::where('type', 'mission')->get();

            $missions->transform(function ($mission) {
                $mission->icon = Helper::convertFileUrl($mission->icon);
                return $mission;
            });

            $mappedMissions = $this->mapMissions($missions);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'informasi' => $information->description,
                    'visi' => [
                        'judul' => $vision->title,
                        'deskripsi' => $vision->description
                    ],
                    'misi' => $mappedMissions
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
}
