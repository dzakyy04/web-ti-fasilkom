<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Competency;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompetencyController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/kompetensi",
     *   tags={"Kompetensi"},
     *   operationId="getAllKompetensi",
     *   summary="Dapatkan Semua Kompetensi",
     *   description="Mengambil semua kompetensi berdasarkan tipe",
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
     *           property="kompetensiUtama",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="deskripsi", type="string", example="{deskripsi kompetensi utama}")
     *           )
     *         ),
     *         @OA\Property(
     *           property="kompetensiPendukung",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="nama", type="string", example="{nama kompetensi pendukung}"),
     *             @OA\Property(property="deskripsi", type="string", example="Pikiran yang maju")
     *           )
     *         ),
     *         @OA\Property(
     *           property="kompetensiLulusan",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="nama", type="string", example="{nama kompetensi lulusan}"),
     *             @OA\Property(property="deskripsi", type="string", example="{deskripsi kompetensi lulusan}"),
     *             @OA\Property(property="icon", type="string", example="{url icon kompetensi lulusan}")
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
            $mainCompetencies = Competency::where('type', 'main')->get();
            $supportCompetencies = Competency::where('type', 'support')->get();
            $graduateCompetencies = Competency::where('type', 'graduate')->get();

            $graduateCompetencies->transform(function ($competency) {
                $competency->icon = Helper::convertImageUrl($competency->icon);
                return $competency;
            });

            $mappedMainCompetencies = $this->mapCompetencies($mainCompetencies, 'main');
            $mappedSupportCompetencies = $this->mapCompetencies($supportCompetencies, 'support');
            $mappedGraduateCompetencies = $this->mapCompetencies($graduateCompetencies, 'graduate');

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'kompetensiUtama' => $mappedMainCompetencies,
                    'kompetensiPendukung' => $mappedSupportCompetencies,
                    'kompetensiLulusan' => $mappedGraduateCompetencies
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
