<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileGalleryController extends Controller
{
    public function index()
    {
        $title = 'Galeri Profil';
        $profiles = Gallery::where('usability', 'profile')->get();
        $disabledAddButton = Gallery::where('usability', 'profile')->count() >= 1;
        return view('admin.galleries.profile.index', compact('title', 'profiles', 'disabledAddButton'));
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
            $directory = $request->type == 'image' ? 'public/profile-gallery/images' : 'public/profile-gallery/videos';
            $path = $request->file('file')->storeAs($directory, 'file_' . $timestamp . '.' . $fileExtension);
        }

        Gallery::create([
            'type' => $request->type,
            'path' => $path,
            'usability' => 'profile',
        ]);

        return redirect()->route('profile-galleries')->with('success', 'Galeri profil berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'file' => 'nullable|mimes:jpg,jpeg,png,mp4,mov,avi,gif',
        ]);

        $profile = Gallery::where('usability', 'profile')->findOrFail($id);

        if ($request->hasFile('file')) {
            if ($profile->file_path) {
                Storage::delete($profile->file_path);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $directory = $request->type == 'image' ? 'public/profile-gallery/images' : 'public/profile-gallery/videos';
            $path = $request->file('file')->storeAs($directory, 'file_' . $timestamp . '.' . $fileExtension);
            $profile->path = $path;
        }

        $profile->update([
            'type' => $request->type,
            'path' => $profile->path,
        ]);

        return redirect()->route('profile-galleries')->with('success', 'Galeri profil berhasil diperbarui.');
    }

    public function delete($id)
    {
        $profile = Gallery::where('usability', 'profile')->findOrFail($id);
        Storage::delete($profile->path);
        $profile->delete();
        return redirect()->route('profile-galleries')->with('success', 'Galeri profil berhasil dihapus.');
    }

    public function findProfileGallery($id)
    {
        $profile = Gallery::where('usability', 'profile')->findOrFail($id);
        return response()->json($profile);
    }

    public function sessionProfileGallery($id)
    {
        session(['edit_gallery_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
