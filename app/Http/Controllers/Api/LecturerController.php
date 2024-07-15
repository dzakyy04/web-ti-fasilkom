<?php

namespace App\Http\Controllers\Api;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LecturerController extends Controller
{
    public function getAll(Request $request)
    {
        $research = $request->query('research');

        $query = Lecturer::with('educations', 'researchFields');

        if ($research) {
            $query->whereHas('researchFields', function ($q) use ($research) {
                $q->where('name', 'like', '%' . $research . '%');
            });
        }

        $lecturers = $query->get();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Success'
            ],
            'data' => [
                'lecturers' => $lecturers
            ]
        ]);
    }

    public function getById($id)
    {
        try {
            $lecturer = Lecturer::with('educations', 'researchFields')->findOrFail($id);

            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Success'
                ],
                'data' => [
                    'lecturer' => $lecturer
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Lecturer not found.'
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
