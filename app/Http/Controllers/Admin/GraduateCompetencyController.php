<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GraduateCompetencyController extends Controller
{
    public function index()
    {
        $title = 'Kompetensi Lulusan';
        $graduateCompetencies = Competency::where('type', 'graduate')->get();
        return view('admin.competencies.graduate.index', compact('title', 'graduateCompetencies'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|image',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('icon')->getClientOriginalExtension();
            $iconPath = $request->file('icon')->storeAs('public/kompetensi-lulusan/icon', $request->name . '_' . $timestamp . '.' . $photoExtension);
        }

        Competency::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $iconPath,
            'type' => 'graduate',
        ]);

        return redirect()->route('graduate-competencies')->with('success', 'Kompetensi lulusan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|image',
        ]);

        $competency = Competency::where('type', 'graduate')->findOrFail($id);

        if ($request->hasFile('icon')) {
            if ($competency->icon) {
                Storage::delete($competency->icon);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('icon')->getClientOriginalExtension();
            $iconPath = $request->file('icon')->storeAs('public/kompetensi-lulusan/icon', $request->name . '_' . $timestamp . '.' . $photoExtension);
            $competency->icon = $iconPath;
        }

        $competency->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $competency->icon,
        ]);

        return redirect()->route('graduate-competencies')->with('success', 'Kompetensi lulusan berhasil diperbarui.');
    }


    public function delete($id)
    {
        $graduateCompetency = Competency::where('type', 'graduate')->findOrFail($id);
        Storage::delete($graduateCompetency->icon);
        $graduateCompetency->delete();
        return redirect()->route('graduate-competencies')->with('success', 'Kompetensi pendukung berhasil dihapus.');
    }

    public function findGraduateCompetency($id)
    {
        $graduateCompetency = Competency::where('type', 'graduate')->findOrFail($id);
        return response()->json($graduateCompetency);
    }
}
