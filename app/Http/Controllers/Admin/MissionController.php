<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisionMission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MissionController extends Controller
{
    public function index()
    {
        $title = 'Misi';
        $missions = VisionMission::where('type', 'mission')->get();
        return view('admin.department-information.mission.index', compact('title', 'missions'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:svg',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('icon')->getClientOriginalExtension();
            $iconPath = $request->file('icon')->storeAs('public/informasi-jurusan-misi/icon', $request->title . '_' . $timestamp . '.' . $photoExtension);
        }

        VisionMission::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $iconPath,
            'type' => 'mission',
        ]);

        return redirect()->route('missions')->with('success', 'Misi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:svg',
        ]);

        $mission = VisionMission::where('type', 'mission')->findOrFail($id);

        if ($request->hasFile('icon')) {
            if ($mission->icon) {
                Storage::delete($mission->icon);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('icon')->getClientOriginalExtension();
            $iconPath = $request->file('icon')->storeAs('public/informasi-jurusan-misi/icon', $request->title . '_' . $timestamp . '.' . $photoExtension);
            $mission->icon = $iconPath;
        }

        $mission->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $mission->icon,
        ]);

        return redirect()->route('missions')->with('success', 'Misi berhasil diperbarui.');
    }


    public function delete($id)
    {
        $mission = VisionMission::where('type', 'mission')->findOrFail($id);
        Storage::delete($mission->icon);
        $mission->delete();
        return redirect()->route('missions')->with('success', 'Misi berhasil dihapus.');
    }

    public function findMission($id)
    {
        $mission = VisionMission::where('type', 'mission')->findOrFail($id);
        return response()->json($mission);
    }

    public function sessionMission($id)
    {
        session(['edit_competency_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
