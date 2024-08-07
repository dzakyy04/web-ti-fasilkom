<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderGalleryController extends Controller
{
    public function index()
    {
        $title = 'Galeri Slider';
        $sliderItems = Gallery::where('usability', 'slider')->get();
        return view('admin.galleries.slider.index', compact('title', 'sliderItems'));
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
            $directory = $request->type == 'image' ? 'public/slider-gallery/images' : 'public/slider-gallery/videos';
            $path = $request->file('file')->storeAs($directory, 'file_' . $timestamp . '.' . $fileExtension);
        }

        Gallery::create([
            'type' => $request->type,
            'path' => $path,
            'usability' => 'slider',
        ]);

        return redirect()->route('slider-galleries')->with('success', 'Slider berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'file' => 'nullable|mimes:jpg,jpeg,png,mp4,mov,avi,gif',
        ]);

        $sliderItem = Gallery::where('usability', 'slider')->findOrFail($id);

        if ($request->hasFile('file')) {
            if ($sliderItem->file_path) {
                Storage::delete($sliderItem->file_path);
            }
            $timestamp = Carbon::now()->format('Ymd_His');
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $directory = $request->type == 'image' ? 'public/slider-gallery/images' : 'public/slider-gallery/videos';
            $path = $request->file('file')->storeAs($directory, 'file_' . $timestamp . '.' . $fileExtension);
            $sliderItem->path = $path;
        }

        $sliderItem->update([
            'type' => $request->type,
            'path' => $sliderItem->path,
        ]);

        return redirect()->route('slider-galleries')->with('success', 'Slider berhasil diperbarui.');
    }

    public function delete($id)
    {
        $sliderItem = Gallery::where('usability', 'slider')->findOrFail($id);
        Storage::delete($sliderItem->path);
        $sliderItem->delete();
        return redirect()->route('slider-galleries')->with('success', 'Slider berhasil dihapus.');
    }

    public function findSliderGallery($id)
    {
        $sliderGallery = Gallery::where('usability', 'slider')->findOrFail($id);
        return response()->json($sliderGallery);
    }

    public function sessionSliderGallery($id)
    {
        session(['edit_gallery_id' => $id]);
        return response()->json(['status' => 'success']);
    }
}
