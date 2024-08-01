<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisionMission;
use Illuminate\Http\Request;

class VisionController extends Controller
{
    public function index()
    {
        $title = 'Visi';
        $visions = VisionMission::where('type', 'vision')->get();
        return view('admin.department-information.vision.index', compact('title', 'visions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'required|string|max:500'
        ]);

        VisionMission::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => null, 
            'type' => 'vision',
        ]);

        return redirect()->route('visions')->with('success', 'Visi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'required|string|max:500',
        ]);

        $vision = VisionMission::where('type', 'vision')->findOrFail($id);
        $vision->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('visions')->with('success', 'Visi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $vision = VisionMission::where('type', 'vision')->findOrFail($id);
        $vision->delete();
        return redirect()->route('visions')->with('success', 'Visi berhasil dihapus.');
    }

    public function findVision($id)
    {
        $vision = VisionMission::where('type', 'vision')->findOrFail($id);
        return response()->json($vision);
    }

    public function sessionVision($id)
    {
        session(['edit_competency_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
