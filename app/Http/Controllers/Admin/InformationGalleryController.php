<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InformationGalleryController extends Controller
{
    public function index()
    {
        $title = 'Galeri Informasi';
        $informations = Gallery::where('usability', 'information')->get();
        $disabledAddButton = Gallery::where('usability', 'information')->count() >= 1;
        return view('admin.galleries.information.index', compact('title', 'informations', 'disabledAddButton'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'file' => 'required|mimes:jpg,jpeg,png,mp4,mov,avi,gif',
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $directory = $request->type == 'image' ? 'public/information-gallery/images' : 'public/information-gallery/videos';
            $path = $request->file('file')->storeAs($directory, 'file_' . $timestamp . '.' . $fileExtension);
        }

        Gallery::create([
            'type' => $request->type,
            'path' => $path,
            'usability' => 'information',
        ]);

        return redirect()->route('information-galleries')->with('success', 'Galeri informasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'file' => 'nullable|mimes:jpg,jpeg,png,mp4,mov,avi,gif',
        ]);

        $information = Gallery::where('usability', 'information')->findOrFail($id);

        if ($request->hasFile('file')) {
            if ($information->file_path) {
                Storage::delete($information->file_path);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $directory = $request->type == 'image' ? 'public/information-gallery/images' : 'public/information-gallery/videos';
            $path = $request->file('file')->storeAs($directory, 'file_' . $timestamp . '.' . $fileExtension);
            $information->path = $path;
        }

        $information->update([
            'type' => $request->type,
            'path' => $information->path,
        ]);

        return redirect()->route('information-galleries')->with('success', 'Galeri informasi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $information = Gallery::where('usability', 'information')->findOrFail($id);
        Storage::delete($information->path);
        $information->delete();
        return redirect()->route('information-galleries')->with('success', 'Galeri informasi berhasil dihapus.');
    }

    public function findInformationGallery($id)
    {
        $information = Gallery::where('usability', 'information')->findOrFail($id);
        return response()->json($information);
    }

    public function sessionInformationGallery($id)
    {
        session(['edit_gallery_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
