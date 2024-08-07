<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Lecturer;
use App\Traits\MapsResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LecturerController extends Controller
{
    use MapsResponse;

    /**
     * @OA\Get(
     *   path="/dosen",
     *   tags={"Dosen"},
     *   operationId="getAllDosen",
     *   summary="Dapatkan Semua Dosen",
     *   description="Mengambil semua dosen",
     *   @OA\Parameter(
     *     name="research",
     *     in="query",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     description="Nama bidang penelitian"
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
     *           property="dosen",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="{id}"),
     *             @OA\Property(property="nama", type="string", example="{nama dosen}"),
     *             @OA\Property(property="nip", type="string", example="{nip dosen}"),
     *             @OA\Property(property="nidn", type="string", example="{nidn dosen}"),
     *             @OA\Property(property="jabatan", type="string", example="{jabatan dosen}"),
     *             @OA\Property(property="foto", type="string", example="{url foto dosen}"),
     *             @OA\Property(
     *               property="pendidikan",
     *               type="array",
     *               @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="jenjang", type="string", example="{jenjang pendidikan}"),
     *                 @OA\Property(property="jurusan", type="string", example="{jurusan pendidikan}"),
     *                 @OA\Property(property="institusi", type="string", example="{institusi pendidikan}")
     *               )
     *             ),
     *             @OA\Property(
     *               property="bidangPenelitian",
     *               type="array",
     *               @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="nama", type="string", example="{nama bidang penelitian}")
     *               )
     *             )
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
            $research = $request->query('research');

            $query = Lecturer::with('educations', 'researchFields');

            if ($research) {
                $query->whereHas('researchFields', function ($q) use ($research) {
                    $q->where('name', 'like', '%' . $research . '%');
                });
            }

            $lecturers = $query->get();
            $lecturers->transform(function ($lecturer) {
                $lecturer->photo = Helper::convertFileUrl($lecturer->photo);
                return $lecturer;
            });

            $mappedLecturers = $this->mapLecturers($lecturers);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'dosen' => $mappedLecturers
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
     *   path="/dosen/{id}",
     *   tags={"Dosen"},
     *   operationId="getDosenById",
     *   summary="Dapatkan Dosen berdasarkan ID",
     *   description="Mengambil dosen tertentu berdasarkan ID",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
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
     *           property="dosen",
     *           type="object",
     *           @OA\Property(property="id", type="integer", example="{id}"),
     *           @OA\Property(property="nama", type="string", example="{nama dosen}"),
     *           @OA\Property(property="nip", type="string", example="{nip dosen}"),
     *           @OA\Property(property="nidn", type="string", example="{nidn dosen}"),
     *           @OA\Property(property="jabatan", type="string", example="{jabatan dosen}"),
     *           @OA\Property(property="foto", type="string", example="{url foto dosen}"),
     *           @OA\Property(
     *             property="pendidikan",
     *             type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="jenjang", type="string", example="{jenjang pendidikan}"),
     *               @OA\Property(property="jurusan", type="string", example="{jurusan pendidikan}"),
     *               @OA\Property(property="institusi", type="string", example="{institusi pendidikan}")
     *             )
     *           ),
     *           @OA\Property(
     *             property="bidangPenelitian",
     *             type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="nama", type="string", example="{nama bidang penelitian}")
     *             )
     *           )
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
     *         @OA\Property(property="message", type="string", example="Dosen tidak ditemukan.")
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
    public function getById($id)
    {
        try {
            $lecturer = Lecturer::with('educations', 'researchFields')->findOrFail($id);
            $lecturer->photo = Helper::convertFileUrl($lecturer->photo);
            $mappedLecturer = $this->mapLecturers(collect([$lecturer]));

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'dosen' => $mappedLecturer[0]
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Dosen tidak ditemukan.'
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
