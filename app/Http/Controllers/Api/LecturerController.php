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
                $lecturer->photo = Helper::convertImageUrl($lecturer->photo);
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

    public function getById($id)
    {
        try {
            $lecturer = Lecturer::with('educations', 'researchFields')->findOrFail($id);
            $lecturer->photo = Helper::convertImageUrl($lecturer->photo);
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
