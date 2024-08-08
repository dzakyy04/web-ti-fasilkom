<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\GuidelineAndSop;
use App\Http\Controllers\Controller;
use App\Traits\MapsResponse;

class GuidelineAndSopController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/panduan-dan-sop",
     *   tags={"Panduan dan SOP"},
     *   operationId="getAllGuidelinesAndSops",
     *   summary="Dapatkan Semua Panduan dan SOP",
     *   description="Mengambil semua data panduan dan SOP",
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
     *           property="panduan",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="judul", type="string", example="{Judul panduan}"),
     *             @OA\Property(property="file", type="string", example="{url file panduan}")
     *           )
     *         ),
     *         @OA\Property(
     *           property="sop",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="judul", type="string", example="{judul sop}"),
     *             @OA\Property(property="file", type="string", example="{url file sop}")
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
            $guidelines = GuidelineAndSop::where('type', 'panduan')->get();
            $sops = GuidelineAndSop::where('type', 'sop')->get();

            $guidelines->transform(function ($guideline) {
                $guideline->file = Helper::convertFileUrl($guideline->file);
                return $guideline;
            });
            $sops->transform(function ($sop) {
                $sop->file = Helper::convertFileUrl($sop->file);
                return $sop;
            });

            $mappedGuidelines = $this->mapGuidelineAndSops($guidelines);
            $mappedSops = $this->mapGuidelineAndSops($sops);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'panduan' => $mappedGuidelines,
                    'sop' => $mappedSops
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
