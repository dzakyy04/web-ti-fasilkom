<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuidelineAndSop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuidelineController extends Controller
{
    public function index()
    {
        $title = 'Panduan';
        $guidelines = GuidelineAndSop::where('type', 'guideline')->get();
        return view('admin.guideline-and-sop.guideline.index', compact('title', 'guidelines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $filePath = $request->file('file')->storeAs('public/panduan/file', $request->title . '_' . $timestamp . '.' . $fileExtension);
        }

        GuidelineAndSop::create([
            'title' => $request->title,
            'file' => $filePath,
            'type' => 'guideline',
        ]);

        return redirect()->route('guidelines')->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
        ]);

        $guideline = GuidelineAndSop::where('type', 'guideline')->findOrFail($id);

        if ($request->hasFile('file')) {
            if ($guideline->file) {
                Storage::delete($guideline->file);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $filePath = $request->file('file')->storeAs('public/panduan/file', $request->title . '_' . $timestamp . '.' . $fileExtension);
            $guideline->file = $filePath;
        }

        $guideline->update([
            'title' => $request->title,
            'file' => $guideline->file,
        ]);

        return redirect()->route('guidelines')->with('success', 'Panduan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $guideline = GuidelineAndSop::where('type', 'guideline')->findOrFail($id);
        if ($guideline->file) {
            Storage::delete($guideline->file);
        }
        $guideline->delete();
        return redirect()->route('guidelines')->with('success', 'Panduan berhasil dihapus.');
    }

    public function findGuideline($id)
    {
        $guideline = GuidelineAndSop::where('type', 'guideline')->findOrFail($id);
        return response()->json($guideline);
    }

    public function sessionGuideline($id)
    {
        session(['edit_guideline_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}

