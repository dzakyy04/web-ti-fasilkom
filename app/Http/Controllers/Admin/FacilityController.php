<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FacilityController extends Controller
{
    public function index()
    {
        $title = 'Fasilitas';
        $facilities = Facility::get();
        return view('admin.facilities.index', compact('title', 'facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'location' => 'required|in:Indralaya,Palembang',
            'photo' => 'required|image',
        ]);

        $timestamp = Carbon::now()->format('Ymd_His');
        $photoExtension = $request->file('photo')->getClientOriginalExtension();
        $photoPath = $request->file('photo')->storeAs('public/fasilitas/foto', $request->name . '_' . $timestamp . '.' . $photoExtension);

        Facility::create([
            'photo' => $photoPath,
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('facilities')->with('success', 'Sarana dan Prasarana berhasil ditambahkan.');
    }

    public function findFacility($id)
    {
        $facility = Facility::findOrFail($id);

        return response()->json($facility);
    }

    public function update(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image',
            'name' => 'required|string',
            'location' => 'required|string|max:20|',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('photo')) {
            if ($facility->photo) {
                Storage::delete($facility->photo);
            }

            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('public/fasilitas/foto', $request->name . '_' . $timestamp . '.' . $photoExtension);
            $facility->photo = $photoPath;
        }

        $facility->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('facilities')->with('success', 'Data sarana dan prasarana berhasil diperbarui.');
    }

    public function delete($id)
    {
        $facility = Facility::findOrFail($id);
        Storage::delete($facility->photo);
        $facility->delete();
        return redirect()->route('facilities')->with('success', 'Data sarana dan prasarana berhasil dihapus.');
    }

    public function sessionFacility($id)
    {
        session(['edit_competency_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
