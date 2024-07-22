<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\Leader;
use App\Helpers\Helper;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrganizationController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/struktur-jabatan",
     *   tags={"Struktur Jabatan"},
     *   operationId="getAllStrukturJabatan",
     *   summary="Dapatkan Semua Struktur Jabatan",
     *   description="Mengambil semua struktur jabatan",
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
     *           property="pimpinan",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="{id}"),
     *             @OA\Property(property="nama", type="string", example="{nama pimpinan}"),
     *             @OA\Property(property="jabatan", type="string", example="{jabatan pimpinan}"),
     *             @OA\Property(property="deskripsi", type="string", example="{deskripsi pimpinan}"),
     *             @OA\Property(property="foto", type="string", example="{url foto pimpinan}")
     *           )
     *         ),
     *         @OA\Property(
     *           property="admin",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="{id}"),
     *             @OA\Property(property="name", type="string", example="{name admin}"),
     *             @OA\Property(property="foto", type="string", example="{url foto admin}"),
     *             @OA\Property(property="lokasi", type="string", example="{lokasi kampus admin}")
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

            $leaders = Leader::get();
            $leaders->transform(function ($leader) {
                $leader->photo = Helper::convertImageUrl($leader->photo);
                return $leader;
            });
            $mappedLeaders = $this->mapLeaders($leaders);

            $admins = Admin::get();
            $admins->transform(function ($admin) {
                $admin->photo = Helper::convertImageUrl($admin->photo);
                return $admin;
            });
            $mappedAdmins = $this->mapAdmins($admins);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'pimpinan' => $mappedLeaders,
                    'admin' => $mappedAdmins
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
