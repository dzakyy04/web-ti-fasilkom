<?php

namespace App\Http\Controllers\Api;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
