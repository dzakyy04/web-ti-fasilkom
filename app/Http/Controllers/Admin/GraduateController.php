<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Graduate;
use Illuminate\Http\Request;

class GraduateController extends Controller
{
    public function indexMainCompetency()
    {
        $title = 'Kompetensi Utama';
        $mainCompetencies = Graduate::first()->main_competencies ?? [];
        return view('admin.graduates.main.index', compact('mainCompetencies', 'title'));
    }

    public function storeMainCompetency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $graduate = Graduate::first();
        if (!$graduate) {
            $graduate = new Graduate();
            $graduate->main_competencies = [];
        }

        $mainCompetencies = $graduate->main_competencies ?? [];
        $id = uniqid();
        $mainCompetencies[] = array_merge($request->only('name', 'description'), ['id' => $id]);
        $graduate->main_competencies = $mainCompetencies;
        $graduate->save();

        return redirect()->route('graduates.main-competencies')->with('success', 'Kompetensi utama berhasil ditambahkan.');
    }

    public function updateMainCompetency(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $graduate = Graduate::first();
        if (!$graduate) {
            return redirect()->route('graduates.main-competencies')->with('error', 'Graduate not found.');
        }

        $mainCompetencies = $graduate->main_competencies;
        foreach ($mainCompetencies as &$competency) {
            if ($competency['id'] == $id) {
                $competency['name'] = $request->input('name');
                $competency['description'] = $request->input('description');
            }
        }

        $graduate->main_competencies = $mainCompetencies;
        $graduate->save();

        return redirect()->route('graduates.main-competencies')->with('success', 'Kompetensi utama berhasil diperbarui.');
    }

    public function deleteMainCompetency($id)
    {
        $graduate = Graduate::first();
        if (!$graduate) {
            return redirect()->route('graduates.main-competencies')->with('error', 'Graduate not found.');
        }

        $mainCompetencies = $graduate->main_competencies;
        $mainCompetencies = array_filter($mainCompetencies, function ($competency) use ($id) {
            return $competency['id'] != $id;
        });

        $graduate->main_competencies = array_values($mainCompetencies); // Re-index array
        $graduate->save();

        return redirect()->route('graduates.main-competencies')->with('success', 'Kompetensi utama berhasil dihapus.');
    }

    public function findGraduatesMainCompetency($id)
    {
        $graduate = Graduate::first();
        if (!$graduate) {
            return response()->json(['error' => 'Graduate not found'], 404);
        }

        $mainCompetencies = $graduate->main_competencies;
        $competency = collect($mainCompetencies)->firstWhere('id', $id);

        if (!$competency) {
            return response()->json(['error' => 'Main Competency not found'], 404);
        }

        return response()->json($competency);
    }

    public function indexSupportCompetency()
    {
        $title = 'Kompetensi Pendukung';
        $supportCompetencies = Graduate::first()->support_competencies ?? [];
        return view('admin.graduates.support.index', compact('supportCompetencies', 'title'));
    }

    public function storeSupportCompetency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $graduate = Graduate::first();
        if (!$graduate) {
            $graduate = new Graduate();
            $graduate->support_competencies = [];
        }

        $supportCompetencies = $graduate->support_competencies ?? [];
        $id = uniqid();
        $supportCompetencies[] = array_merge($request->only('name', 'description'), ['id' => $id]);
        $graduate->support_competencies = $supportCompetencies;
        $graduate->save();

        return redirect()->route('graduates.support-competencies')->with('success', 'Kompetensi pendukung berhasil ditambahkan.');
    }

    public function updateSupportCompetency(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $graduate = Graduate::first();
        if (!$graduate) {
            return redirect()->route('graduates.support-competencies')->with('error', 'Graduate not found.');
        }

        $supportCompetencies = $graduate->support_competencies;
        foreach ($supportCompetencies as &$competency) {
            if ($competency['id'] == $id) {
                $competency['name'] = $request->input('name');
                $competency['description'] = $request->input('description');
            }
        }

        $graduate->support_competencies = $supportCompetencies;
        $graduate->save();

        return redirect()->route('graduates.support-competencies')->with('success', 'Kompetensi pendukung berhasil diperbarui.');
    }

    public function deleteSupportCompetency($id)
    {
        $graduate = Graduate::first();
        if (!$graduate) {
            return redirect()->route('graduates.support-competencies')->with('error', 'Graduate not found.');
        }

        $supportCompetencies = $graduate->support_competencies;
        $supportCompetencies = array_filter($supportCompetencies, function ($competency) use ($id) {
            return $competency['id'] != $id;
        });

        $graduate->support_competencies = array_values($supportCompetencies); // Re-index array
        $graduate->save();

        return redirect()->route('graduates.support-competencies')->with('success', 'Kompetensi pendukung berhasil dihapus.');
    }

    public function findGraduatesSupportCompetency($id)
    {
        $graduate = Graduate::first();
        if (!$graduate) {
            return response()->json(['error' => 'Graduate not found'], 404);
        }

        $supportCompetencies = $graduate->support_competencies;
        $competency = collect($supportCompetencies)->firstWhere('id', $id);

        if (!$competency) {
            return response()->json(['error' => 'Support Competency not found'], 404);
        }

        return response()->json($competency);
    }
    

}
