<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    public function edit()
    {
        $student = Auth::user()->student;
        return view('student.profile.edit', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::user()->student;

        $request->validate([
            'name' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:255', // Diubah
            'birth_date' => 'nullable|date', // Diubah
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'religion' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'parent_address' => 'nullable|string',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        $student->update($request->all());

        if ($request->filled('name')) {
            $student->user->update([
                'name' => $request->name
            ]);
        }

        return back()->with('success', 'Biodata diri dan orang tua berhasil diperbarui!');
    }
}