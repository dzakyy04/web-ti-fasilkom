<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Facility;
use App\Models\Lecturer;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FacilityController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/sarana-prasarana",
     *   tags={"Sarana Prasarana"},
     *   operationId="getAllFacilities",
     *   summary="Dapatkan Semua Sarana dan Prasarana",
     *   description="Mengambil semua data sarana dan prasarana berdasarkan lokasi",
     *   @OA\Parameter(
     *     name="lokasi",
     *     in="query",
     *     description="Lokasi sarana dan prasarana",
     *     required=false,
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
     *           property="saranaPrasarana",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="{id}"),
     *             @OA\Property(property="nama", type="string", example="{nama fasilitas}"),
     *             @OA\Property(property="lokasi", type="string", example="{lokasi fasilitas}"),
     *             @OA\Property(property="foto", type="string", example="{url foto fasilitas}")
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
    public function getAll(Request $request)
    {
        try {
            $location = $request->query('lokasi');

            $query = Facility::orderBy('location');

            if ($location) {
                $query->where('location', 'like', '%' . $location . '%');
            }

            $facilities = $query->get();
            $facilities->transform(function ($facility) {
                $facility->photo = Helper::convertImageUrl($facility->photo);
                return $facility;
            });

            $mappedFacilities = $this->mapFacilities($facilities);
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'saranaPrasarana' => $mappedFacilities
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
