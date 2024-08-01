<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InformationController extends Controller
{
    public function index()
    {
        $title = 'Informasi';
        $informations = Information::get();
        return view('admin.department-information.information.index', compact('title', 'informations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);
 
        Information::create([
            'description' => $request->description,
        ]);

        return redirect()->route('informations')->with('success', 'Informasi berhasil ditambahkan.');
    }

    public function findInformation($id)
    {
        $information = Information::findOrFail($id);

        return response()->json($information);
    }

    public function update(Request $request, $id)
    {
        $information = Information::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $information->update([
            'description' => $request->description,
        ]);

        return redirect()->route('informations')->with('success', 'Informasi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $information = Information::findOrFail($id);
        $information->delete();
        return redirect()->route('informations')->with('success', 'Informasi berhasil dihapus.');
    }
}
