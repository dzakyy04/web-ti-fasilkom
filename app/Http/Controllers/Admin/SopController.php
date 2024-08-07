<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuidelineAndSop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SopController extends Controller
{
    public function index()
    {
        $title = 'SOP';
        $sops = GuidelineAndSop::where('type', 'sop')->get();
        return view('admin.guideline-and-sop.sop.index', compact('title', 'sops'));
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
            $filePath = $request->file('file')->storeAs('public/sop/file', $request->title . '_' . $timestamp . '.' . $fileExtension);
        }

        GuidelineAndSop::create([
            'title' => $request->title,
            'file' => $filePath,
            'type' => 'sop',
        ]);

        return redirect()->route('sops')->with('success', 'SOP berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
        ]);

        $sop = GuidelineAndSop::where('type', 'sop')->findOrFail($id);

        if ($request->hasFile('file')) {
            if ($sop->file) {
                Storage::delete($sop->file);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $filePath = $request->file('file')->storeAs('public/sop/file', $request->title . '_' . $timestamp . '.' . $fileExtension);
            $sop->file = $filePath;
        }

        $sop->update([
            'title' => $request->title,
            'file' => $sop->file,
        ]);

        return redirect()->route('sops')->with('success', 'SOP berhasil diperbarui.');
    }

    public function delete($id)
    {
        $sop = GuidelineAndSop::where('type', 'sop')->findOrFail($id);
        if ($sop->file) {
            Storage::delete($sop->file);
        }
        $sop->delete();
        return redirect()->route('sops')->with('success', 'SOP berhasil dihapus.');
    }

    public function findSOP($id)
    {
        $sop = GuidelineAndSop::where('type', 'sop')->findOrFail($id);
        return response()->json($sop);
    }

    public function sessionSOP($id)
    {
        session(['edit_sop_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}

