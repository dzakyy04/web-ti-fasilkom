<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Curiculum;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CuriculumController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/kurikulum",
     *   tags={"Kurikulum"},
     *   operationId="getAllCuriculums",
     *   summary="Dapatkan Semua Data Kurikulum",
     *   description="Mengambil semua data kurikulum",
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
     *           property="kurikulum",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="nama", type="string", example="{nama kurikulum}"),
     *             @OA\Property(property="deskripsi", type="string", example="deskripsi kurikulum"),
     *             @OA\Property(property="file", type="string", example="{url file kurikulum}"),
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
            $curiculums = Curiculum::get();
            $curiculums->transform(function ($curiculum) {
                $curiculum->file = Helper::convertFileUrl($curiculum->file);
                return $curiculum;
            });

            $mappedCurilucums = $this->mapCuriculums($curiculums);
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'kurikulum' => $mappedCurilucums
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
