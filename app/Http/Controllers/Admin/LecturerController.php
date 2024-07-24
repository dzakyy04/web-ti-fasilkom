<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lecturer;
use App\Models\Education;
use Illuminate\Http\Request;
use App\Models\ResearchField;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LecturerController extends Controller
{
    public function index()
    {
        $title = 'Dosen';
        $lecturers = Lecturer::with('educations')->get();

        return view('admin.lecturers.index', compact('title', 'lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:20|unique:lecturers,nip',
            'nidn' => 'nullable|string|max:20|unique:lecturers,nidn',
            'position' => 'nullable|string|max:255',
            'educations.*.degree' => 'required|string|max:255',
            'educations.*.institution' => 'required|string|max:255',
            'educations.*.major' => 'required|string|max:255',
            'research_fields.*' => 'required|string|max:255',
        ]);

        $photoExtension = $request->file('photo')->getClientOriginalExtension();
        $photoPath = $request->file('photo')->storeAs('public/dosen/foto', $request->nip . '.' . $photoExtension);


        $lecturer = Lecturer::create([
            'photo' => $photoPath,
            'name' => $request->name,
            'nip' => $request->nip,
            'nidn' => $request->nidn,
            'position' => $request->position,
        ]);

        foreach ($request->educations as $educationData) {
            Education::create([
                'lecturer_id' => $lecturer->id,
                'degree' => $educationData['degree'],
                'institution' => $educationData['institution'],
                'major' => $educationData['major'],
            ]);
        }

        foreach ($request->research_fields as $researchField) {
            $field = ResearchField::firstOrCreate(['name' => $researchField]);
            $lecturer->researchFields()->attach($field->id);
        }

        return redirect()->route('lecturers')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $lecturer = Lecturer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image',
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:20|unique:lecturers,nip,' . $id,
            'nidn' => 'nullable|string|max:20|unique:lecturers,nidn,' . $id,
            'position' => 'nullable|string|max:255',
            'educations.*.degree' => 'required|string|max:255',
            'educations.*.institution' => 'required|string|max:255',
            'educations.*.major' => 'required|string|max:255',
            'research_fields.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) {
                Storage::delete($lecturer->photo);
            }

            $photoExtension = $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('public/dosen/foto', $request->nip . '.' . $photoExtension);
            $lecturer->photo = $photoPath;
        }

        $lecturer->update([
            'name' => $request->name,
            'nip' => $request->nip,
            'nidn' => $request->nidn,
            'position' => $request->position,
        ]);

        // Update educations
        $lecturer->educations()->delete();
        foreach ($request->educations as $educationData) {
            Education::create([
                'lecturer_id' => $lecturer->id,
                'degree' => $educationData['degree'],
                'institution' => $educationData['institution'],
                'major' => $educationData['major'],
            ]);
        }

        // Update research fields
        $lecturer->researchFields()->detach();
        foreach ($request->research_fields as $researchField) {
            $field = ResearchField::firstOrCreate(['name' => $researchField]);
            $lecturer->researchFields()->attach($field->id);
        }

        return redirect()->route('lecturers')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function delete($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        Storage::delete($lecturer->photo);

        $lecturer->educations()->delete();
        $lecturer->researchFields()->detach();
        $lecturer->delete();
        return redirect()->route('lecturers')->with('success', 'Data dosen berhasil dihapus.');
    }

    public function findLecturer($id)
    {
        $lecturer = Lecturer::with('educations', 'researchFields')->findOrFail($id);

        return response()->json($lecturer);
    }
}
