<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curiculum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CurriculumController extends Controller
{
    public function index()
    {
        $title = 'Kurikulum';
        $curriculums = Curiculum::all();
        return view('admin.curriculums.index', compact('title', 'curriculums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $filePath = $request->file('file')->storeAs('public/kurikulum/file', $request->name . '_' . $timestamp . '.' . $fileExtension);
        }

        Curiculum::create([
            'name' => $request->name,
            'description' => $request->description,
            'file' => $filePath,
        ]);

        return redirect()->route('curriculums')->with('success', 'Kurikulum berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
        ]);

        $curriculum = Curiculum::findOrFail($id);

        if ($request->hasFile('file')) {
            if ($curriculum->file) {
                Storage::delete($curriculum->file);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $filePath = $request->file('file')->storeAs('public/kurikulum/file', $request->name . '_' . $timestamp . '.' . $fileExtension);
            $curriculum->file = $filePath;
        }

        $curriculum->update([
            'name' => $request->name,
            'description' => $request->description,
            'file' => $curriculum->file,
        ]);

        return redirect()->route('curriculums')->with('success', 'Kurikulum berhasil diperbarui.');
    }

    public function delete($id)
    {
        $curriculum = Curiculum::findOrFail($id);
        if ($curriculum->file) {
            Storage::delete($curriculum->file);
        }
        $curriculum->delete();
        return redirect()->route('curriculums')->with('success', 'Kurikulum berhasil dihapus.');
    }

    public function findCurriculum($id)
    {
        $curriculum = Curiculum::findOrFail($id);
        return response()->json($curriculum);
    }

    public function sessionCurriculum($id)
    {
        session(['edit_curriculum_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
