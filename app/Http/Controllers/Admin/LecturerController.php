<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function index()
    {
        $title = 'Dosen';
        $lecturers = Lecturer::with('educations')->get();

        return view('admin.lecturers.index', compact('title', 'lecturers'));
    }
}
