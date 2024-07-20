<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $title = 'Admin';
        $admins = Admin::get();
        return view('admin.admin.index', compact('title', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|in:Kampus Indralaya,Kampus Palembang',
            'photo' => 'required|image|max:2048',
        ]);

        $timestamp = Carbon::now()->format('Ymd_His');
        $photoExtension = $request->file('photo')->getClientOriginalExtension();
        $photoPath = $request->file('photo')->storeAs('public/admin/foto', $request->name . '_' . $timestamp . '.' . $photoExtension);

        Admin::create([
            'photo' => $photoPath,
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('admins')->with('success', 'Data admin berhasil ditambahkan.');
    }

    public function findAdmin($id)
    {
        $admin = Admin::findOrFail($id);

        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:30|',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('photo')) {
            if ($admin->photo) {
                Storage::delete($admin->photo);
            }

            $timestamp = Carbon::now()->format('Ymd_His');
            $photoExtension = $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('public/admin/foto', $request->name . '_' . $timestamp . '.' . $photoExtension);
            $admin->photo = $photoPath;
        }

        $admin->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('admins')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function delete($id)
    {
        $admin = Admin::findOrFail($id);
        Storage::delete($admin->photo);
        $admin->delete();
        return redirect()->route('admins')->with('success', 'Data admin berhasil dihapus.');
    }
}
