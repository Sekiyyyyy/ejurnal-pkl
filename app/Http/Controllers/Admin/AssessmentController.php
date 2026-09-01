<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Major;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $majors = Major::orderBy('name')->get();
        $selectedMajorId = $request->major_id;
        
        $assessments = [];
        if ($selectedMajorId) {
            $assessments = Assessment::where('major_id', $selectedMajorId)
                                     ->orderBy('category')
                                     ->orderBy('order_number')
                                     ->get();
        }

        return view('admin.assessments.index', compact('majors', 'selectedMajorId', 'assessments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'major_id' => 'required|exists:majors,id',
            'category' => 'required|string',
            'name' => 'required|string',
            'order_number' => 'required|integer|min:1',
            'parent_id' => 'nullable|exists:assessments,id' // <-- Tambahan
        ]);

        Assessment::create($request->all());

        return back()->with('success', 'Kriteria penilaian berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $assessment = Assessment::findOrFail($id);
        return view('admin.assessments.edit', compact('assessment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string',
            'order_number' => 'required|integer|min:1',
            'parent_id' => 'nullable|exists:assessments,id' // <-- Tambahan
        ]);

        $assessment = Assessment::findOrFail($id);
        // Pastikan parent_id ikut di-update
        $assessment->update($request->only(['category', 'name', 'order_number', 'parent_id'])); 

        return redirect()->route('admin.assessments.index', ['major_id' => $assessment->major_id])
                         ->with('success', 'Kriteria penilaian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Assessment::findOrFail($id)->delete();
        return back()->with('success', 'Kriteria berhasil dihapus.');
    }
}