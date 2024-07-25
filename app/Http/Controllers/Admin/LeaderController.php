<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LeaderController extends Controller
{
        public function index()
    {
        $title = 'Pimpinan';
        $leaders = Leader::get();
        return view('admin.organization-structure.leader.index', compact('title', 'leaders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'position' => 'required|string',
            'description' => 'required|string',
            'photo' => 'required|image',
        ]);

        $timestamp = Carbon::now()->format('Ymd_His');
        $photoExtension = $request->file('photo')->getClientOriginalExtension();
        $photoPath = $request->file('photo')->storeAs('public/pimpinan/foto', $request->name . '_' . $timestamp . '.' . $photoExtension);

        Leader::create([
            'photo' => $photoPath,
            'name' => $request->name,
            'position' => $request->position,
            'description' => $request->description,
        ]);

        return redirect()->route('leaders')->with('success', 'Data pimpinan berhasil ditambahkan.');
    }

    public function findLeader($id)
    {
        $leader = Leader::findOrFail($id);

        return response()->json($leader);
    }

    public function update(Request $request, $id)
    {
        $leader = Leader::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image',
            'name' => 'required|string',
            'position' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('photo')) {
            if ($leader->photo) {
                Storage::delete($leader->photo);
            }

            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('public/pimpinan/foto', $request->name . '_' . $timestamp . '.' . $photoExtension);
            $leader->photo = $photoPath;
        }

        $leader->update([
            'name' => $request->name,
            'position' => $request->position,
            'description' => $request->description,
        ]);

        return redirect()->route('leaders')->with('success', 'Data pimpinan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $leader = Leader::findOrFail($id);
        Storage::delete($leader->photo);
        $leader->delete();
        return redirect()->route('leaders')->with('success', 'Data pimpinan berhasil dihapus.');
    }
}
