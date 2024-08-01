<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Illuminate\Http\Request;

class MainCompetencyController extends Controller
{
    public function index()
    {
        $title = 'Kompetensi Utama';
        $mainCompetencies = Competency::where('type', 'main')->get();
        return view('admin.competencies.main.index', compact('title', 'mainCompetencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500'
        ]);

        Competency::create([
            'name' => null,
            'description' => $request->description,
            'icon' => null, 
            'type' => 'main',
        ]);

        return redirect()->route('main-competencies')->with('success', 'Kompetensi utama berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $mainCompetency = Competency::where('type', 'main')->findOrFail($id);
        $mainCompetency->update([
            'description' => $request->description,
        ]);

        return redirect()->route('main-competencies')->with('success', 'Kompetensi utama berhasil diperbarui.');
    }

    public function delete($id)
    {
        $mainCompetency = Competency::where('type', 'main')->findOrFail($id);
        $mainCompetency->delete();
        return redirect()->route('main-competencies')->with('success', 'Kompetensi utama berhasil dihapus.');
    }

    public function findMainCompetency($id)
    {
        $mainCompetency = Competency::where('type', 'main')->findOrFail($id);
        return response()->json($mainCompetency);
    }
    public function sessionMainCompetency($id)
    {
        session(['edit_competency_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
