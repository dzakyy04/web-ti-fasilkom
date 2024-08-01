<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use Illuminate\Http\Request;

class SupportCompetencyController extends Controller
{
    public function index()
    {
        $title = 'Kompetensi Pendukung';
        $supportCompetencies = Competency::where('type', 'support')->get();
        return view('admin.competencies.support.index', compact('title', 'supportCompetencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:500',
            'description' => 'required|string|max:500'
        ]);

        Competency::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => null, 
            'type' => 'support',
        ]);

        return redirect()->route('support-competencies')->with('success', 'Kompetensi pendukung berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:500',
            'description' => 'required|string|max:500',
        ]);

        $supportCompetency = Competency::where('type', 'support')->findOrFail($id);
        $supportCompetency->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('support-competencies')->with('success', 'Kompetensi pendukung berhasil diperbarui.');
    }

    public function delete($id)
    {
        $supportCompetency = Competency::where('type', 'support')->findOrFail($id);
        $supportCompetency->delete();
        return redirect()->route('support-competencies')->with('success', 'Kompetensi pendukung berhasil dihapus.');
    }

    public function findSupportCompetency($id)
    {
        $supportCompetency = Competency::where('type', 'support')->findOrFail($id);
        return response()->json($supportCompetency);
    }

    public function sessionSupportCompetency($id)
    {
        session(['edit_competency_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
